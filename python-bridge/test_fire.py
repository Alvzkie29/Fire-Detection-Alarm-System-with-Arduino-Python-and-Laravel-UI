import requests
import time

API_URL = "http://127.0.0.1:8000/api/update-room"

# Simulate fire in rooms
while True:
    # Room1 on fire
    requests.post(API_URL, data={"room":"Room1","status":"FIRE"})
    requests.post(API_URL, data={"room":"Room2","status":"SAFE"})
    requests.post(API_URL, data={"room":"Room3","status":"SAFE"})
    print("Room1 FIRE sent")
    time.sleep(3)

    # Room2 on fire
    requests.post(API_URL, data={"room":"Room1","status":"SAFE"})
    requests.post(API_URL, data={"room":"Room2","status":"FIRE"})
    requests.post(API_URL, data={"room":"Room3","status":"SAFE"})
    print("Room2 FIRE sent")
    time.sleep(3)

    # Room3 on fire
    requests.post(API_URL, data={"room":"Room1","status":"SAFE"})
    requests.post(API_URL, data={"room":"Room2","status":"SAFE"})
    requests.post(API_URL, data={"room":"Room3","status":"FIRE"})
    print("Room3 FIRE sent")
    time.sleep(3)
