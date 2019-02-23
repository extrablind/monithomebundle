<template>
  <div class="topology">
    <input type="range" min="200" max="20000" step="1" v-model="zoom">
    <d3-network
    :net-nodes="$store.getters.topology.nodes"
    :net-links="$store.getters.topology.links"
    :options="options" />
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css?family=PT+Sans');

.title{
  position:absolute;
  text-align: center;
  left: 2em;
}
h1,a{
  color: #1aad8d;
  text-decoration: none;
}

ul.menu {
  list-style: none;
  position: absolute;
  z-index: 100;
  min-width: 20em;
  text-align: left;
}
ul.menu li{
  margin-top: 1em;
  position: relative;
}

#m-end path, #m-start{
  fill: rgba(18, 120, 98, 0.8);
}
.node-label{
  font-size: 1em;
  fill:white;
}
.link-label{
  fill: purple;
  transform: translate(0,.5em);
  font-size: .8em;
}
</style>

<script>
import Vue from 'vue'
import Vuex from 'vuex'
import store from '../store.js'
import D3Network from 'vue-d3-network'

const $ = require('jquery');

Vue.use(require('vue-moment'));
Vue.use(store);

export default {
  name: 'Topology',
  components: {
    D3Network
  },
  data: function(){
    return {
      nodeSize:10,
      canvas:false,
      zoom: 3000,
      w : 600,
      h : 300
    }
  },
  computed:{
    ...Vuex.mapGetters(['topology']),
    ...Vuex.mapGetters({isConnected : 'socketIsConnected'}),
    options: function(){
      var options = {
        force: this.zoom,
        size:{ w:this.w, h:this.h},
        nodeSize: this.nodeSize,
        nodeLabels: true,
        linkLabels: true,
        canvas: this.canvas,
        linkWidth:4,
      }
      return options;
    },
  },
  mounted:function() {
    $( window ).trigger('resize')
    this.zoom = 3001;
    if(this.isConnected){
      this.initialize()
    }
  },
  watch: {
    isConnected() {
      if(this.isConnected){
        this.initialize()
      }
    },
  },
  created: function () {
    $( window ).on('resize',() =>  {
      this.w = this.getWindowWidth()
      this.h = this.getWindowHeight()
      console.log(this.options.size.w + 'x' + this.options.size.h)
    });
  },
  methods: {
    ...Vuex.mapActions([
      'socketPublish',
      'socketSend',
      'waitStart'
    ]),
    getWindowWidth:function(){
      return   $( window ).width()
    },
    getWindowHeight:function(){
      return   $( window ).height()
    },
    initialize: function(){
      this.socketSend({ action: 'getNodes' });
    },
  },
  beforeDestroy() {
    $( "window" ).off('resize');
  }
}
</script>
