import serial
import requests
import json
import time
import threading
import os
from datetime import datetime

# ======== Load Config ========
try:
    with open("config.json", "r") as f:
        config = json.load(f)
except FileNotFoundError:
    print("ERROR: config.json not found!")
    exit(1)
except json.JSONDecodeError:
    print("ERROR: config.json is not valid JSON!")
    exit(1)

COM_PORT  = config.get("com_port",  "/dev/ttyUSB0")
BAUD_RATE = config.get("baud_rate", 9600)
API_URL   = config.get("api_url",   "http://localhost:8000/api/update-room")

print("=" * 60)
print("FIRE DETECTION SYSTEM - PYTHON BRIDGE")
print("=" * 60)
print(f"COM Port : {COM_PORT}")
print(f"Baud Rate: {BAUD_RATE}")
print(f"API URL  : {API_URL}")
print("=" * 60)

# ======== Check if port exists ========
if not os.path.exists(COM_PORT):
    print(f"ERROR: Port {COM_PORT} does not exist!")
    print("Available ports:")
    os.system("ls /dev/ttyUSB* /dev/ttyACM* 2>/dev/null")
    exit(1)

# ======== Connect to Arduino ========
try:
    print(f"Connecting to {COM_PORT} at {BAUD_RATE} baud...")
    arduino = serial.Serial(
        port=COM_PORT,
        baudrate=BAUD_RATE,
        timeout=2,
        bytesize=serial.EIGHTBITS,
        parity=serial.PARITY_NONE,
        stopbits=serial.STOPBITS_ONE
    )
    
    arduino.reset_input_buffer()
    arduino.reset_output_buffer()
    
    time.sleep(2)
    print(f"Connected to Arduino on {COM_PORT}")
    
except serial.SerialException as e:
    print(f"ERROR: Could not connect to {COM_PORT}")
    print(f"Details: {e}")
    print("\nTroubleshooting steps:")
    print("1. Check if Arduino is plugged in")
    print("2. Run: sudo chmod 666 " + COM_PORT)
    print("3. Run: sudo usermod -a -G dialout $USER")
    print("4. Log out and back in")
    exit(1)

# ======== Thresholds ========
TEMP_HIGH = 50.0
TEMP_LOW  = 45.0
GAS_HIGH  = 700
GAS_LOW   = 600

# ======== State Tracking ========
class RoomState:
    def __init__(self, room_type):
        self.status = "SAFE"
        self.reading = "--"
        self.last_status = "SAFE"
        self.last_reading = "--"
        self.last_sent_time = 0
        self.room_type = room_type
    
    def should_send(self, new_status, new_reading):
        current_time = time.time()
        
        if new_status != self.last_status:
            return True
        
        if new_status == "FIRE":
            if current_time - self.last_sent_time >= 0.3:
                return True
        else:
            if self.room_type == "temp" and isinstance(new_reading, (int, float)):
                try:
                    if abs(new_reading - float(self.last_reading)) > 0.5:
                        return True
                except:
                    return True
            elif self.room_type == "gas" and isinstance(new_reading, int):
                try:
                    if abs(new_reading - int(self.last_reading)) > 50:
                        return True
                except:
                    return True
            elif self.room_type == "flame":
                return new_status != self.last_status
            
            if current_time - self.last_sent_time >= 1.5:
                return True
        
        return False
    
    def update(self, new_status, new_reading):
        self.last_status = self.status
        self.last_reading = self.reading
        self.status = new_status
        self.reading = new_reading
        self.last_sent_time = time.time()

rooms = {
    "ROOM1": RoomState("temp"),
    "ROOM2": RoomState("gas"),
    "ROOM3": RoomState("flame")
}

# ======== Queue System ========
update_queue = []
queue_lock = threading.Lock()
last_send_time = 0

def send_updates():
    global last_send_time
    
    current_time = time.time()
    
    if current_time - last_send_time < 0.2:
        return
    
    with queue_lock:
        if not update_queue:
            return
        
        updates_to_send = update_queue.copy()
        update_queue.clear()
    
    for update in updates_to_send:
        try:
            response = requests.post(
                API_URL,
                json={
                    "room": update['room'],
                    "status": update['status'],
                    "reading": update['reading']
                },
                timeout=0.5
            )
        except Exception as e:
            pass
    
    last_send_time = current_time

def queue_update(room, status, reading):
    with queue_lock:
        update_queue.append({
            "room": room,
            "status": status,
            "reading": reading,
            "timestamp": time.time()
        })

def parse_line(line):
    parts = line.strip().split(",")
    if len(parts) != 4:
        return None
    try:
        temp = float(parts[0])
        gas = int(parts[1])
        flame = parts[2].strip().upper()
        alarm = parts[3].strip().upper()
        return temp, gas, flame, alarm
    except (ValueError, IndexError):
        return None

def determine_status(value, current_status, high, low):
    if value > high:
        return "FIRE"
    elif value < low:
        return "SAFE"
    else:
        return current_status

# ======== Main Loop ========
print("\nListening for Arduino data...")
print("Press Ctrl+C to stop\n")
print("-" * 60)

try:
    last_print_time = time.time()
    last_data_time = time.time()
    line_count = 0
    
    while True:
        if arduino.in_waiting > 0:
            try:
                raw = arduino.readline()
                
                try:
                    raw_str = raw.decode("utf-8", errors="ignore").strip()
                except:
                    raw_str = ""
                
                if raw_str:
                    line_count += 1
                    
                    if line_count <= 10 or line_count % 50 == 0:
                        print(f"[{line_count}] Received: {raw_str}")
                    
                    last_data_time = time.time()
                    
                    parsed = parse_line(raw_str)
                    if parsed is None:
                        if line_count <= 5:
                            print(f"  Parse failed - expected format: temp,gas,flame,alarm")
                        continue
                    
                    temp, gas, flame, alarm = parsed
                    
                    if line_count <= 10 or line_count % 50 == 0:
                        print(f"  Parsed: Temp={temp}C, Gas={gas}, Flame={flame}")
                    
                    temp_status = determine_status(
                        temp, rooms["ROOM1"].status, TEMP_HIGH, TEMP_LOW
                    )
                    if rooms["ROOM1"].should_send(temp_status, round(temp, 1)):
                        queue_update("ROOM1", temp_status, round(temp, 1))
                        rooms["ROOM1"].update(temp_status, round(temp, 1))
                    
                    gas_status = determine_status(
                        gas, rooms["ROOM2"].status, GAS_HIGH, GAS_LOW
                    )
                    if rooms["ROOM2"].should_send(gas_status, gas):
                        queue_update("ROOM2", gas_status, gas)
                        rooms["ROOM2"].update(gas_status, gas)
                    
                    flame_status = "FIRE" if flame == "DETECTED" else "SAFE"
                    if rooms["ROOM3"].should_send(flame_status, flame):
                        queue_update("ROOM3", flame_status, flame)
                        rooms["ROOM3"].update(flame_status, flame)
                    
                    current_time = time.time()
                    if current_time - last_print_time >= 5:
                        print(f"\n[{datetime.now().strftime('%H:%M:%S')}] Summary:")
                        print(f"  Temp: {temp}C | Gas: {gas} | Flame: {flame}")
                        for room_name, room in rooms.items():
                            status_text = "[FIRE]" if room.status == "FIRE" else "[SAFE]"
                            print(f"    {status_text} {room_name}: {room.status} | {room.reading}")
                        print("-" * 40)
                        last_print_time = current_time
                
            except Exception as e:
                print(f"Error: {e}")
        
        send_updates()
        
        current_time = time.time()
        if current_time - last_data_time > 5:
            print(f"Warning: No data from Arduino for {int(current_time - last_data_time)} seconds")
        
        time.sleep(0.05)

except KeyboardInterrupt:
    print("\n" + "=" * 60)
    print("Shutting down...")
    for room_name, room in rooms.items():
        try:
            requests.post(
                API_URL,
                json={"room": room_name, "status": "SAFE", "reading": "--"},
                timeout=1
            )
        except:
            pass
    arduino.close()
    print("Bridge stopped")
    print("=" * 60)

except Exception as e:
    print(f"\nFATAL ERROR: {e}")
    import traceback
    traceback.print_exc()
    try:
        arduino.close()
    except:
        pass