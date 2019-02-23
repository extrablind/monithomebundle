<template>
  <div class="scenario">
    <div class="card h-200">

      <div class="toggle" v-on:click="toggle()">
        <i v-if="!opened" class="fa fa-caret-right "></i>
        <i v-if="opened" class="fa fa-caret-down "></i>
      </div>

      <div class="card-header">

        <div class="btn-group pull-right">
          <button
          class="btn btn-secondary btn-sm btn-warning trigger"
          v-on:click="trigger(scenario)"
          ><i class="fa fa-bolt"></i>&nbsp;Trigger {{scenario.type}}</button>
        </div>

        <h5 >
          <span v-on:click="toggle()">
            {{scenario.name}}
          </span>
        </h5>

      </div>

      <div v-if="opened" class="card-body">
        <h5 class="card-title">
          <small>
            {{scenario.description| truncate(100) }}
          </small>
        </h5>
        <div v-if="!scenario.conditions" class="btn btn-error btn-sm">x
          <div class="" v-for="(condition, index) in scenario.conditions">
            When {{condition.type}} to {{action.value}}
          </div>
        </div>
        <div v-else class="">
          When action is triggered,
          <span v-for="(action, index) in scenario.actions">
            <span v-if="index > 0"> and </span>
            set <b><SensorDisplayer :sensorId="action.sensor" /></b>
            to <b>{{ action.value | formatValue}}</b>
          </span>
        </div>
      </div>

      <div class="card-footer text-muted">
        <span class="badge badge-light">Id {{scenario.id}}</span>
        <span class="badge badge-light">Type {{scenario.type}}</span>
        <span v-if="scenario.lastPlayed" class="badge badge-light">
          Last played {{ scenario.lastPlayed.date | formatDate('full') }}
        </span>
        <span v-else  class="badge badge-light">
          Last played : never
        </span>
      </div>
    </div>
  </div>
</div>
</template>
<style>
.card{
  margin-bottom: 3px;
}
</style>


<script>
import Vue from 'vue'
import Vuex from 'vuex'
import store from '../../store.js'
import SensorDisplayer from './SensorDisplayer.vue'

var VueTruncate = require('vue-truncate-filter')
Vue.use(VueTruncate)
Vue.use(SensorDisplayer)

const $ = require('jquery');

export default  {
  name: 'ScenarioCard',
  props: {
    scenario:Object,
    id:String,
  },
  components: {
    SensorDisplayer
  },
  data:function(){
    return {
      opened: false
    }
  },
  computed:{
  },
  methods: {
    ...Vuex.mapActions([
      'socketPublish'
    ]),
    toggle:function(){
      this.opened = !this.opened
    },
    trigger: function(scenario){
      this.socketPublish({ action: 'triggerScenario', params:{id:scenario.id}})
    }
  }
}
</script>
