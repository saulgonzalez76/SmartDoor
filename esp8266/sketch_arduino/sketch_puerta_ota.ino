
/*Copyright (C) 2018  
 * by: Saul Gonzalez
 * email: saulgonzalez76@gmail.com
 * 
 * This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>


    DESCRIPTION:
 * 
 * This script is for making a smart door, controls any electric controlled access lock. go to bitbucket https://saulgonzalez@bitbucket.org/saulgonzalez
 * Use an esp8266 module to read qr scanner and send data to web service, then recive 'ok' to open the relay for 'n' seconds, all managed by a webservice.
 * 
 * 
 * 
*/
#include <ESP8266WebServer.h>
#include <WiFiManager.h>         
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <SoftwareSerial.h>

#include <ESP8266HTTPClient.h>
#include <ESP8266httpUpdate.h>

const int FW_VERSION = 10;   //<--------------- IMPORTANT: MODIFY THIS VERSION NUMBER AFTER ANY CHANGES, COMPILE AND THEN FTP THE .BIN FIRMWARE TO WEBSERVICE FOR AUTOUPDATE  
const char* firmwareURL = "http://example.com"; // do not use https, it only works with http
String url = "http://example.com"; // do not use https, it only works with http
String idesp = "";
long hora = millis();
const int tiempoUpdateCheck = 3600000; // time for each firmware version upgrade check, set to one hour ( sec * 1000 )
WiFiServer server(80);

// Variable to store the HTTP request
String header;

// Auxiliar variables to store the current output state
String output5State = "off";
String tmpkey = "6A4946F2B25FF41C952F3EC7EEA1D";

// Assign output variables to GPIO pins
//const int output5 = 2;
//const int pinrelay = 0;

void checkForUpdates() {
//  Serial.println( "Checando updates de firmware." );
//  Serial.println( firmwareURL );
//  Serial.println( idesp );
  WiFiClient client;
  HTTPClient http;
  http.begin(client, firmwareURL); //HTTP
  http.addHeader("Content-Type", "application/json");
  int httpCode = http.POST("{\"id\":\"" + idesp + "\",\"version\":\"" + FW_VERSION + "\"}");
//  Serial.printf("[HTTP] POST... code: %d\n", httpCode);
  if (httpCode > 0) {
    if (httpCode == HTTP_CODE_OK) {
      const String& payload = http.getString();
      String urlFIRMWARE = payload;      
      urlFIRMWARE.replace("\n","");
//      Serial.println(payload);
      if (urlFIRMWARE != "") {
//        Serial.println( "Preparando update" );  
        t_httpUpdate_return ret = ESPhttpUpdate.update( urlFIRMWARE );
        switch(ret) {
          case HTTP_UPDATE_FAILED:
//            Serial.printf("HTTP_UPDATE_FAILD Error (%d): %s", ESPhttpUpdate.getLastError(), ESPhttpUpdate.getLastErrorString().c_str());
            break;
          case HTTP_UPDATE_NO_UPDATES:
//            Serial.println("HTTP_UPDATE_NO_UPDATES");
            break;
        }
      }
    }
  } 
  http.end();
}

String getValue(String data, char separator, int index)
{
    int found = 0;
    int strIndex[] = { 0, -1 };
    int maxIndex = data.length() - 1;

    for (int i = 0; i <= maxIndex && found <= index; i++) {
        if (data.charAt(i) == separator || i == maxIndex) {
            found++;
            strIndex[0] = strIndex[1] + 1;
            strIndex[1] = (i == maxIndex) ? i+1 : i;
        }
    }
    return found > index ? data.substring(strIndex[0], strIndex[1]) : "";
}

void setup() {
  Serial.begin(115200);

  idesp = WiFi.macAddress();
  // Initialize the output variables as outputs
//  pinMode(output5, OUTPUT);
  pinMode(0, OUTPUT);
  pinMode(1, OUTPUT);
  pinMode(2, OUTPUT);
  // Set outputs to LOW
//  digitalWrite(output5, LOW);
  digitalWrite(0, HIGH);  
  digitalWrite(1, HIGH);
  digitalWrite(2, HIGH);  

  // WiFiManager
  // Local intialization. Once its business is done, there is no need to keep it around
  WiFiManager wifiManager;
  
  // Uncomment and run it once, if you want to erase all the stored information
  //wifiManager.resetSettings();
  
  // set custom ip for portal
  //wifiManager.setAPConfig(IPAddress(10,0,1,1), IPAddress(10,0,1,1), IPAddress(255,255,255,0));

  // fetches ssid and pass from eeprom and tries to connect
  // if it does not connect it starts an access point with the specified name
  // here  "AutoConnectAP"
  // and goes into a blocking loop awaiting configuration
  wifiManager.autoConnect("intelliDoor", "intelliDoor");
  // or use this for auto generated name ESP + ChipID
  //wifiManager.autoConnect();
  
  // if you get here you have connected to the WiFi
  Serial.println("Connected.");
  Serial.println(idesp);
  //server.begin();
}

void loop(){
  if ((WiFi.status() == WL_CONNECTED)) {
    WiFiClient client;
    HTTPClient http;
      if (Serial.available()) {
        byte incomingData;
        String data = "codigo=";
        while(Serial.available() > 0) {
              incomingData = Serial.read();
              data = data + " " + String(incomingData);
              delay(50);
          }
        http.begin(client, url); //HTTP
        http.addHeader("Content-Type", "application/json");
        int httpCode = http.POST("{\"codigo\":\"" + data + "\",\"id\":\"" + idesp + "\"}");
        if (httpCode > 0) {
          if (httpCode == HTTP_CODE_OK) {
            const String& payload = http.getString();
            String strdatos = payload;
            strdatos.replace("\n","");
            if (strdatos != ""){
              int tiempo = getValue(strdatos, ';', 1).toInt();
              int activar_pin = getValue(strdatos, ';', 0).toInt();
              digitalWrite(activar_pin, LOW);
              delay(tiempo);
              digitalWrite(activar_pin, HIGH);
            }
          }
        } 
        http.end();
      }      
    }
    if ((millis() - hora) > tiempoUpdateCheck){ 
      checkForUpdates();
      hora = millis();
    }
}
