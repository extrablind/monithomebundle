<template>
  <div class="scenarios">
    <table class="table table-hover">
      <thead>
        <tr>
          <th v-for="key in columns">{{key}}</th>
          <th>Conditions</th>
          <th>Doing</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="scenario in  scenarios">
          <td >{{scenario.id}}</td>
          <td >{{scenario.name}}</td>
          <td >{{scenario.type}}</td>
          <td ><span v-if="scenario.lastPlayed">{{scenario.lastPlayed.date | formatDate }}</span></td>
          <td >{{scenario.active}}</td>
          <td >
            <div v-if="scenario.conditions" class="btn btn-error btn-sm">
              <div class="" v-for="(condition, index) in scenario.conditions">
                {{condition.type}} <SensorDisplayer :sensorId="condition.sensor" />
                's value is
                <b-badge  class="sensorName"  variant="success">{{condition.operator}}</b-badge>
                <b-badge  class="sensorName"  variant="info">{{condition.value}}</b-badge>
              </div>
            </div>
          </td>
          <td >
            <div class="" v-for="(action, index) in scenario.actions">
              Set <SensorDisplayer :sensorId="action.sensor" /> to
              <b-badge  class="sensorName"  variant="info">{{action.value}}</b-badge>
            </div>
          </td>
          <td>

            <button
          </button>

          <b-dropdown size="sm">
            <template slot="button-content">
              <i class="fa fa-cog"></i>
            </template>
            <b-dropdown-header>Actions</b-dropdown-header>

            <b-dropdown-item v-on:click="trigger(scenario)">
              <i class="fa fa-bolt"></i>&nbsp;Trigger
            </b-dropdown-item>

            <b-dropdown-item :disabled="$wait.waiting('scenario.save') || $wait.waiting('scenarios.get.all')"
            v-on:click="duplicate(scenario)"><i class="fa fa-copy"></i>&nbsp;Duplicate</b-dropdown-item>

            <b-dropdown-item :disabled="$wait.waiting('scenario.delete') || $wait.waiting('scenarios.get.all')"
            v-on:click="edit(scenario)"><i class="fa fa-edit"></i>&nbsp;Edit</b-dropdown-item>

            <b-dropdown-divider></b-dropdown-divider>
            <b-dropdown-item :disabled="$wait.waiting('scenario.delete') || $wait.waiting('scenarios.get.all')"
            v-on:click="del(scenario)"><i class="fa fa-times"></i>&nbsp;Delete</b-dropdown-item>
          </b-dropdown>
        </b-button-group>


        <div class="pull-right">
        </div>
      </td>
    </tr>
  </tbody>
</table>
</div>
</template>

<script>
import Vue from 'vue'
import Vuex from 'vuex'
import SensorDisplayer from './SensorDisplayer.vue'

const $ = require('jquery');

export default  {
  name: 'ScenarioList',
  props: {
    data: Array,
    scenarios:Array,
    filterKey: String,
    deleteLoaded: Boolean
  },
  components: {
    SensorDisplayer
  },
  data: function () {
    this.columns = ['id', 'name', 'type','created', 'active']
    return {
    }
  },
  computed: {
  },
  filters: {
    capitalize: function (str) {
      return str.charAt(0).toUpperCase() + str.slice(1)
    }
  },
  methods: {
    ...Vuex.mapActions([
      'socketPublish'
    ]),
    duplicate: function(scenario){
      this.$emit('onDuplicateScenario', scenario)
    },
    trigger: function(scenario){
      this.socketPublish({ action: 'triggerScenario', params:{id:scenario.id}})
    },
    del: function(scenario){
      this.$emit('onDeleteScenario', scenario)
    },
    edit:function(scenario){
      this.$emit('onEditScenario', scenario)
    },
  }
}
</script>

<style scoped lang="scss">
</style>
