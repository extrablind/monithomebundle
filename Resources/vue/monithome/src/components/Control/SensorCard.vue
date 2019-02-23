<template>
  <div class="">
    <div class="card card h-100" :id="'sensor' + id">
      <div class="card-header">
        {{sensor.title}} ({{sensor.node.place}})
      </div>
      <div class="card-body">
        <div class="tab-content">
          <div
          :id="'panelSensor' + id"
          :class="'show active tab-pane panelSensor' + id"
          role="tabpanel">
          <div class="row">
            <div class="col-lg-2">
              <p class="text-center">
                <i :class="'fa fa-3x fa-' + sensor.configuration.icon"></i>
              </p>
            </div>
            <div class="col-lg-10">
              <h4>
                <ValueDisplayer
                :sensor="sensor"
                :node="sensor.node" :id="id">
              </ValueDisplayer>
            </h4>
          </div>
        </div>

      </div>

      <div
      :id="'panelEdit' + id"
      :class="'tab-pane panelEdit' + id"
      role="tabpanel">
      <SensorForm :sensor="sensor"></sensorForm>

    </div>

    <div
    :id="'panelGraph' + id"
    :class="'tab-pane panelGraph' + id"
    role="tabpanel">
    <SensorChart
    v-if="$store.getters.logs.sensors"
    :chart-data="$store.getters.getLogFromSensor(sensor.id)"
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
      </div>
    </div>
  </div>
  <div class="card-footer">
    <nav class="nav nav-pills nav-justified pull-right">
      <a class="nav-link active"  v-on:click="openPanel('panelSensor', id, $event)" :href="'#panelSensorLink'+id" data-toggle="tab"><i class="fa fa-thermometer-three-quarters"></i></a>
      <a class="nav-link" v-on:click="openPanel('panelGraph',  id, $event)" :href="'#panelGraphLink'+id" data-toggle="tab"><i class="fa fa-area-chart"></i></a>

      <b-btn size="sm" v-b-modal="'graph'+id"><i class="fa fa-chart"></i> graph</b-btn>
      <b-modal :id="'graph'+id" size="lg">
        <SensorChart
        v-if="$store.getters.logs.sensors"
        :chart-data="$store.getters.logs.sensors[sensor.id]"
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

      <b-btn size="sm" v-b-modal="'edit'+id"><i class="fa fa-edit"></i> Edit</b-btn>
      <b-modal :id="'edit'+id" size="lg">
        <SensorForm :creation="false" :sensor="sensor"></sensorForm>
      </b-modal>

    </nav>

    <div class="text-muted">{{sensor.updated.date |formatDate('full') }}</div>
  </div>

</div>
<br/>
</div>
</template>

<script>
import Vue from 'vue'
import Vuex from 'vuex'
import store from '../../store.js'
import ToggleButton from 'vue-js-toggle-button'
import { Line, mixins } from 'vue-chartjs'
import ValueDisplayer from './ValueDisplayer.vue'
import SensorChart from './SensorChart.vue'
import SensorForm from '../Form/SensorForm.vue'
const $ = require('jquery');

Vue.use(ToggleButton)
export default {
  name: 'SensorCard',
  components: {
    SensorChart,
    SensorForm,
    ValueDisplayer,
    ToggleButton
  },
  props: ["sensor", 'id', 'index'],
  mounted () {
  },
  created: function(){
  },
  data: function(){
    return {
    }
  },
  methods: {
    ...Vuex.mapActions([
      'socketPublish',
      'socketSend'
    ]),
    saveSensor : function(){
      this.socketSend({ action: 'getNodes' });
    },
    openPanel: function(type, id, $event){
      $event.preventDefault()
      $('#sensor' + id + ' .nav a').removeClass('active')
      $('#sensor' + id + ' .nav a[href="#'+type+'Link'+id+'"]').addClass('active')
      $('#sensor' + id + ' .tab-pane').hide()
      $('#'+ type + id).show()
    },
  }
}
</script>

<style scoped lang="scss">

</style>
