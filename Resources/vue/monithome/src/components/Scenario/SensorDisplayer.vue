<template>
    <b-badge  class="sensorName"  variant="secondary">{{ getSensorName(sensorId) }}</b-badge>
</template>
<style>
</style>


<script>
import Vue from 'vue'
import Vuex from 'vuex'
import store from '../../store.js'

const $ = require('jquery');

export default  {
  name: 'SensorDisplayer',
  props: ["sensorId"],
  data:function(){
    return {
    }
  },
  computed:{
    ...Vuex.mapGetters(['sensors']),
  },
  methods: {
    getSensorName: function(value){
      if (!value) {
        return ;
      }
      var index = _.findIndex(this.$store.getters.sensors, (o) => { return o.id == value; });
      if(index === -1){
        return value;
      }
      return this.$store.getters.sensors[index].title;
    }
  }
}
</script>
