<template>
  <div class="sensorForm">
    <form  @submit.prevent="saveSensor">
      <b-row>
        <b-col :sm="sizes.label"><label for="input-large">Title</label></b-col>
        <b-col :sm="sizes.input">
          <b-form-input
          placeholder="Short title to identify this sensor"
          v-model="sensor.title"  type="text"
          />
        </b-col>
      </b-row>

      <b-row>
        <b-col :sm="sizes.label"><label for="input-large">Description</label></b-col>
        <b-col :sm="sizes.input">
          <b-form-textarea
          v-model="sensor.description"
          placeholder="Describe precisly this sensor"
          :rows="3"
          :max-rows="6">
        </b-form-textarea>
      </b-col>
    </b-row>

    <b-row>
      <b-col :sm="sizes.label"><label for="input-large">Unit</label></b-col>
      <b-col :sm="sizes.input">
        <b-form-input v-model="sensor.configuration.unit"  type="text"/>
      </b-col>
    </b-row>

    <b-row>
      <b-col :sm="sizes.label"><label for="input-small">Log</label></b-col>
      <b-col :sm="sizes.input">
        <b-row>
          <b-col :sm="2">
            <b-form-group label="">
              <b-form-radio-group
              buttons
              v-model="sensor.configuration.log.status"
              button-variant="outline-secondary"
              :options="[{text:'On', value:'on'}, {text:'Off', value:'off'}]"
              name="radioBtnOutline" />
            </b-form-group>
          </b-col>
          <b-col :sm="4">
            <b-form-radio-group
            :disabled="sensor.configuration.log.status === 'off'"
            buttons
            v-model="sensor.configuration.log.mode"
            button-variant="outline-secondary"
            :options="choicesLogMode"
            name="radioBtnOutline" />
          </b-col>
          <b-col :sm="6">
            <b-input-group>
              <b-form-input
              :disabled="sensor.configuration.log.mode === 'always' || sensor.configuration.log.status === 'off'"
              placeholder="Every xx minutes"
              v-model="sensor.configuration.log.temporality.every"
              type="text"
              />
              <b-input-group-append>
                <b-btn
                disabled
                variant="secondary">Min</b-btn>
              </b-input-group-append>
            </b-input-group>
          </b-col>
        </b-row>
      </b-form-group>
    </b-col>
  </b-row>

  <b-row>
    <b-col :sm="sizes.label">
      <label for="input-large">Icon</label>
    </b-col>
    <b-col :sm="sizes.input">
      <b-form-input
      v-model="sensor.configuration.icon"  type="text"  />
    </b-col>
  </b-row>

  <b-row>
    <b-col :sm="sizes.label"><label for="input-large">Sensor Id</label></b-col>
    <b-col :sm="sizes.input">
      <b-form-input
      :disabled="$store.getters.settings.autoMode"
      v-model="sensor.sensorId"
      type="number"
      min="0"
      max="254"
      />
    </b-col>
  </b-row>

  <b-row>
    <b-col :sm="sizes.label"><label for="input-large">Value type</label></b-col>
    <b-col :sm="sizes.input">
      <b-form-select
      :disabled="$store.getters.settings.autoMode"
      v-model="sensor.sensorValueType"
      :options="$store.getters.lists.protocol.sensorValueTypes"
      class="mb-3"
      size="sm" />
    </b-col>
  </b-row>

  <b-row>
    <b-col :sm="sizes.label"><label for="input-large">Sensor type</label></b-col>
    <b-col :sm="sizes.input">
      <b-form-select
      :disabled="$store.getters.settings.autoMode"
      v-model="sensor.sensorType"
      :options="$store.getters.lists.protocol.sensorTypes"
      class="mb-3"
      size="sm" />
    </b-col>
  </b-row>

  <b-row>
    <b-col :sm="sizes.label"><label for="input-large">Belongs to node</label></b-col>
    <b-col :sm="sizes.input">
      <b-form-select
      :disabled="$store.getters.settings.autoMode"
      v-model="node.id"
      :options="$store.getters.lists.nodes"
      class="mb-3"
      size="sm" />
    </b-col>
  </b-row>

  <b-row>
    <b-col :sm="sizes.label"></b-col>
    <b-col :sm="sizes.input">
      <b-button
      variant="primary"
      type="submit">Save</b-button>
    </b-col>
  </b-row>
</form>

</div>
</template>


<script>
import Vue from 'vue'
import Vuex from 'vuex'
import BootstrapVue from 'bootstrap-vue'
import store from '../../store.js'
import ToggleButton from 'vue-js-toggle-button'
import { Line, mixins } from 'vue-chartjs'
import ValueDisplayer from '../Control/ValueDisplayer.vue'
import SensorChart from '../Control/SensorChart.vue'
import Vuelidate from 'vuelidate'
import { required, minLength, between } from 'vuelidate/lib/validators'

const $ = require('jquery');

Vue.use(ToggleButton)
export default {
  name: 'SensorForm',
  components: {
    ToggleButton
  },
  validations: {
    sensor: {
      title:{
        required,
        minLength: minLength(4)
      },
      icon:{
        required,
      },
      sensorId:{
        required,
      },
      sensorValueType: {
        required,
      },
      sensorType: {
        required,
      }
    },
  },
  props: {
    // Vérification de type basique (`null` valide n'importe quel type)
    creation: Boolean,
    // Objet avec une valeur par défaut
    node:{
      type: Object
    },
    sensor: {
      type: Object,
      default:function () {
        return {
          title: "",
          description: "",
          value: "",
          configuration: {
            unit: "",
            icon: "thermometer-full",
            log: {
              status: "on",
              mode: "always",
              temporality: { every: 3, unit: "m" }
            }
          },
          created: {},
          updated: { },
          unit: "",
          sensorId: "",
          sensorUniqueIdentifier: "",
          sensorValueType: "V_TEMP",
          sensorType: "S_TEMP",
          lastLogDate: { }
        }
      }
    }
  },
  created: function(){
  },
  mounted () {
    this.initialize()
  },
  data: function(){
    return {
      choicesLogMode: [
        {text:"Always", value:"always"},
        {text:"Every", value:"every"},
      ],
      sizes:{
        input:9,
        label:3
      },
    }
  },
  computed:{
    ...Vuex.mapGetters(['lists'])
  },
  watch: {
  },
  methods: {
    ...Vuex.mapActions([
      'socketPublish',
      'socketSend'
    ]),
    saveSensor : function(){
      this.socketSend({ action: 'saveSensor', params:{sensor:this.sensor, node:this.node}});
    },
    initialize:function(){
    }
  }
}
</script>

<style scoped lang="scss">

</style>
