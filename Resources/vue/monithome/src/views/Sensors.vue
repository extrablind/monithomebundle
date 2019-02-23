<template>
  <div class="sensors">
    <SensorForm :creation="true"></sensorForm>

    <div class="card card h-100" :id="'sensor' ">
      <div class="card-body">
        <div class=" text-center">
          <b-btn v-b-modal.create><i class="fa fa-plus"></i> Create sensor</b-btn>
        </div>
        <!-- Using modifiers -->
        <b-modal size="lg">
          <SensorForm :creation="creation" :sensor="sensor"></sensorForm>
          </b-modal>
        </div>
      </div>

      <br v-for="index in Math.ceil($store.getters.sensors.length / modulo)" />
      <div v-if="$store.getters.sensors" class="row row-fluid" v-for="index in Math.ceil($store.getters.sensors.length / modulo)">
        <div class="col col-lg-6 col-xl-6 col-12 col-sm-12 col-md-6" v-for="(sensor, i) in $store.getters.sensors.slice((index - 1) * modulo, index * modulo)">
          <SensorCard :sensor="sensor" :id="generateUniqId()" :index="index" />
        </div>
      </div>
    </div>
  </template>

  <style>
  .table-condensed{
    font-size: 12px;
  }
  .table-condensed   td{
    padding:0;
  }
  </style>

  <script>
  import Vue from 'vue'
  import Vuex from 'vuex'
  import store from '../store.js'
  import SensorCard from '../components/Control/SensorCard.vue'
  import ScenarioCard from '../components/Scenario/ScenarioCard.vue'
  import draggable from 'vuedraggable'
  import NodeCard from '../components/Control/NodeCard.vue'
  import SensorForm from '../components/Form/SensorForm.vue'
  import { mapWaitingActions, mapWaitingGetters } from 'vue-wait'
  import FontAwesomeInput from '../components/Form/FontAwesomeInput.vue'

  const $ = require('jquery');

  Vue.use(require('vue-moment'));
  Vue.use(store);

  export default {
    name: 'Sensors',
    components: {
      SensorCard,
      draggable,
      ScenarioCard,
      NodeCard,
      SensorForm,
      FontAwesomeInput
    },
    data: function(){
      return {
        modulo: 2,
        loaded:null,
        scenarios:{},
        map: {},
        tabs: [ 'panelSensorLink' ,'panelGraphLink', 'panelEditLink'],
        tabIndex : 0,
        date:{
          format:{      },
          from: Vue.moment().subtract(1,'month'),
          to:  Vue.moment()
        },
        creation:false,
        sensor: {
          id: null,
          title: "",
          node: {}
        }
      }
    },
    computed:{
      ...Vuex.mapGetters(['sensors', 'logs', 'nodes']),
      ...Vuex.mapGetters({ isConnected : 'socketIsConnected'})
    },
    beforeDestroy:  function() {
      window.removeEventListener('keydown', this.onkey)
    },
    created: function () {
      window.addEventListener('keydown', this.onkey)
    },
    mounted: function(){
      this.initialize()
    },
    watch: {
      isConnected() {
        if(!this.isConnected){
          return
        }
        this.initialize()
      }
    },
    methods: {
      ...Vuex.mapActions([
        'socketPublish',
        'socketSend'
      ]),
      create: function(){
        this.creation = true;
      },
      initialize: function(){
        this.socketSend({ action: 'getScenarios'});
        this.socketSend({ action: 'getSensors' });
        this.socketSend({ action: 'getNodes' });
        this.socketSend({ action: 'getSensorTypesList' });
        this.socketSend({ action: 'getSensorValueTypesList' });
        var from = this.$store.getters.dates.format.from
        var to = this.$store.getters.dates.format.to
        this.socketSend({ action: 'getLogs', params: {from, to} });
      },
      generateUniqId : function(){
        return Math.random().toString(36).substr(2, 9);
      },
      onkey (e) {
        this.map[e.keyCode] = e.type == 'keydown';
        if(this.map[71]){
          if(this.tabIndex >= this.tabs.length-1){
            this.tabIndex = 0;
          }
          else{
            this.tabIndex++
          }
          var tabclass = '.nav a[href^="#'+this.tabs[this.tabIndex]+'"]';
          $(tabclass).click(function(){
            this.click()
          });
          $(tabclass).trigger('click');
          this.map = []
        }
      }
    }
  }
  </script>
