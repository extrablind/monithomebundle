<template>
  <div :id="'sensorValue' + id">
    <div v-if="sensor.sensorType === 'S_BINARY'" >
      <toggle-button
      :sync="true"
      :disabled="isDisabled()"
      @change="toggle($event, sensor)"
      :value="sensor.value === '1'"
      :labels="{checked: 'On', unchecked: 'Off'}"
      :width="75"
      :height="25"
      />
    </div>
    <div v-else-if="sensor.value">
      {{sensor.value}}&nbsp;{{sensor.configuration.unit}}
    </div>
    <div v-else>
      --
    </div>
  </div>
</template>
<script>
import Vue from 'vue'
import Vuex from 'vuex'
import store from '../../store.js'
import { ToggleButton } from 'vue-js-toggle-button'
import SensorChart from './SensorChart.vue'
const $ = require('jquery');

Vue.component('ToggleButton', ToggleButton)

export default {
  name: 'ValueDisplayer',
  components: {
    ToggleButton
  },
  props: ["sensor", 'node', "id"],
  mounted () {
  },
  created: function(){},
  data: function(){
    return {
    }
  },
  methods: {
    ...Vuex.mapActions([
      'socketPublish',
    ]),
    isDisabled: function (){
      // During those actions, sensor's value can not be changed
      var disabled =  this.$wait.waiting('nodes.get') ||this.$wait.waiting('sensor.edit.value') ||this.$wait.waiting('sensors.get') || this.$wait.waiting('scenario.trigger')
      return disabled;
    },
    toggle: function($event, sensor){
      this.socketPublish({ action: 'sensorEditValue', params:{
        id: sensor.id,
        value: $event.value
      }
    })
  }
}
}
</script>
