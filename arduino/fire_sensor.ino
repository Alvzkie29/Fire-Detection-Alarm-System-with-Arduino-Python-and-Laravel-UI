#include <Servo.h>

// ===== PIN SETUP =====
const int tempPin = A1;     // LM35 temperature sensor
const int gasPin  = A0;     // MQ-2 gas sensor
const int flamePin = 10;    // Flame sensor (LOW = fire)

// Buzzers
const int buzzer1 = 7;
const int buzzer2 = 8;
const int buzzer3 = 9;

// Servos
Servo servo1;
Servo servo2;
Servo servo3;
Servo servo4;
Servo servo5;

const int servo1Pin = 2;
const int servo2Pin = 3;
const int servo3Pin = 4;
const int servo4Pin = 5;  // reversed
const int servo5Pin = 6;

const int ledPin = 13;

// ===== THRESHOLDS =====
// Temperature
const float tempHigh = 50.0;  // trigger alarm
const float tempLow  = 45.0;  // clear alarm

// Gas
const int gasHigh = 1100;
const int gasLow  = 1000;

// ===== STATES =====
bool tempAlert = false;
bool gasAlert  = false;
bool flameAlert = false;

// Smoothed temperature value
float filteredTemp = 25.0;   // Start with safe ambient value

// ===== FUNCTIONS =====
float readTempC() {
  int raw = analogRead(tempPin);
  float voltage = raw * (5.0 / 1023.0);
  float tempC = voltage * 100.0;

  // Exponential smoothing
  filteredTemp = (filteredTemp * 0.9) + (tempC * 0.1);

  return filteredTemp;
}

int readGas() {
  int sum = 0;
  const int samples = 5;
  for (int i = 0; i < samples; i++) {
    sum += analogRead(gasPin);
    delay(5);
  }
  return sum / samples;
}

void moveServosOpen() {
  servo1.write(90);
  servo2.write(90);
  servo3.write(90);
  servo4.write(0);   // reversed
  servo5.write(90);
}

void moveServosClose() {
  servo1.write(0);
  servo2.write(0);
  servo3.write(0);
  servo4.write(90);  // reversed
  servo5.write(0);
}

void buzzAlarm(int times) {
  for (int i = 0; i < times; i++) {
    digitalWrite(buzzer1, HIGH);
    digitalWrite(buzzer2, HIGH);
    digitalWrite(buzzer3, HIGH);
    delay(200);
    digitalWrite(buzzer1, LOW);
    digitalWrite(buzzer2, LOW);
    digitalWrite(buzzer3, LOW);
    delay(200);
  }
}

// ===== SETUP =====
void setup() {
  Serial.begin(9600);

  pinMode(flamePin, INPUT);
  pinMode(buzzer1, OUTPUT);
  pinMode(buzzer2, OUTPUT);
  pinMode(buzzer3, OUTPUT);
  pinMode(ledPin, OUTPUT);

  servo1.attach(servo1Pin);
  servo2.attach(servo2Pin);
  servo3.attach(servo3Pin);
  servo4.attach(servo4Pin);
  servo5.attach(servo5Pin);

  moveServosClose();

  Serial.println("Fire Detection System Ready...");
}

// ===== LOOP =====
void loop() {
  float tempC = readTempC();
  int gasValue = readGas();
  int flameValue = digitalRead(flamePin);

  // ===== HYSTERESIS =====
  // Temperature
  if (tempC > tempHigh) tempAlert = true;
  else if (tempC < tempLow) tempAlert = false;

  // Gas
  if (gasValue > gasHigh) gasAlert = true;
  else if (gasValue < gasLow) gasAlert = false;

  // Flame
  flameAlert = (flameValue == LOW);

  // ===== SERIAL OUTPUT =====
  Serial.print("Temp: ");
  Serial.print(tempC);
  Serial.print(" °C, Gas: ");
  Serial.print(gasValue);
  Serial.print(", Flame: ");
  Serial.print(flameAlert ? "DETECTED" : "SAFE");
  Serial.print(" → ");

  bool alarm = tempAlert || gasAlert || flameAlert;

  Serial.println(alarm ? "ALARM!" : "SAFE");

  // ===== ALARM LOGIC =====
  if (alarm) {
    moveServosOpen();
    digitalWrite(ledPin, HIGH);
    buzzAlarm(3);
  } else {
    moveServosClose();
    digitalWrite(ledPin, LOW);
    digitalWrite(buzzer1, LOW);
    digitalWrite(buzzer2, LOW);
    digitalWrite(buzzer3, LOW);
  }

  delay(150);
}
