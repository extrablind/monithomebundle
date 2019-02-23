<template>
  <div class="">
    <div class="card card h-100" :id="'sensor' + id">
      <div class="card-header">
        {{node.title}} {{node.place}}
      </div>

      <div class="card-body">
        <div
        v-for="sensor in node.sensors"
        :id="'panelSensor' + id"
        :class="'show active tab-pane panelSensor' + id"
        role="tabpanel">
        <div class="row">
          <div class="col-lg-2">
            <p class="text-center">
              <i :class="'fa fa-2x fa-' + sensor.configuration.icon"></i>
            </p>
          </div>

          <div class="col-lg-2">
            <ValueDisplayer
            :sensor="sensor"
            :node="node"
            :id="id">
          </ValueDisplayer>
        </div>
        <div class="col-lg-5">
          {{sensor.title}}
        </div>
        <div class="col-lg-3">
          <b-button-group>
            <b-btn size="sm" v-b-modal="'graph'+sensor.id"><i class="fa fa-chart-line"></i></b-btn>
            <b-dropdown size="sm">
              <template slot="button-content">
                <i class="fa fa-cog"></i>
              </template>
              <b-dropdown-header>{{sensor.title}}</b-dropdown-header>
              <b-dropdown-item  v-b-modal="'edit'+sensor.id">Edit</b-dropdown-item>
              <b-dropdown-item  v-b-modal="'infos'+sensor.id">Informations</b-dropdown-item>
            </b-dropdown>
          </b-button-group>
        </div>
        <b-modal hide-footer :id="'edit'+sensor.id" size="lg" :title="'Edit sensor : '+sensor.title">
          <SensorForm :creation="false" :sensor="sensor" :node="node"></sensorForm>
          </b-modal>
          <b-modal  hide-footer :id="'infos'+sensor.id" :title="'Information on node : '+sensor.title" size="lg">
            <b-table hover :items="buildTable(sensor)"></b-table>
          </b-modal>
          <b-modal lazy :id="'graph'+sensor.id" hide-footer size="lg" :title="'Chart for '+sensor.title">
            <SensorChart
            v-if="$store.getters.logs.full"
            :chart-data="$store.getters.getSensorLogsById(sensor.id)"
            :options="{
              scales: {
                xAxes: [{
                  type: 'time',
                  time: {
                    displayFormats: {
                      quarter: 'MMM DDD'
                    }
                  }
                  }]
                }
                }"
                />
              </b-modal>
            </div>
          </div>
        </div>

        <div class="card-footer text-muted">
          <b-button-group >
            <b-btn
            v-b-tooltip.hover title="Add Sensor"
            size="sm"
            :disabled="$store.getters.settings.autoMode"
            v-on:click="addSensor(node)"
            ><i class="fa fa-plus" ></i></b-btn>
          </b-button-group>

        </div>

      </div>
      <br/>
    </div>

  </div>
</template>
<script>
import Vue from 'vue'
import Vuex from 'vuex'
import store from '../../store.js'
import ToggleButton from 'vue-js-toggle-button'
import ValueDisplayer from './ValueDisplayer.vue'
import SensorForm from '../Form/SensorForm.vue'
import SensorChart from './SensorChart.vue'
import { Line, mixins } from 'vue-chartjs'

const $ = require('jquery');


Vue.use(ToggleButton)
export default {
  name: 'NodeCard',
  components: {
    ValueDisplayer,
    SensorChart,
    SensorForm
  },
  props: ["node", 'id', 'index'],
  mounted () {
  },
  computed: {
    // rajouter les accesseurs dans `computed` avec l'opérateur de décomposition
    ...Vuex.mapGetters([
      'getSensorLogsById',
    ])
  },
  created: function(){  },
  data: function(){
    return {
    }
  },
  methods: {
    ...Vuex.mapActions([
      'getNodes'
    ]),
    buildTable:function(sensor){
      var table = []
      _.forEach(sensor, function(value, key) {
        table.push({key, value})
      });
      return table
    },
    openPanel: function(type, id, $event){
      $event.preventDefault()
      $('#sensor' + id + ' .nav a').removeClass('active')
      $('#sensor' + id + ' .nav a[href="#'+type+'Link'+id+'"]').addClass('active')
      $('#sensor' + id + ' .tab-pane').hide()
      $('#'+ type + id).show()
    },
    addSensor: function(node){
      this.$emit('addSensor', node)
    },
    initialize: function(){
      this.socketSend({ action: 'getSensorTypesList' });
      this.socketSend({ action: 'getSensorValueTypesList' });
    }
  }
}
</script>

<style scoped lang="scss">

</style>
