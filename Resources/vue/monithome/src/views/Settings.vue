<template>
  <div class="settings">

    <form>

      <b-row>
        <b-col :sm="3"><label for="input-large">Auto mode</label></b-col>
        <b-col :sm="9">
          <ToggleButton
          :sync="true"
          v-model="setting.autoMode"
          :labels="{checked: 'On', unchecked: 'Off'}"
          :width="75"
          :height="25"
          />
          <br/>
          <small class="help">When enabled, gateway manage presentation message and new nodes/sensor automatically</small>
        </b-col>
      </b-row>

      <b-row>
        <b-col :sm="3"><label for="input-large">Metric</label></b-col>
        <b-col :sm="9">
          <toggle-button
          :sync="true"
          v-model="setting.metric"
          :labels="{checked: 'On', unchecked: 'Off'}"
          :width="75"
          :height="25"
          />
        </b-col>
      </b-row>

      <b-row>
        <b-col :sm="3"><label for="input-large">Time zone</label></b-col>
        <b-col :sm="9">
          <b-form-input
          v-model="setting.timezone"
          type="text"
          placeholder="Europe/Paris" />
        </b-col>
      </b-row>

      <br/>
      <div class="form-group row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-9">
          <button type="submit" v-on:click="saveSettings()" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
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
import { mapWaitingActions, mapWaitingGetters } from 'vue-wait'
const $ = require('jquery');
import { ToggleButton } from 'vue-js-toggle-button'

Vue.component('ToggleButton', ToggleButton)
Vue.use(require('vue-moment'));
Vue.use(store);


export default {
  name: 'Settings',
  components: {
    ToggleButton
  },
  data: function(){
    return {
      setting: {
        autoMode: false,
        metric: true,
        timezone: "Europe/Paris",
      }
    }
  },
  computed:{
    ...Vuex.mapGetters(['settings']),
    ...Vuex.mapGetters({isConnected : 'socketIsConnected'}),
    settingsLoaded () {
      return this.$store.getters.settings
    }
  },
  watch: {
    settingsLoaded(){
      if(this.$store.getters.settings){
        this.setting = this.$store.getters.settings
      }
    },
    isConnected() {
      if(this.isConnected){
        this.initialize()
      }
    }
  },
  beforeDestroy:  function() {
  },
  created: function () {
  },
  mounted: function(){
    if(this.isConnected){
      this.initialize()
    }
  },
  methods: {
    ...Vuex.mapActions([
      'socketPublish',
      'socketSend',
      'waitStart'
    ]),
    initialize: function(){
      this.socketSend({ action: 'getSettings' });
    },
    saveSettings: function(){
      this.socketSend({ action: 'saveSettings', params : { settings: this.settings } });
    }
  }
}
</script>
