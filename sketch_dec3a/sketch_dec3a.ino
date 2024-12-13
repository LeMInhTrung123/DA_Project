#include <WiFi.h>
#include <ArduinoWebsockets.h>

using namespace websockets;
WebsocketsClient socket;
const char* websocketServer = "ws://192.168.110.216:81/";
boolean connected = false;

const char* ssid = "SSID:	SakehomeLau3_5G";
const char* password = "sakehomelau3";

void setup() {
  Serial.begin(115200);
  connectWiFi();
  connectToWebSocket();
  socket.onMessage(handleMessage);
  socket.onEvent(handleEvent);
}

void loop() {
  // put your main code here, to run repeatedly:
  if(!connected){
      Serial.println("Connecting to WebSocket server");
      connectToWebSocket();
  }
  socket.poll();
}

void handleMessage(WebsocketsMessage message){
  Serial.println(message.data());
}

void handleEvent(WebsocketsEvent event, WSInterfaceString data){
  //TODO: implement lator on
}
void connectToWebSocket(){

  connected = socket.connect(websocketServer);
  if (connected){
    Serial.println("Connected");
  }
  else{
    Serial.println("Connection failed.");
  }
}
void connectWiFi()
{
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi");

  while (WiFi.status() != WL_CONNECTED){
    delay(500);
    Serial.print(".");
  }

  Serial.print("Connected to: "); Serial.println(ssid);
  Serial.print("IP address: "); Serial.println(WiFi.localIP());
}
