from sense_emu import SenseHat
import requests
import time

s = SenseHat()
s.clear()
server_url = 'http://iotserver.com/logger.php'

day = 1
month = 1
site_id = 91107
mode = "normal"
selection = 0
speed = 0.05

selection_list = ["day","month","site"]
site_list = [91107,91237,91292,94029,94212]

predict_temp = (0,1)
predict_humidity = (0,1)
current_type = "temp"
predicted = False

def selections():
    s.show_message("Set " + selection_list[selection],speed)
    
def scroll_selections():
    if selection_list[selection] == "day":
        s.show_message("Day: " + str(day), scroll_speed=speed)
    elif selection_list[selection] == "month":
        s.show_message("Month: " + str(month), scroll_speed=speed)
    elif selection_list[selection] == "site":
        s.show_message("Site: " + str(site_id), scroll_speed=speed)

def prediction():
    global predict_temp, predict_humidity,predicted
    r = requests.get("http://iotserver.com/prediction.php")
    data = r.json()
    
    predict_temp = (float(data["min_temp"]), float(data["max_temp"]))
    predict_humidity = (float(data["min_humidity"]), float(data["max_humidity"]))
    predicted = True

    print("Prediction:")
    print("Site:", data["site_name"])
    print("Date:", data["date"])
    print("Temp:","min:", data["min_temp"], " max: ", data["max_temp"])
    print("Humidity:","min:", data["min_humidity"], " max:", data["max_humidity"])
        
def send_to_server():
    payload = {
        "day": str(day),
        "month": str(month),
        "year" : "2022",
        "site": str(site_id)
    }
    r = requests.get(server_url, params=payload)
    print("Update date successful")
    if "Write Successful" not in r.text:
        print("data upload failed")
    prediction()
        
s.show_message("Mode: Normal",scroll_speed = speed)
while True:
    for event in s.stick.get_events():
        if event.action == "pressed":
            #middle button pressed, check mode status
            if event.direction == "middle":                
                if mode == "normal":
                    mode = "setup"
                    s.show_message("Mode: Setup", scroll_speed=speed)
                else:
                    mode = "normal"
                    print(f"Date: {day}/{month}/2022  SiteID: {site_id}")
                    s.show_message("Getting prediction", scroll_speed=speed)
                    s.show_message("Mode: Normal", scroll_speed=speed)                    
                    send_to_server()
            #setup mode
            elif mode == "setup":
                if event.direction == "left":                
                    selection = (selection - 1) % 3
                    selections()
                elif event.direction == "right":
                    selection = (selection + 1) % 3
                    selections()
                #change value
                elif event.direction == "up":
                    if selection_list[selection] == "day":
                        day = (day % 31) + 1
                    elif selection_list[selection] == "month":
                        month = (month % 12) + 1
                    elif selection_list[selection] == "site":
                        index = site_list.index(site_id)
                        site_id = site_list[(index + 1) % 5]
                    scroll_selections()
                elif event.direction == "down":
                    if selection_list[selection] == "day":
                        if day == 1:
                            day = 31
                        else:
                            day = day - 1
                    elif selection_list[selection] == "month":
                        if month == 1:
                            month = 12
                        else:
                            month = month - 1
                    elif selection_list[selection] == "site":
                        index = site_list.index(site_id)
                        site_id = site_list[(index - 1) % 5]
                    scroll_selections()
            elif mode == "normal":
                if event.direction == "left":
                    current_type = "humidity"
                    s.show_message("Humidity",scroll_speed=speed)
                elif event.direction == "right":
                    current_type = "temp"
                    s.show_message("Temp",scroll_speed = speed)
                else:
                    current_type = "temp"
                 
    if mode == "normal" and predicted:
        temperature = s.get_temperature()
        humidity = s.get_humidity()

        if current_type == "temp":
            if temperature < predict_temp[0] or temperature > predict_temp[1]:
                s.clear(255, 0, 0)
            else:
                s.clear(0, 255, 0)
        elif current_type == "humidity":
            if humidity < predict_humidity[0] or humidity > predict_humidity[1]:
                s.clear(255, 255, 0)
            else:
                s.clear(0, 0, 255)
                
                    
    

        


