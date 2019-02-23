# How does it work

## Server side
* Gateway loop ``
  * Reads usb port byte by byte
  * As soon as a message is fully received (\n character detected) it triggers a new event and continue reading serial port
  * Event parse message and query additionnal datas on node, sensor and message type from db
  * When a sensor's value is modified it trigger a push message to websocket so the gui can be updated

* Socket loop ``
  * InputTopic : monithome/input (channel)
    * Is the request/response channel
    * Receive message from GUI
    * Determine and apply correct controller and execute given action
    * Eventually send a Response
  * PushTopic : monithome/push channel
    * Responsible in pushing publication from server to client

## Gui side

* Client subscribe to channels
  * As soon as a message receive it update vuejs components
  * Communicate with monithome/input via websockets
