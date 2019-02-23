<template>
  <div class="nodeForm">
    <form  @submit.prevent="saveNode">
      <b-row>
        <b-col :sm="sizes.label"><label for="input-large">Title</label></b-col>
        <b-col :sm="node.input">
          <b-form-input
          placeholder="Short title to identify this sensor"
          v-model="node.title"  type="text"
          />
        </b-col>
      </b-row>

      <b-row>
        <b-col :sm="sizes.label"><label for="input-large">Description</label></b-col>
        <b-col :sm="sizes.input">
          <b-form-textarea
          v-model="node.description"
          placeholder="Describe precisly this sensor"
          :rows="3"
          :max-rows="6">
        </b-form-textarea>
      </b-col>
    </b-row>

    <b-row>
      <b-col :sm="sizes.label"><label for="input-large">Node Id</label></b-col>
      <b-col :sm="sizes.input">
        <b-form-input
        placeholder="Short title to identify this node"
        v-model="node.nodeId"
        type="number"
        min="1"
        max="254"
        :disabled="$store.getters.settings.autoMode"
        />
      </b-col>
    </b-row>

    <b-row>
      <b-col :sm="sizes.label"><label for="input-large">Node type</label></b-col>
      <b-col :sm="node.input">
        <b-form-input
        :disabled="$store.getters.settings.autoMode"
        placeholder="Node type"
        v-model="node.type"  type="text"
        />
      </b-col>
    </b-row>

    <b-row>
      <b-col :sm="sizes.label"><label for="input-large">Node name</label></b-col>
      <b-col :sm="node.input">
        <b-form-input
        :disabled="$store.getters.settings.autoMode"
        placeholder="Node type"
        v-model="node.name"  type="text"
        />
      </b-col>
    </b-row>



  <b-row>
    <b-col :sm="sizes.label"></b-col>
    <b-col :sm="sizes.input">
      <b-button
      :disabled="$store.getters.settings.autoMode"
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
  name: 'NodeForm',
  components: {
    ToggleButton
  },
  validations: {
    node: {
      title:{
        required,
        minLength: minLength(4)
      }
    }
  },
  props: {
    // Vérification de type basique (`null` valide n'importe quel type)
    creation: Boolean,
    node: {
      type: Object,
      default:function () {
        return {
          title: "",
          place: "",
          description: "",
          nodeId: "",
          nodeType: "",
          nodeName: "",
          created: {}
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
    saveNode : function(){
      this.socketSend({ action: 'saveNode', params:{node:this.node}});
    },
    initialize:function(){
    }
  }
}
</script>

<style scoped lang="scss">

</style>
