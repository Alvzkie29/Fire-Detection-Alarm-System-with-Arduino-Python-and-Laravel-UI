import serial
import requests
import json
import time
import re

# Load configuration
try:
    with open("config.json", "r") as f:
        config = json.load(f)
except FileNotFoundError:
    print("❌ Error: config.json not found!")
    exit(1)
except json.JSONDecodeError:
    print("❌ Error: config.json is not valid JSON!")
    exit(1)

print("=" * 60)
print("🔥 FIRE DETECTION SYSTEM - ARDUINO TO LARAVEL BRIDGE")
print("=" * 60)
print(f"📡 COM Port: {config.get('com_port', 'COM5')}")
print(f"⚡ Baud Rate: {config.get('baud_rate', 9600)}")
print(f"🌐 API URL: {config.get('api_url', 'http://localhost:8000/api/update-room')}")
print("=" * 60)

# ======== Connect to Arduino ========
try:
    arduino = serial.Serial(config["com_port"], config["baud_rate"], timeout=1)
    time.sleep(2)  # Wait for Arduino to initialize
    print(f"✅ Connected to Arduino on {config['com_port']} at {config['baud_rate']} baud")
except serial.SerialException as e:
    print(f"❌ Error: Could not connect to {config['com_port']}")
    print(f"   Details: {e}")
    print("\n💡 Troubleshooting tips:")
    print("   1. Check if Arduino is connected")
    print("   2. Check COM port in Device Manager")
    print("   3. Close Arduino IDE Serial Monitor")
    exit(1)

# ======== Track last known states ========
last_states = {
    "ROOM1": "SAFE",
    "ROOM2": "SAFE",
    "ROOM3": "SAFE"
}

# ======== Helper Functions ========
def send_to_laravel(room, status):
    """Send room status to Laravel API"""
    try:
        print(f"📤 Sending: {room} -> {status}")
        response = requests.post(
            config["api_url"],
            json={"room": room, "status": status},
            timeout=3
        )
        
        if response.status_code == 200:
            print(f"✅ Success: {room} updated to {status}")
            return True
        else:
            print(f"❌ Failed: HTTP {response.status_code} - {response.text}")
            return False
            
    except requests.exceptions.ConnectionError:
        print("❌ Cannot connect to Laravel. Is the server running?")
        print("   Run: php artisan serve")
        return False
    except requests.exceptions.Timeout:
        print("❌ Request timeout")
        return False
    except Exception as e:
        print(f"❌ Unexpected error: {e}")
        return False

def parse_arduino_data(line):
    """Parse Arduino serial data"""
    data = {
        'temp': None,
        'gas': None,
        'flame': None,
        'alarm': None
    }
    
    # Parse temperature (e.g., "Temp: 25.5 °C")
    temp_match = re.search(r"Temp:\s*([0-9]+(?:\.[0-9]+)?)", line, re.IGNORECASE)
    if temp_match:
        try:
            data['temp'] = float(temp_match.group(1))
        except ValueError:
            pass
    
    # Parse gas (e.g., "Gas: 850")
    gas_match = re.search(r"Gas:\s*(\d+)", line, re.IGNORECASE)
    if gas_match:
        try:
            data['gas'] = int(gas_match.group(1))
        except ValueError:
            pass
    
    # Parse flame status (e.g., "Flame: SAFE" or "Flame: DETECTED")
    flame_match = re.search(r"Flame:\s*(DETECTED|SAFE)", line, re.IGNORECASE)
    if flame_match:
        data['flame'] = flame_match.group(1).upper()
    
    # Parse overall alarm status (e.g., "→ SAFE" or "→ ALARM!")
    alarm_match = re.search(r"→\s*(SAFE|ALARM!)", line)
    if alarm_match:
        data['alarm'] = alarm_match.group(1)
    
    return data

def update_room_status(room, new_status):
    """Update room status if changed"""
    if last_states.get(room) != new_status:
        if send_to_laravel(room, new_status):
            last_states[room] = new_status
            return True
    return False

print("\n🔄 Listening for Arduino data...")
print("Press Ctrl+C to stop")
print("-" * 60)

try:
    while True:
        if arduino.in_waiting:
            try:
                # Read line from Arduino
                line = arduino.readline().decode('utf-8', errors='ignore').strip()
                
                if not line:
                    continue
                
                print(f"\n📨 Arduino: {line}")
                
                # Parse the data
                data = parse_arduino_data(line)
                
                # Process Room 1 (Temperature)
                if data['temp'] is not None:
                    temp = data['temp']
                    print(f"🌡️  Temperature: {temp}°C")
                    
                    # Apply Arduino's hysteresis logic
                    if temp > 50.0:  # tempHigh
                        update_room_status("ROOM1", "FIRE")
                    elif temp < 45.0:  # tempLow
                        update_room_status("ROOM1", "SAFE")
                    # If between 45-50°C, keep previous state
                
                # Process Room 2 (Gas)
                if data['gas'] is not None:
                    gas = data['gas']
                    print(f"💨 Gas Level: {gas} PPM")
                    
                    # Apply Arduino's hysteresis logic
                    if gas > 1100:  # gasHigh
                        update_room_status("ROOM2", "FIRE")
                    elif gas < 1000:  # gasLow
                        update_room_status("ROOM2", "SAFE")
                    # If between 1000-1100 PPM, keep previous state
                
                # Process Room 3 (Flame)
                if data['flame'] is not None:
                    flame_status = data['flame']
                    print(f"🔥 Flame Status: {flame_status}")
                    
                    new_status = "FIRE" if flame_status == "DETECTED" else "SAFE"
                    update_room_status("ROOM3", new_status)
                
                # If Arduino says ALARM! but we missed it, force FIRE status
                if data['alarm'] == "ALARM!":
                    print("🚨 Arduino reports ALARM!")
                    # Check if any room should be FIRE but isn't
                    for room in ["ROOM1", "ROOM2", "ROOM3"]:
                        if last_states.get(room) != "FIRE":
                            # If temperature, gas, or flame suggest FIRE but we missed it
                            if (room == "ROOM1" and data['temp'] and data['temp'] > 50.0) or \
                               (room == "ROOM2" and data['gas'] and data['gas'] > 1100) or \
                               (room == "ROOM3" and data['flame'] == "DETECTED"):
                                update_room_status(room, "FIRE")
                
                # Print current status
                print("\n📊 Current Status:")
                for room in ["ROOM1", "ROOM2", "ROOM3"]:
                    status = last_states.get(room, "UNKNOWN")
                    icon = "🔥" if status == "FIRE" else "✅" if status == "SAFE" else "❓"
                    print(f"  {icon} {room}: {status}")
                
                print("-" * 40)
                
            except UnicodeDecodeError:
                print("⚠️  Could not decode Arduino data")
            except Exception as e:
                print(f"⚠️  Error processing data: {e}")
        
        time.sleep(0.1)  # Small delay to prevent CPU overuse

except KeyboardInterrupt:
    print("\n\n" + "=" * 60)
    print("🛑 Shutting down...")
    
    # Send SAFE status to all rooms before exiting
    print("📤 Setting all rooms to SAFE before exit...")
    for room in ["ROOM1", "ROOM2", "ROOM3"]:
        send_to_laravel(room, "SAFE")
    
    # Close serial connection
    try:
        arduino.close()
        print("✅ Serial connection closed")
    except:
        print("⚠️  Could not close serial connection")
    
    print("\n👋 Bridge stopped successfully")
    print("=" * 60)

except Exception as e:
    print(f"\n❌ Fatal error: {e}")
    try:
        arduino.close()
    except:
        pass
    print("❌ Bridge crashed")