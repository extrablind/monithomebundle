<template>
  <div id="app">
    <div id="nav">
      <div class="btn-group">
        <button disabled  class="btn btn-secondary">
          <i class="fa fa-clock"></i>
        </button>
        <button disabled  class="btn btn-secondary">{{$store.getters.dates.format.from}}</button>
        <button disabled  class="btn btn-secondary">{{$store.getters.dates.format.to}}</button>
      </div>

      <div class="btn-group pull-right">
        <button disabled  class="btn btn-secondary">WebSockets
          <i v-if="$store.getters.socket.isConnected" class="fa fa-check"></i>
          <i v-if="!$store.getters.socket.isConnected" class="fa fa-times"></i>
        </button>

        <button disabled  class="btn btn-secondary">Gateway daemon
          <i v-if="$store.getters.alive.gateway.isConnected" class="fa fa-check"></i>
          <i v-if="!$store.getters.alive.gateway.isConnected" class="fa fa-times"></i>
        </button>

        <button  v-if="!$wait.any" v-on:click="refresh()" class="btn btn-secondary"><i class="fa fa-refresh"></i></button>
        <button disabled v-if="$wait.any" class="btn btn-secondary">
          <i  class="fa fa-spinner fa-spin"></i>
        </button>
        <!--
        <button disabled v-if="$wait.waiting('getLogs')" class="btn btn-secondary">Getting logs</button>
        <button disabled v-if="$wait.waiting('sensorEditValue')" class="btn btn-secondary">Edit value sensor</button>
        <button disabled v-if="$wait.waiting('removeScenario')" class="btn btn-secondary">Deleting scenario</button>
        <button disabled v-if="$wait.waiting('getSensors')" class="btn btn-secondary">Getting sensors</button>
        <button disabled v-if="$wait.waiting('getNodes')" class="btn btn-secondary">Getting nodes</button>
        <button disabled v-if="$wait.waiting('getScenarios')" class="btn btn-secondary"> Loading scenarios</button>
      -->
    </button>
  </div>
  <br/>
  <br/>
  <ul class="nav nav-pills nav-fill">
    <li class="nav-item">
      <router-link class="nav-link" to="/nodes">Nodes</router-link>
    </li>
    <li class="nav-item">
      <router-link class="nav-link" to="/stats">Stats</router-link>
    </li>
    <li class="nav-item">
      <router-link class="nav-link" to="/scenarios">Scenarios</router-link>
    </li>
    <li class="nav-item">
      <router-link class="nav-link" to="/schedule">Schedule</router-link>
    </li>

    <li class="nav-item">
      <router-link class="nav-link" to="/topology">Topology</router-link>
    </li>
    <li class="nav-item">
      <router-link class="nav-link" to="/settings">Settings</router-link>
    </li>
  </ul>
</div>
<hr/>
<router-view/>
</div>
</template>

<style>
.router-link-active{
  color: hsla(0,0%,100%,.75);
  background-color: #073642;
}
</style>

<script>
import Vue from 'vue'
import Vuex from 'vuex'
import VueWait from 'vue-wait'
import store from './store.js'
import VueNativeSock from 'vue-native-websocket'

Vue.use(Vuex)
Vue.use(VueWait)
Vue.use(require('vue-moment'));
Vue.use(store);

const wait =  new VueWait({
  useVuex: true,              // Uses Vuex to manage wait state
  vuexModuleName: 'wait',      // Vuex module name

  registerComponent: true,     // Registers `v-wait` component
  componentName: 'v-wait',     // <v-wait> component name, you can set `my-loader` etc.

  registerDirective: true,     // Registers `v-wait` directive
  directiveName: 'wait',       // <span v-wait /> directive name, you can set `my-loader` etc.
});



Vue.filter('formatValue' , function(value, format) {
  if (!value) {
    return ;
  }
  switch(value) {
    case '1':
      return 'ON';
    case '0':
      return 'OFF';
  }
});

Vue.filter('formatDate', function(value, format) {
  if (!value) {
    return ;
  }
  value = String(value);
  switch(format) {
    case "hours-full":
    return Vue.moment(value).format('HH:mm:ss')
    case "hours":
    return Vue.moment(value).format('HH:mm')
    case 'days':
    return Vue.moment(value).format('DD/MM/YYYY')
    case 'full':
    return Vue.moment(value).format('DD/MM/YYYY HH:mm:ss')
    default:
    return Vue.moment(value).format('DD/MM/YYYY HH:mm')
  }
});

const $ = require('jquery');

export default {
  name: 'App',
  store,
  wait: wait,
  components: {
  },
  data: function(){
    return {
    }
  },
  computed:{
    ...Vuex.mapGetters(['sensors', 'logs', 'alive']),
    ...Vuex.mapGetters({isConnected : 'socketIsConnected'}),
  },
  created: function () {
    this.socketConnect()
    this.checkGatewayLastTimestamp();
  },
  mounted:function(){
  },
  watch: {
    isConnected() {
      if(this.isConnected){
        this.initialize()
      }
    }
  },
  methods: {
    ...Vuex.mapActions([
      'setDates',
      'socketConnect',
      'socketPublish',
      'socketSend'
    ]),
    initialize: function(){
      var from = Vue.moment().subtract(1,'month');
      var to = Vue.moment().subtract(1,'second');
      this.setDates({from: from, to:to})
      this.socketSend({ action: 'getSettings' });
    },
    checkGatewayLastTimestamp: function(){
      setInterval(() => {
        var gatewayLastSeenAt = this.$store.getters.alive.gateway.timestamp;
        var isConnected = this.$store.getters.alive.gateway.isConnected;
        var now = Vue.moment().unix();
        var timeIsUp = (now - gatewayLastSeenAt) > 2 ;

        if(isConnected && timeIsUp){
          this.$store.commit('UPDATE_GATEWAY_STATUS', false)
        }else if(!isConnected && !timeIsUp){
          this.$store.commit('UPDATE_GATEWAY_STATUS', true)
        }
      }, 1000);
    },
    refresh:function (){
      this.socketSend({ action: 'getNodes'});
      this.socketSend({ action: 'getScenarios'});
      this.socketSend({ action: 'getSensors' });
      this.socketSend({ action: 'getSettings' });

      var from = this.$store.getters.dates.format.from
      var to = this.$store.getters.dates.format.to
      this.socketPublish({ action: 'getLogs', params: {from, to} });
    },
  }
}
</script>
