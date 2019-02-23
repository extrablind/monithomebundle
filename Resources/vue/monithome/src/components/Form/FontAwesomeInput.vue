<template>
  <div class="fontAwesomeInput">
    <h1>fsdfqs</h1>

    <div class="">
      <!-- <i :class="'fa fa-2x '+icon"></i> -->
      <v-select :options="icons">
        <template slot="option" slot-scope="option">
          <i :class="'fa '+option.value"></i>
          {{ option.value }}
        </template>
      </v-select>
    </div>

  </div>
</template>

<script>
import Vue from 'vue'
import Vuex from 'vuex'
import store from '../../store.js'
import { Line, mixins } from 'vue-chartjs'
import icons from './FontAwesomeIcons.js'
const $ = require('jquery');

import vSelect from 'vue-select'

export default {
  name: 'FontAwesomeInput',
  components: {
    vSelect
  },
  props: ["sensor", 'creation'],
  mounted () {
  },
  created: function(){
  },
  data: function(){
    return {
      icons : icons
    }
  },
  computed:{
    ...Vuex.mapGetters(['lists'])
  },
  methods: {
    ...Vuex.mapActions([
      'socketPublish',
      'socketSend'
    ]),
    createOptions:function(){
      var icons = []
      _foreach(this.icons, (e, i) =>{
        icons.push({label: e, value:e})
      })
      this.icons = icons
    }
  }
}
</script>

<style scoped lang="scss">

</style>
