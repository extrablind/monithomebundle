import Vue from 'vue'
import Vuex from 'vuex'
import VueWait from 'vue-wait'
const $ = require('jquery');
import WS from './libs/gos.js'
var _ = require('lodash');

Vue.use(Vuex)
Vue.use(require('vue-moment'));

const state = {
  /*
  main: [
  sensors: [],
  nodes: [],
  events: [],
  scenarios: [],
  logs: [],
],
socket:[],
lists:[],
settings: [],
*/

sensors : [],
settings: {},
events : {
  raw : [],
  fullcalendar: []
},
scenarios : [],
logs : [],
nodes : [],
socket: {
  isConnected: false,
  reconnectError: false,
  message: '',
},
dates:{
  format:{},
  from: Vue.moment().subtract(1,'month'),
  to:  Vue.moment()
},
alive:{
  gateway: {
    // a date in the past
    timestamp: 946684800,
    isConnected: false
  }
},
lists: {
  sensors: [],
  sensorsActuator: [],
  nodes:[],
  scenarios: {
    actions : [],
    scenarios : [],
    all : [],
  },
  protocol: {
    sensorValueTypes: [],
    sensorTypes: []
  },
},
topology:{
  nodes: [],
  links:[]
}
}

const methods = {
  update : function(source, destination, match){
    // Find matching object index
    var index = _.findIndex(destination, (o) => { return o[match] == source[match]; });
    if(index === -1) {
      return;
    }
    // Loop over modified keys
    var objectToUpdate = destination[index];
    _.forEach(source, (value, key) => {
      objectToUpdate[key] = value;
    });
    // Replace object
    destination.splice(index, 1, objectToUpdate);
    return destination;
  }
}


const mutations = {
  SOCKET_OPEN:  async function(state, event){
    var websocket = await WS.connect(window.wsUrl);

    websocket.on("socket/connect", (session)=> {
      this.session = session
      state.socket.isConnected = true;
      this.commit('SOCKET_SUBSCRIBE', {channel: "monithome/push"})
      this.commit('SOCKET_SUBSCRIBE', {channel: "monithome/input"})
    })

    websocket.on("socket/disconnect", function (error) {
      state.socket.isConnected = false;
      console.log("Disconnected for " + error.reason + " with code " + error.code);
    })
  },
  SOCKET_PUBLISH: function(state, payload){
    // Request an action from socket
    console.log('Publish ' + payload.action)
    var actionsWithResponse = ['getNodes',  'sensorEditValue', 'removeScenario', 'getLogs', 'getScenario', 'getSensors', 'getScenarios' ]
    var actionRequireResponse = _.findIndex(actionsWithResponse, function(o) { return o === payload.action; });

    if(actionRequireResponse !== -1){
      this.commit('wait/START', payload.action)
    }
    this.session.publish("monithome/input", payload);
  },
  SOCKET_SUBSCRIBE: function(state, payload){
    // Push : pushed to all subscribers from socket
    if(payload.channel === 'monithome/push'){
      this.session.subscribe('monithome/push',  (uri, received)=> {
        console.log("Received push : " + received.msg.action  + " on channel "+ payload.channel)

        switch(received.msg.action){
          case 'updateGatewayStatus':
          state.alive.gateway.timestamp = received.msg.data;
          break

          case 'updateSensor' :
          this.commit('wait/END', 'sensorEditValue')
          this.commit('UPDATE_SENSOR', received.msg.data)
          break;
        }
      });
    }
    // Input : received message from socket
    if(payload.channel === 'monithome/input'){
      this.session.subscribe('monithome/input',  (uri, received)=> {
        console.log("Received input : " + received.msg.action  + " on channel "+ payload.channel)
        var action = received.msg.action
        switch(action){
          case 'setSettings' :
          this.commit('wait/END', 'getSettings')
          this.commit('SET_SETTINGS', received.msg.data)
          break;
          case 'setSensorTypesList' :
          this.commit('wait/END', 'getSensorTypesList')
          this.commit('SET_SENSOR_TYPES_LIST', received.msg.data)
          break;
          case 'setSensorValueTypesList' :
          this.commit('wait/END', 'getSensorValueTypesList')
          this.commit('SET_SENSOR_VALUE_TYPES_LIST', received.msg.data)
          break;
          case 'setEvents' :
          this.commit('wait/END', 'getEvents')
          this.commit('SET_EVENTS', received.msg.data)
          break;
          case 'setNodes' :
          this.commit('wait/END', 'getNodes')
          this.commit('SET_NODES', received.msg.data)
          break;
          case 'updateScenario' :
          this.commit('UPDATE_SCENARIO', received.msg.data)
          break;
          case 'removeScenario' :
          this.commit('wait/END', 'removeScenario')
          this.commit('DELETE_SCENARIO', received.msg.data)
          break;
          case 'setLogs' :
          this.commit('wait/END', 'getLogs')
          this.commit('SET_LOGS', received.msg.data)
          break;
          case 'setScenario' :
          this.commit('wait/END', 'getScenario')
          this.commit('SET_SCENARIO', received.msg.data)
          break;
          case 'setSensors' :
          this.commit('wait/END', 'getSensors')
          this.commit('SET_SENSORS', received.msg.data)
          break;
          case 'setScenarios' :
          this.commit('wait/END', 'getScenarios')
          this.commit('SET_SCENARIOS', received.msg.data)
          break;
          default:
          console.log('No action found with this name : ' + action );
        }
      });
    }
  },
  SET_SETTINGS: function(state, payload){
    state.settings = payload
  },
  SET_SENSOR_TYPES_LIST: function(state, payload){
    state.lists.protocol.sensorTypes = payload
  },
  SET_SENSOR_VALUE_TYPES_LIST:function(state, payload){
    state.lists.protocol.sensorValueTypes = payload
  },
  UPDATE_GATEWAY_STATUS: function(state, payload){
    state.alive.gateway.isConnected = payload
  },
  UPDATE_SCENARIO: function(state, payload){
    var dest =   methods.update(payload, state.scenarios, 'id')
  },
  SET_EVENTS: function(state, payload)  {
    var events =[]
    _.forEach(payload,  (event, indexNode) => {
      events.push(event.event)
    });
    state.events.raw = payload
    state.events.fullcalendar = events
  },
  SET_NODES: function(state, payload)  {
    state.nodes = payload
    var nodesList = [];
    var nodesTopology = {
      nodes:[{id:'gateway', name:"USB Gateway",  _color: 'orange', _size:'25'}],
      links:[]
    };

    var level0;
    _.forEach(payload, async (node, index) => {
      var label = node.nodeName + ' (' + node.place +')';
      nodesList.push({text : label, value:node.id});
      nodesTopology.nodes.push({id:'node-'+node.id, name:label , _color: '#00aaff',  _size:'20'})
      nodesTopology.links.push({sid:'gateway', tid:'node-'+node.id,  _color: '#555'})

      _.forEach(node.sensors,  (sensor, indexNode) => {
        nodesTopology.nodes.push({id:'sensor-'+sensor.id, name:sensor.title , _color: 'red',  _size:'15' })
        nodesTopology.links.push({sid:'sensor-'+sensor.id, tid:'node-'+node.id , _color: '#aaa'})
      });
    });
    state.lists.nodes = nodesList;
    state.topology = nodesTopology;
    return
  },
  SET_SENSORS: function(state, payload)  {
    state.sensors = payload
    var sensors = [];
    var sensorsActuator =  [];
    _.forEach(payload, function(sensor, index) {
      if(sensor.sensorType === "S_BINARY"){
        var sa = {label : sensor.title + ' on ' + sensor.node.place, value : sensor.id}
        sensorsActuator.push(sa)
      }
      var ss = {label : sensor.title + ' on ' + sensor.node.place, value : sensor.id}
      sensors.push(ss);
    });
    state.lists.sensors = sensors
    state.lists.sensorsActuator = sensorsActuator
    return
  },
  UPDATE_SENSOR: function(state, payload)  {
    methods.update(payload, state.sensors, 'id')

    _.forEach(state.nodes, (node, key) => {
      methods.update(payload, state.nodes[key].sensors, 'id')
    });
  },
  SET_SCENARIO: function(state, payload){
    // update
    if(!payload.created){
      return
    }
    state.scenarios.push(payload.scenario)
  },
  SET_SCENARIOS: function (state, payload)  {
    state.scenarios = payload.scenarios
    var sScenarios  = _.filter(state.scenarios, {type:'scenario'}  )
    var sActions    = _.filter(state.scenarios, {type:'action'}  )
    var sAll        = state.scenarios
    state.lists.scenarios.scenarios = []
    state.lists.scenarios.actions = []
    state.lists.scenarios.all = []

    _.forEach(sScenarios, async (s, index) => {
      state.lists.scenarios.scenarios.push({text:s.name, value:s.id })
    });
    _.forEach(sActions, async (s, index) => {
      state.lists.scenarios.actions.push({text:s.name, value:s.id })
    });
    _.forEach(sAll, async (s, index) => {
      state.lists.scenarios.all.push({text:s.name, value:s.id })
    });
  },
  SET_GATEWAY_STATUS: function (state, payload)  {
    state.alive.gateway.isConnected = payload
  },
  DELETE_SCENARIO: function (state, payload)  {
    var scenars = state.scenarios
    var index =   _.findIndex(scenars, (o) => { return o.id === payload.scenario.id; });
    if(index === -1){
      return;
    }
    state.scenarios.splice(index, 1)
  },
  SET_LOGS: function(state, payload){
    state.logs = payload.logs
  },
  SET_DATES: (state, payload) => {
    state.dates.from = payload.from;
    state.dates.to = payload.to;
    state.dates.format.from = state.dates.from.format("YYYY-MM-DD HH:mm:ss");
    state.dates.format.to = state.dates.to.format("YYYY-MM-DD HH:mm:ss");
  },
}

const getters = {
  socketIsConnected : state =>state.socket.isConnected,
  socket            : state => state.socket,
  sensors           : state => state.sensors,
  logs              : state => state.logs,
  dates             : state => state.dates,
  nodes             : state => state.nodes,
  scenarios         : state => state.scenarios,
  lists             : state => state.lists,
  alive             : state => state.alive,
  topology          : state => state.topology,
  events            : state => state.events,
  settings          : state => state.settings,
  getSensorLogsById : (state) => (id) => {
    var i = _.findIndex(state.logs.full.datasets, (o) => { return o.id == id; });
    if(i == -1) return;
    return {'datasets' : [state.logs.full.datasets[i]] }
  }
}

const actions = {
  waitStart: (store, payload) => {
    store.commit('wait/START', payload);
  },
  waitEnd: (store, payload) => {
    store.commit('wait/END', payload);
  },
  socketConnect: (store) => {
    store.commit('SOCKET_OPEN', true);
  },
  socketPublish: (store, payload) => {
    store.commit('SOCKET_PUBLISH', payload);
  },
  // alias for socketPublish
  socketSend: (store, payload) => {
    store.commit('SOCKET_PUBLISH', payload);
  },
  setDates : (store, payload) => {
    store.commit('SET_DATES', payload)
  },
}

let store = new  Vuex.Store({
  state     : state,
  actions   : actions,
  mutations : mutations,
  getters   : getters,
  methods : methods
})

global.store = store
export default store
