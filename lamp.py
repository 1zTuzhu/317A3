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

def selections():
    s.show_message("Set " + selection_list[selection],speed)
    
def scroll_selections():
    if selection_list[selection] == "day":
        s.show_message("Day: " + str(day), scroll_speed=speed)
    elif selection_list[selection] == "month":
        s.show_message("Month: " + str(month), scroll_speed=speed)
    elif selection_list[selection] == "site":
        s.show_message("Site: " + str(site_id), scroll_speed=speed)
        
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
                    
    

        
