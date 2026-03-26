#include <Servo.h>

// ===== PIN SETUP =====
const int tempPin  = A1;    // LM35 temperature sensor
const int gasPin   = A0;    // MQ-2 gas sensor
const int flamePin = 10;    // Flame sensor (LOW = fire)

// Buzzers
const int buzzer1 = 7;
const int buzzer2 = 8;
const int buzzer3 = 9;

// Servos
Servo servo1, servo2, servo3, servo4, servo5;
const int servo1Pin = 2;
const int servo2Pin = 3;
const int servo3Pin = 4;
const int servo4Pin = 5;   // reversed
const int servo5Pin = 6;

const int ledPin = 13;

// ===== THRESHOLDS =====
const float tempHigh = 50.0;
const float tempLow  = 45.0;
const int   gasHigh  = 700;   // Fixed: was 1100, unreachable on 10-bit ADC
const int   gasLow   = 600;

// ===== STATES =====
bool tempAlert  = false;
bool gasAlert   = false;
bool flameAlert = false;

float filteredTemp = 25.0;

// ===== NON-BLOCKING BUZZER =====
unsigned long lastBuzzTime = 0;
bool buzzerState = false;
const int buzzOnTime  = 200;
const int buzzOffTime = 200;

// ===== FUNCTIONS =====

float readTempC() {
    int raw       = analogRead(tempPin);
    float voltage = raw * (5.0 / 1023.0);
    float tempC   = voltage * 100.0;
    filteredTemp  = (filteredTemp * 0.9) + (tempC * 0.1);
    return filteredTemp;
}

int readGas() {
    int sum = 0;
    for (int i = 0; i < 5; i++) {
        sum += analogRead(gasPin);
        delay(5);
    }
    return sum / 5;
}

void moveServosOpen() {
    servo1.write(90); servo2.write(90); servo3.write(90);
    servo4.write(0);  servo5.write(90);
}

void moveServosClose() {
    servo1.write(0); servo2.write(0); servo3.write(0);
    servo4.write(90); servo5.write(0);
}

void tickBuzzer(bool active) {
    if (!active) {
        digitalWrite(buzzer1, LOW);
        digitalWrite(buzzer2, LOW);
        digitalWrite(buzzer3, LOW);
        buzzerState = false;
        return;
    }
    unsigned long now = millis();
    if (buzzerState && now - lastBuzzTime >= buzzOnTime) {
        digitalWrite(buzzer1, LOW);
        digitalWrite(buzzer2, LOW);
        digitalWrite(buzzer3, LOW);
        buzzerState  = false;
        lastBuzzTime = now;
    } else if (!buzzerState && now - lastBuzzTime >= buzzOffTime) {
        digitalWrite(buzzer1, HIGH);
        digitalWrite(buzzer2, HIGH);
        digitalWrite(buzzer3, HIGH);
        buzzerState  = true;
        lastBuzzTime = now;
    }
}

void sendSerial(float tempC, int gasValue, bool flame, bool alarm) {
    Serial.print("Temp: ");
    Serial.print(tempC);
    Serial.print(", Gas: ");
    Serial.print(gasValue);
    Serial.print(", Flame: ");
    Serial.print(flame ? "DETECTED" : "SAFE");
    Serial.print(" -> ");
    Serial.println(alarm ? "ALARM!" : "SAFE");
    Serial.flush();  // Flush immediately to ensure data is sent
}

// ===== SETUP =====
void setup() {
    Serial.begin(9600);
    pinMode(flamePin,  INPUT_PULLUP);  // Fixed: prevents floating signal
    pinMode(buzzer1,   OUTPUT);
    pinMode(buzzer2,   OUTPUT);
    pinMode(buzzer3,   OUTPUT);
    pinMode(ledPin,    OUTPUT);

    servo1.attach(servo1Pin);
    servo2.attach(servo2Pin);
    servo3.attach(servo3Pin);
    servo4.attach(servo4Pin);
    servo5.attach(servo5Pin);

    moveServosClose();
    Serial.println("Fire Detection System Ready...");
    Serial.println("NOTE: Allow 30-60 seconds for MQ-2 to warm up.");
}

// ===== LOOP =====
void loop() {
    float tempC    = readTempC();
    int   gasValue = readGas();
    int   flameRaw = digitalRead(flamePin);

    // Hysteresis
    if (tempC > tempHigh)       tempAlert = true;
    else if (tempC < tempLow)   tempAlert = false;

    if (gasValue > gasHigh)     gasAlert  = true;
    else if (gasValue < gasLow) gasAlert  = false;

    flameAlert = (flameRaw == LOW);

    bool alarm = tempAlert || gasAlert || flameAlert;

    // Send serial immediately on every cycle
    sendSerial(tempC, gasValue, flameAlert, alarm);

    if (alarm) {
        moveServosOpen();
        digitalWrite(ledPin, HIGH);

        // =====================================================
        // ALARM LOOP — stays here until ALL sensors are clear
        // Sends serial every ~50ms so Python gets updates fast
        // Buzzer runs non-blocking so loop never freezes
        // =====================================================
        while (true) {
            tempC    = readTempC();
            gasValue = readGas();
            flameRaw = digitalRead(flamePin);

            // Re-evaluate with hysteresis
            if (tempC > tempHigh)       tempAlert  = true;
            else if (tempC < tempLow)   tempAlert  = false;

            if (gasValue > gasHigh)     gasAlert   = true;
            else if (gasValue < gasLow) gasAlert   = false;

            flameAlert = (flameRaw == LOW);

            alarm = tempAlert || gasAlert || flameAlert;

            tickBuzzer(true);

            // Keep sending serial rapidly during alarm
            sendSerial(tempC, gasValue, flameAlert, true);

            // Exit only when all sensors are clear
            if (!alarm) {
                tickBuzzer(false);
                moveServosClose();
                digitalWrite(ledPin, LOW);
                sendSerial(tempC, gasValue, false, false);
                break;
            }

            delay(50);
        }

    } else {
        moveServosClose();
        digitalWrite(ledPin, LOW);
        tickBuzzer(false);
        delay(150);
    }
}