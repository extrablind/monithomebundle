<template>
  <div class="control">
    <div class="row row-fluid">
      <hr/>
      <div class="col-lg-8">
        <div class="" v-if="$store.getters.nodes">
          <br v-if="$store.getters.nodes" v-for="index in Math.ceil($store.getters.nodes.length / modulo)" />
          <div v-if="$store.getters.nodes.length > 0" class="row row-fluid" v-for="index in Math.ceil($store.getters.nodes.length / modulo)">
            <div
            class="col col-lg-6 col-xl-6 col-12 col-sm-12 col-md-6"
            v-for="(node, i) in $store.getters.nodes.slice((index - 1) * modulo, index * modulo)">
            <NodeCard @addSensor="showModalAddSensor" :node="node" :id="generateUniqId()" :index="index" />
          </div>
        </div>
      </div>
    </div>
    <div class="col col-lg-4">
      <h4>Actions</h4>
        <b-button v-b-modal.createNode
        :disabled="$store.getters.settings.autoMode"
        block>
          <i class="fa fa-plus"></i>Add a node
        </b-button>
        <b-modal lazy id="createNode" size="lg" hide-footer title="Add a node">
            <NodeForm :creation="true" />
        </b-modal>
      <br/>
      <br/>

      <h4>Triggers</h4>
      <draggable
      @end="endReorder()"
      @start="startReorder($event)"
      @update="changeOrder($event)"
      :options="{group:'people'}">

      <ScenarioCard
      v-for="(s, i) in scenarios"
      :scenario="s"
      :data-index="i"
      :key="s.id"
      />


    </draggable>


  </div>




  <b-modal lazy ref="addSensor" size="lg" hide-footer title="Add a sensor">
      <SensorForm :creation="true" :node="create.node" />
  </b-modal>

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
import draggable from 'vuedraggable'

import SensorCard from '../components/Control/SensorCard.vue'
import ScenarioCard from '../components/Scenario/ScenarioCard.vue'
import NodeCard from '../components/Control/NodeCard.vue'
import SensorForm from '../components/Form/SensorForm.vue'
import NodeForm from '../components/Form/NodeForm.vue'

const $ = require('jquery');

Vue.use(require('vue-moment'));
Vue.use(store);

export default {
  name: 'Nodes',
  components: {
    SensorCard,
    draggable,
    ScenarioCard,
    NodeCard,
    SensorForm,
    NodeForm
  },
  data: function(){
    return {
      create:{
        node:null
      },
      modulo: 2,
      loaded:null,
      map: {},
      tabs: [ 'panelSensorLink', 'panelInfoLink' ,'panelGraphLink'],
      tabCount : 1,
      date:{
        format:{      },
        from: Vue.moment().subtract(1,'month'),
        to:  Vue.moment()
      }
    }
  },
  computed:{
    ...Vuex.mapGetters(['nodes']),
    ...Vuex.mapGetters({isConnected : 'socketIsConnected'}),
    scenarios: function (){
      return  _.filter(this.$store.state.scenarios, {type:'action'}  )
    }
  },
  watch: {
    isConnected() {
      if(this.isConnected){
        this.initialize()
      }
    }
  },
  beforeDestroy:  function() {
    window.removeEventListener('keydown', this.onkey)
  },
  created: function () {
    window.addEventListener('keydown', this.onkey)
  },
  mounted: function(){
    if(this.isConnected){
      this.initialize()
    }
  },
  methods: {
    ...Vuex.mapActions([
      'socketSend',
      'waitStart'
    ]),
    createNode: function(){
      console.log("Add node action")

    },
    showModalAddSensor: function(node){
      console.log("Add sensor")
      this.create.node = node
      this.$refs.addSensor.show()
    },
    hideModalAddSensor:function(){

    },
    initialize: function(){
      var from = this.$store.getters.dates.format.from
      var to = this.$store.getters.dates.format.to
      this.socketSend({ action: 'getNodes' });
      this.socketSend({ action: 'getScenarios'});
      this.socketSend({ action: 'getSensors' });
      this.socketSend({ action: 'getLogs', params: {from, to} });
      this.socketSend({ action: 'getSensorTypesList' });
      this.socketSend({ action: 'getSensorValueTypesList' });
    },
    endReorder: function(){
      $('.scenario').css({ 'opacity':1});
      $('.scenario .card').css({'padding':'0px', 'border': 'none'});
    },
    startReorder: function($event){
      $('.scenario').not($event.target).not('.sortable-ghost').css({'opacity':0.4});
      $('.sortable-ghost .card')
      .css({'opacity':1,'padding':'3px','border': '2px dashed gray' })
    },
    changeOrder: function(){
      var sc =[]
      $('.scenario').each((i, elem) => {
        var index = $(elem).data('index');
        sc.push({id:this.scenarios[index].id});
      });
      this.socketSend({ action: 'changeScenariosOrder', params:{
        scenarios: sc
      }
    })
  },
  generateUniqId : function(){
    return Math.random().toString(36).substr(2, 9);
  }
}
}
</script>
