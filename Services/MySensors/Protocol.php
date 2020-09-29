<?php

namespace Extrablind\MonitHomeBundle\Services\MySensors;

class Protocol
{
    const COMMANDS = [
        0 => 'presentation', // (Sent by a node when they present attached sensors. This is usually done in the presentation() function which runs at startup.)
        1 => 'set', // (This message is sent from or to a sensor when a sensor value should be updated)
        2 => 'req', // (Requests a variable value (usually from an actuator destined for controller).)
        3 => 'internal', // (This is a special internal message. See table below for the details)
        4 => 'stream', // (Used for OTA firmware updates)
    ];

    const TYPES = [
        'presentation' => [
            0  => 'S_DOOR', // (Door and window sensors) -- (Used in : V_TRIPPED, V_ARMED)
            1  => 'S_MOTION', // (Motion sensors) -- (Used in : V_TRIPPED, V_ARMED)
            2  => 'S_SMOKE', // (Smoke sensor) -- (Used in : V_TRIPPED, V_ARMED)
            3  => 'S_BINARY', // (Binary device (on/off)) -- (Used in : V_STATUS, V_WATT)
            4  => 'S_DIMMER', // (Dimmable device of some kind) -- (Used in : V_STATUS (on/off), V_PERCENTAGE (dimmer level 0-100), V_WATT)
            5  => 'S_COVER', // (Window covers or shades) -- (Used in : V_UP, V_DOWN, V_STOP, V_PERCENTAGE)
            6  => 'S_TEMP', // (Temperature sensor) -- (Used in : V_TEMP, V_ID)
            7  => 'S_HUM', // (Humidity sensor) -- (Used in : V_HUM)
            8  => 'S_BARO', // (Barometer sensor (Pressure)) -- (Used in : V_PRESSURE, V_FORECAST)
            9  => 'S_WIND', // (Wind sensor) -- (Used in : V_WIND, V_GUST, V_DIRECTION)
            10 => 'S_RAIN', // (Rain sensor) -- (Used in : V_RAIN, V_RAINRATE)
            11 => 'S_UV', // (UV sensor) -- (Used in : V_UV)
            12 => 'S_WEIGHT', // (Weight sensor for scales etc.) -- (Used in : V_WEIGHT, V_IMPEDANCE)
            13 => 'S_POWER', // (Power measuring device, like power meters) -- (Used in : V_WATT, V_KWH, V_VAR, V_VA, V_POWER_FACTOR)
            14 => 'S_HEATER', // (Heater device) -- (Used in : V_HVAC_SETPOINT_HEAT, V_HVAC_FLOW_STATE, V_TEMP, V_STATUS)
            15 => 'S_DISTANCE', // (Distance sensor) -- (Used in : V_DISTANCE, V_UNIT_PREFIX)
            16 => 'S_LIGHT_LEVEL', // (Light sensor) -- (Used in : V_LIGHT_LEVEL (uncalibrated percentage), V_LEVEL (light level in lux))
            17 => 'S_ARDUINO_NODE', 	// 17 	Arduino node device
            18 => 'S_ARDUINO_REPEATER_NODE', // 	18 	Arduino repeating node device
            19 => 'S_LOCK', // (Lock device) -- (Used in : V_LOCK_STATUS)
            20 => 'S_IR', // (Ir sender/receiver device) -- (Used in : V_IR_SEND, V_IR_RECEIVE, V_IR_RECORD)
            21 => 'S_WATER', // (Water meter) -- (Used in : V_FLOW, V_VOLUME)
            22 => 'S_AIR_QUALITY', // (Air quality sensor e.g. MQ-2) -- (Used in : V_LEVEL, V_UNIT_PREFIX)
            23 => 'S_CUSTOM', //Use this for custom sensors where no other fits.
            24 => 'S_DUST', // (Dust level sensor) -- (Used in : V_LEVEL, V_UNIT_PREFIX)
            25 => 'S_SCENE_CONTROLLER', // (Scene controller device) -- (Used in : V_SCENE_ON, V_SCENE_OFF)
            26 => 'S_RGB_LIGHT', // (RGB light) -- (Used in : V_RGB, V_WATT)
            27 => 'S_RGBW_LIGHT', // (RGBW light (with separate white component)) -- (Used in : V_RGBW, V_WATT)
            28 => 'S_COLOR_SENSOR', // (Color sensor) -- (Used in : V_RGB)
            29 => 'S_HVAC', // (Thermostat/HVAC device) -- (Used in : V_STATUS, V_TEMP, V_HVAC_SETPOINT_HEAT, V_HVAC_SETPOINT_COOL, V_HVAC_FLOW_STATE, V_HVAC_FLOW_MODE, V_HVAC_SPEED)
            30 => 'S_MULTIMETER', // (Multimeter device) -- (Used in : V_VOLTAGE, V_CURRENT, V_IMPEDANCE)
            31 => 'S_SPRINKLER', // (Sprinkler device) -- (Used in : V_STATUS (turn on/off), V_TRIPPED (if fire detecting device))
            32 => 'S_WATER_LEAK', // (Water leak sensor) -- (Used in : V_TRIPPED, V_ARMED)
            33 => 'S_SOUND', // (Sound sensor) -- (Used in : V_LEVEL (in dB), V_TRIPPED, V_ARMED)
            34 => 'S_VIBRATION', // (Vibration sensor) -- (Used in : V_LEVEL (vibration in Hz), V_TRIPPED, V_ARMED)
            35 => 'S_MOISTURE', // (Moisture sensor) -- (Used in : V_LEVEL (water content or moisture in percentage?), V_TRIPPED, V_ARMED)
            36 => 'S_INFO', // (LCD text device) -- (Used in : V_TEXT)
            37 => 'S_GAS', // (Gas meter) -- (Used in : V_FLOW, V_VOLUME)
            38 => 'S_GPS', // (GPS Sensor) -- (Used in : V_POSITION)
            39 => 'S_WATER_QUALITY', // (Water quality sensor) -- (Used in : V_TEMP, V_PH, V_ORP, V_EC, V_STATUS)
        ],
        'subtypes' => [
            0  => 'V_TEMP', // (Temperature 	S_TEMP, S_HEATER, S_HVAC, S_WATER_QUALITY)
            1  => 'V_HUM', // (Humidity 	S_HUM)
            2  => 'V_STATUS', // (Binary status. 0=off 1=on 	S_BINARY, S_DIMMER, S_SPRINKLER, S_HVAC, S_HEATER, S_WATER_QUALITY)
            3  => 'V_PERCENTAGE', // (Percentage value. 0-100 (%) 	S_DIMMER, S_COVER)
            4  => 'V_PRESSURE', // (Atmospheric Pressure 	S_BARO)
            5  => 'V_FORECAST', // (Whether forecast. One of "stable", "sunny", "cloudy", "unstable", "thunderstorm" or "unknown" 	S_BARO)
            6  => 'V_RAIN', // (Amount of rain 	S_RAIN)
            7  => 'V_RAINRATE', // (Rate of rain 	S_RAIN)
            8  => 'V_WIND', // (Windspeed 	S_WIND)
            9  => 'V_GUST', // (Gust 	S_WIND)
            10 => 'V_DIRECTION', // (Wind direction 0-360 (degrees) 	S_WIND)
            11 => 'V_UV', // (UV light level 	S_UV)
            12 => 'V_WEIGHT', // (Weight (for scales etc) 	S_WEIGHT)
            13 => 'V_DISTANCE', // (Distance 	S_DISTANCE)
            14 => 'V_IMPEDANCE', // (Impedance value 	S_MULTIMETER, S_WEIGHT)
            15 => 'V_ARMED', // (Armed status of a security sensor. 1=Armed, 0=Bypassed 	S_DOOR, S_MOTION, S_SMOKE, S_SPRINKLER, S_WATER_LEAK, S_SOUND, S_VIBRATION, S_MOISTURE)
            16 => 'V_TRIPPED', // (Tripped status of a security sensor. 1=Tripped, 0=Untripped 	S_DOOR, S_MOTION, S_SMOKE, S_SPRINKLER, S_WATER_LEAK, S_SOUND, S_VIBRATION, S_MOISTURE)
            17 => 'V_WATT', // (Watt value for power meters 	S_POWER, S_BINARY, S_DIMMER, S_RGB_LIGHT, S_RGBW_LIGHT)
            18 => 'V_KWH', // (Accumulated number of KWH for a power meter 	S_POWER)
            19 => 'V_SCENE_ON', // (Turn on a scene 	S_SCENE_CONTROLLER)
            20 => 'V_SCENE_OFF', // (Turn of a scene 	S_SCENE_CONTROLLER)
            21 => 'V_HVAC_FLOW_STATE', // (Mode of header. One of "Off", "HeatOn", "CoolOn", or "AutoChangeOver" 	S_HVAC, S_HEATER)
            22 => 'V_HVAC_SPEED', // (HVAC/Heater fan speed ("Min", "Normal", "Max", "Auto") 	S_HVAC, S_HEATER)
            23 => 'V_LIGHT_LEVEL', // (Uncalibrated light level. 0-100%. Use V_LEVEL for light level in lux. 	S_LIGHT_LEVEL)
            24 => 'V_VAR1', // (Custom value 	Any device)
            25 => 'V_VAR2', // (Custom value 	Any device)
            26 => 'V_VAR3', // (Custom value 	Any device)
            27 => 'V_VAR4', // (Custom value 	Any device)
            28 => 'V_VAR5', // (Custom value 	Any device)
            29 => 'V_UP', // (Window covering. Up. 	S_COVER)
            30 => 'V_DOWN', // (Window covering. Down. 	S_COVER)
            31 => 'V_STOP', // (Window covering. Stop. 	S_COVER)
            32 => 'V_IR_SEND', // (Send out an IR-command 	S_IR)
            33 => 'V_IR_RECEIVE', // (This message contains a received IR-command 	S_IR)
            34 => 'V_FLOW', // (Flow of water (in meter) 	S_WATER)
            35 => 'V_VOLUME', // (Water volume 	S_WATER)
            36 => 'V_LOCK_STATUS', // (Set or get lock status. 1=Locked, 0=Unlocked 	S_LOCK)
            37 => 'V_LEVEL', // (Used for sending level-value 	S_DUST, S_AIR_QUALITY, S_SOUND (dB), S_VIBRATION (hz), S_LIGHT_LEVEL (lux))
            38 => 'V_VOLTAGE', // (Voltage level 	S_MULTIMETER)
            39 => 'V_CURRENT', // (Current level 	S_MULTIMETER)
            40 => 'V_RGB', // (RGB value transmitted as ASCII hex string (I.e "ff0000" for red) 	S_RGB_LIGHT, S_COLOR_SENSOR)
            41 => 'V_RGBW', // (RGBW value transmitted as ASCII hex string (I.e "ff0000ff" for red + full white) 	S_RGBW_LIGHT)
            42 => 'V_ID', // (Optional unique sensor id (e.g. OneWire DS1820b ids) 	S_TEMP)
            43 => 'V_UNIT_PREFIX', // (Allows sensors to send in a string representing the unit prefix to be displayed in GUI. This is not parsed by controller! E.g. cm, m, km, inch. 	S_DISTANCE, S_DUST, S_AIR_QUALITY)
            44 => 'V_HVAC_SETPOINT_COOL', // (HVAC cold setpoint 	S_HVAC)
            45 => 'V_HVAC_SETPOINT_HEAT', // (HVAC/Heater setpoint 	S_HVAC, S_HEATER)
            46 => 'V_HVAC_FLOW_MODE', // (Flow mode for HVAC ("Auto", "ContinuousOn", "PeriodicOn") 	S_HVAC)
            47 => 'V_TEXT', // (Text message to display on LCD or controller device 	S_INFO)
            48 => 'V_CUSTOM', // (Custom messages used for controller/inter node specific commands, preferably using S_CUSTOM device type. 	S_CUSTOM)
            49 => 'V_POSITION', // (GPS position and altitude. Payload: latitude;longitude;altitude(m). E.g. "55.722526;13.017972;18" 	S_GPS)
            50 => 'V_IR_RECORD', // (Record IR codes S_IR for playback 	S_IR)
            51 => 'V_PH', // (Water PH 	S_WATER_QUALITY)
            52 => 'V_ORP', // (Water ORP : redox potential in mV 	S_WATER_QUALITY)
            53 => 'V_EC', // (Water electric conductivity μS/cm (microSiemens/cm) 	S_WATER_QUALITY)
            54 => 'V_VAR', // (Reactive power: volt-ampere reactive (var) 	S_POWER)
            55 => 'V_VA', // (Apparent power: volt-ampere (VA) 	S_POWER)
            56 => 'V_POWER_FACTOR', // (Ratio of real power to apparent power: floating point value in the range [-1,..,1] 	S_POWER)
        ],
        'internal' => [
            0  => 'I_BATTERY_LEVEL', // (Use this to report the battery level (in percent 0-100).)
            1  => 'I_TIME', // (Sensors can request the current time from the Controller using this message. The time will be reported as the seconds since 1970)
            2  => 'I_VERSION', // (Used to request gateway version from controller.)
            3  => 'I_ID_REQUEST', // (Use this to request a unique node id from the controller.)
            4  => 'I_ID_RESPONSE', // (Id response back to node. Payload contains node id.)
            5  => 'I_INCLUSION_MODE', // (Start/stop inclusion mode of the Controller (1=start, 0=stop).)
            6  => 'I_CONFIG', // (Config request from node. Reply with (M)etric or (I)mperal back to sensor.)
            7  => 'I_FIND_PARENT', // (When a sensor starts up, it broadcast a search request to all neighbor nodes. They reply with a I_FIND_PARENT_RESPONSE.)
            8  => 'I_FIND_PARENT_RESPONSE', // (Reply message type to I_FIND_PARENT request.)
            9  => 'I_LOG_MESSAGE', // (Sent by the gateway to the Controller to trace-log a message)
            10 => 'I_CHILDREN', // (A message that can be used to transfer child sensors (from EEPROM routing table) of a repeating node.)
            11 => 'I_SKETCH_NAME', // (Optional sketch name that can be used to identify sensor in the Controller GUI)
            12 => 'I_SKETCH_VERSION', // (Optional sketch version that can be reported to keep track of the version of sensor in the Controller GUI.)
            13 => 'I_REBOOT', // (Used by OTA firmware updates. Request for node to reboot.)
            14 => 'I_GATEWAY_READY', // (Send by gateway to controller when startup is complete.)
            15 => 'I_SIGNING_PRESENTATION', // (Provides signing related preferences (first byte is preference version).)
            16 => 'I_NONCE_REQUEST', // (Used between sensors when requesting nonce.)
            17 => 'I_NONCE_RESPONSE', // (Used between sensors for nonce response.)
            18 => 'I_HEARTBEAT_REQUEST', // (Heartbeat request)
            19 => 'I_PRESENTATION', // (Presentation message)
            20 => 'I_DISCOVER_REQUEST', // (Discover request)
            21 => 'I_DISCOVER_RESPONSE', // (Discover response)
            22 => 'I_HEARTBEAT_RESPONSE', // (Heartbeat response)
            23 => 'I_LOCKED', // (Node is locked (reason in string-payload))
            24 => 'I_PING', // (Ping sent to node, payload incremental hop counter)
            25 => 'I_PONG', // (In return to ping, sent back to sender, payload incremental hop counter)
            26 => 'I_REGISTRATION_REQUEST', // (Register request to GW)
            27 => 'I_REGISTRATION_RESPONSE', // (Register response from GW)
            28 => 'I_DEBUG', // (Debug message)
        ],
    ];

    const PAYLOAD = [
        0 => 'P_STRING',
        1 => 'P_BYTE',
        2 => 'P_INT16',
        3 => 'P_UINT16',
        4 => 'P_LONG32',
        5 => 'P_ULONG32',
        6 => 'P_CUSTOM',
        7 => 'P_FLOAT32',
    ];

    public $logParser =
  [
      [
          'regex'   => "/^(?:\d+ )?MCO:BGN:INIT (\w+),CP=([^,]+),VER=(.*)/",
          'explain' => 'Core initialization of $1, with capabilities $2, library version $3',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:BGN:BFR/",
          'explain' => 'Callback before()',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:BGN:STP/",
          'explain' => 'Callback setup()',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:BGN:INIT OK,TSP=(.*)/",
          'explain' => 'Core initialized, transport status $1, (1=initialized, 0=not initialized, NA=not available)',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:BGN:NODE UNLOCKED/",
          'explain' => 'Node successfully unlocked (see signing chapter)',
      ],
      [
          'regex'   => "/^(?:\d+ )?!MCO:BGN:TSP FAIL/",
          'explain' => 'Transport initialization failed',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:REG:REQ/",
          'explain' => 'Registration request',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:REG:NOT NEEDED/",
          'explain' => 'No registration needed (i.e. GW)',
      ],
      [
          'regex'   => "/^(?:\d+ )?!MCO:SND:NODE NOT REG/",
          'explain' => 'Node is not registered, cannot send message',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:PIM:NODE REG=(\d+)/",
          'explain' => 'Registration response received, registration status $1',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:PIM:ROUTE N=(\d+),R=(\d+)/",
          'explain' => 'Routing table, messages to node $1 are routed via node $2',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:SLP:MS=(\d+),SMS=(\d+),I1=(\d+),M1=(\d+),I2=(\d+),M2=(\d+)/",
          'explain' => 'Sleep node, duration $1 ms, SmartSleep=$2, Int1=$3, Mode1=$4, Int2=$5, Mode2=$6',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:SLP:TPD/",
          'explain' => 'Sleep node, powerdown transport',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:SLP:WUP=(-?\d+)/",
          'explain' => 'Node woke-up, reason/IRQ=$1 (-2=not possible, -1=timer, >=0 IRQ)',
      ],
      [
          'regex'   => "/^(?:\d+ )?!MCO:SLP:FWUPD/",
          'explain' => 'Sleeping not possible, FW update ongoing',
      ],
      [
          'regex'   => "/^(?:\d+ )?!MCO:SLP:REP/",
          'explain' => 'Sleeping not possible, repeater feature enabled',
      ],
      [
          'regex'   => "/^(?:\d+ )?!MCO:SLP:TNR/",
          'explain' => ' Transport not ready, attempt to reconnect until timeout',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:NLK:NODE LOCKED. UNLOCK: GND PIN (\d+) AND RESET/",
          'explain' => 'Node locked during booting, see signing documentation for additional information',
      ],
      [
          'regex'   => "/^(?:\d+ )?MCO:NLK:TPD/",
          'explain' => 'Powerdown transport',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:INIT/",
          'explain' => 'Transition to Init state',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:INIT:STATID=(\d+)/",
          'explain' => 'Init static node id $1',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:INIT:TSP OK/",
          'explain' => 'Transport device configured and fully operational',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:INIT:GW MODE/",
          'explain' => 'Node is set up as GW, thus omitting ID and findParent states',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSM:INIT:TSP FAIL/",
          'explain' => 'Transport device initialization failed',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:FPAR/",
          'explain' => 'Transition to Find Parent state',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:FPAR:STATP=(\d+)/",
          'explain' => 'Static parent $1 has been set, skip finding parent',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:FPAR:OK/",
          'explain' => 'Parent node identified',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSM:FPAR:NO REPLY/",
          'explain' => 'No potential parents replied to find parent request',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSM:FPAR:FAIL/",
          'explain' => 'Finding parent failed',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:ID/",
          'explain' => 'Transition to Request Id state',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:ID:OK,ID=(\d+)/",
          'explain' => 'Node id $1 is valid',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:ID:REQ/",
          'explain' => 'Request node id from controller',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSM:ID:FAIL,ID=(\d+)/",
          'explain' => 'Id verification failed, $1 is invalid',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:UPL/",
          'explain' => 'Transition to Check Uplink state',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:UPL:OK/",
          'explain' => 'Uplink OK, GW returned ping',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSM:UPL:FAIL/",
          'explain' => 'Uplink check failed, i.e. GW could not be pinged',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:READY/",
          'explain' => 'Transition to Ready state',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:READY:SRT/",
          'explain' => 'Save routing table',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:READY:ID=(\d+),PAR=(\d+),DIS=(\d+)/",
          'explain' => 'Transport ready, node id $1, parent node id $2, distance to GW is $3',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSM:READY:UPL FAIL,SNP/",
          'explain' => 'Too many failed uplink transmissions, search new parent',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSM:READY:FAIL,STATP/",
          'explain' => 'Too many failed uplink transmissions, static parent enforced',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:FAIL:CNT=(\d+)/",
          'explain' => 'Transition to Failure state, consecutive failure counter is $1',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:FAIL:PDT/",
          'explain' => 'Power-down transport',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSM:FAIL:RE-INIT/",
          'explain' => 'Attempt to re-initialize transport',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:CKU:OK,FCTRL/",
          'explain' => 'Uplink OK, flood control prevents pinging GW in too short intervals',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:CKU:OK/",
          'explain' => 'Uplink OK',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:CKU:DGWC,O=(\d+),N=(\d+)/",
          'explain' => 'Uplink check revealed changed network topology, old distance $1, new distance $2',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:CKU:FAIL/",
          'explain' => 'No reply received when checking uplink',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:SID:OK,ID=(\d+)/",
          'explain' => 'Node id $1 assigned',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:SID:FAIL,ID=(\d+)/",
          'explain' => 'Assigned id $1 is invalid',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:PNG:SEND,TO=(\d+)/",
          'explain' => 'Send ping to destination $1',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:WUR:MS=(\d+)/",
          'explain' => 'Wait until transport ready, timeout $1',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:ACK REQ/",
          'explain' => 'ACK message requested',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:ACK/",
          'explain' => 'ACK message, do not proceed but forward to callback',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:FPAR RES,ID=(\d+),D=(\d+)/",
          'explain' => 'Response to find parent request received from node $1 with distance $2 to GW',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:FPAR PREF FOUND/",
          'explain' => 'Preferred parent found, i.e. parent defined via MY_PARENT_NODE_ID',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:FPAR OK,ID=(\d+),D=(\d+)/",
          'explain' => 'Find parent response from node $1 is valid, distance $2 to GW',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:FPAR INACTIVE/",
          'explain' => 'Find parent response received, but no find parent request active, skip response',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:FPAR REQ,ID=(\d+)/",
          'explain' => 'Find parent request from node $1',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:PINGED,ID=(\d+),HP=(\d+)/",
          'explain' => 'Node pinged by node $1 with $2 hops',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:PONG RECV,HP=(\d+)/",
          'explain' => 'Pinged node replied with $1 hops',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:BC/",
          'explain' => 'Broadcast message received',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:GWL OK/",
          'explain' => 'Link to GW ok',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:FWD BC MSG/",
          'explain' => 'Controlled broadcast message forwarding',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:REL MSG/",
          'explain' => 'Relay message',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:REL PxNG,HP=(\d+)/",
          'explain' => 'Relay PING/PONG message, increment hop counter to $1',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:MSG:LEN,(\d+)!=(\d+)/",
          'explain' => 'Invalid message length, $1 (actual) != $2 (expected)',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:MSG:PVER,(\d+)!=(\d+)/",
          'explain' => 'Message protocol version mismatch, $1 (actual) != $2 (expected)',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:MSG:SIGN VERIFY FAIL/",
          'explain' => 'Signing verification failed',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:MSG:REL MSG,NORP/",
          'explain' => 'Node received a message for relaying, but node is not a repeater, message skipped',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:MSG:SIGN FAIL/",
          'explain' => 'Signing message failed',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:MSG:GWL FAIL/",
          'explain' => 'GW uplink failed',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:SAN:OK/",
          'explain' => 'Sanity check passed',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:SAN:FAIL/",
          'explain' => 'Sanity check failed, attempt to re-initialize radio',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:CRT:OK/",
          'explain' => 'Clearing routing table successful',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:LRT:OK/",
          'explain' => 'Loading routing table successful',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:SRT:OK/",
          'explain' => 'Saving routing table successful',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:RTE:FPAR ACTIVE/",
          'explain' => 'Finding parent active, message not sent',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:RTE:DST (\d+) UNKNOWN/",
          'explain' => 'Routing for destination $1 unknown, sending message to parent',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:SND:TNR/",
          'explain' => 'Transport not ready, message cannot be sent',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:READ,(\d+)-(\d+)-(\d+),s=(\d+),c=(\d+),t=(\d+),pt=(\d+),l=(\d+),sg=(\d+):(.*)/",
          'explain' => 'Debug message explanations
      Sender: $1
      Last Node: $2
      Destination: $3
      Sensor Id: $4
      Command: {command:$5}
      Message Type: {type:$5:$6}
      Payload Type: {pt:$7}
      Payload Length: $8
      Signing: $9
      Payload: $10',
      ],
      [
          'regex'   => "/^(?:\d+ )?TSF:MSG:SEND,(\d+)-(\d+)-(\d+)-(\d+),s=(\d+),c=(\d+),t=(\d+),pt=(\d+),l=(\d+),sg=(\d+),ft=(\d+),st=(\w+):(.*)/",
          'explain' => 'Sent Message
      Sender: $1
      Last Node: $2
      Next Node: $3
      Destination: $4
      Sensor Id: $5
      Command: {command:$6}
      Message Type:{type:$6:$7}
      Payload Type: {pt:$8}
      Payload Length: $9
      Signing: $10
      Failed uplink counter: $11
      Status: $12 (OK=success, NACK=no radio ACK received)
      Payload: $13',
      ],
      [
          'regex'   => "/^(?:\d+ )?!TSF:MSG:SEND,(\d+)-(\d+)-(\d+)-(\d+),s=(\d+),c=(\d+),t=(\d+),pt=(\d+),l=(\d+),sg=(\d+),ft=(\d+),st=(\w+):(.*)/",
          'explain' => 'Sent Message
      Sender: $1
      Last Node: $2
      Next Node: $3
      Destination: $4
      Sensor Id: $5
      Command: {command:$6}
      Message Type:{type:$6:$7}
      Payload Type: {pt:$8}
      Payload Length: $9
      Signing: $10
      Failed uplink counter: $11
      Status: $12 (OK=success, NACK=no radio ACK received)
      Payload: $13',
      ],
  ];

    public function getLogParser()
    {
        return $this->logParser;
    }

    public function find($name, $value)
    {
        $thisClass      = new \ReflectionClass(__CLASS__);
        $classConstants = array_keys($thisClass->getConstants());

        if (!\in_array($name, $classConstants)) {
            throw new \Exception('this constant does not exists');
        }
        $flip = array_flip($thisClass->getConstant($name));

        return (string) $flip[$value];
    }

    public function is($const, $index, $value)
    {
        if ($this->get($const, $index) === $value) {
            return true;
        }

        return false;
    }

    public function get($const, $index)
    {
        $thisClass      = new \ReflectionClass(__CLASS__);
        $classConstants = array_keys($thisClass->getConstants());

        foreach ($classConstants as $constName) {
            if ('' !== str_replace($const, '', $constName)) {
                continue;
            }

            $constant = $thisClass->getConstant($constName);
            if (!$constant) {
                throw new \Exception('No constant with this name');
            }

            if (array_key_exists($index, $constant)) {
                return $constant[$index];
            }
        }

        return null;
    }
}
