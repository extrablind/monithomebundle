<template>
  <div class="actions">
    <b-btn v-b-modal.create v-on:click="reset">Create Scenario</b-btn>
    <!-- Modal Component -->
    <b-modal lazy ref="create"  hide-footer id="create" :title="title" size="lg">
      <form  @submit.prevent="save">
        <b-row>
          <b-col :sm="sizes.label"><label for="input-large">Name</label></b-col>
          <b-col :sm="sizes.input">
            <b-form-input
            placeholder="Short title to identify this sensor"
            v-model="scenario.name"  type="text"
            />
          </b-col>
        </b-row>
        <b-row>
          <b-col :sm="sizes.label"><label for="input-large">Description</label></b-col>
          <b-col :sm="sizes.input">
            <b-form-textarea
            v-model="scenario.description"
            placeholder="Describe precisly this sensor"
            :rows="3"
            :max-rows="6">
          </b-form-textarea>
        </b-col>
      </b-row>
      <b-row>
        <b-col :sm="sizes.label"><label for="input-small">Type</label></b-col>
        <b-col :sm="sizes.input">
          <b-form-group label="">
            <b-form-radio-group
            buttons
            v-model="scenario.type"
            button-variant="outline-secondary"
            :options="type"
            name="radioBtnOutline" />
          </b-form-group>
        </b-col>
      </b-row>


      <b-row>
        <b-col :sm="sizes.label"><label for="input-small">Activate</label></b-col>
        <b-col :sm="sizes.input">
          <b-form-group label="">
            <b-form-radio-group
            buttons
            v-model="scenario.active"
            button-variant="outline-secondary"
            :options="[{text:'On', value:true}, {text:'Off', value:false}]"
            name="radioBtnOutline" />
          </b-form-group>
        </b-col>
      </b-row>

      <div class="conditions" v-if="scenario.type === 'scenario'">
        <strong>When</strong>
        <div class="card card-body bg-dark "  v-for="(condition, index) in scenario.conditions">
          <p>
            <button :disabled="index === 0"
            class="btn btn-danger btn-sm" v-on:click="removeCondition(index)">
            <i class="fa fa-minus"></i>
          </button>
          <span class="badge badge-secondary" style="width:30px;">{{index}}</span>

          <span class="typeBlock">
            <select :disabled="index === 0" v-model="condition.type">
              <option v-for="option in typeList" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </span>
          <span class="sensorBlock">
            <select v-model="condition.sensor">
              <option v-for="option in $store.getters.lists.sensors" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </span>
          <span class="operatorBlock">
            <select v-model="condition.operator">
              <option v-for="option in operatorList" v-bind:value="option.value">
                {{ option.label }}
              </option>
            </select>
          </span>
          <span class="valueBlock">
            <input v-model="condition.value"  class="" type="text" />
          </span>
        </p>
      </div>
      <button class="btn btn-success btn-sm" v-on:click="addCondition($event, 'and')"><i class="fa fa-plus"></i>Add Condition</button>
    </div>
    <!-- ACTIONS -->
    <div class="actions">
      <br/>
      <div v-if="scenario.type == 'action'"><strong>Do</strong></div>
      <div v-else><strong>Then Do</strong></div>



      <div class="card card-body bg-dark" v-for="(action, index) in scenario.actions">
        <p>
          <button :disabled="index === 0" class="btn btn-danger btn-sm" v-on:click="removeAction(index)"><i class="fa fa-minus"></i></button>

          <span class="badge badge-secondary" style="width:30px;">{{index}}</span>

          <span class="sensorBlock">
            Set
            <select v-model="action.sensor">
              <option v-for="option in $store.getters.lists.sensorsActuator" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </span>
          to
          <span class="valueBlock">
            <input v-model="action.value"  class="" type="text" />
          </span>
        </p>
      </div>
      <button class="btn btn-success btn-sm" v-on:click="addAction($event)"><i class="fa fa-plus"></i>Add Action</button>
    </div>


    <br/>
    <br/>
    <button
    type="submit"
    class="btn btn-success btn-sm pull-right"
    ><i class="fa fa-save"></i>&nbsp;Save</button>

    <button
    class="btn btn-warning btn-sm "
    type="button"
    v-on:click="cancel()"><i class="fa fa-times"></i>&nbsp;Cancel</button>

    <button
    class="btn btn-warning btn-sm "
    type="button"
    v-on:click="cancel()"><i class="fa fa-times"></i>&nbsp;Reset</button>

  </form>
</b-modal>


<ScenarioList
@onDeleteScenario="deleteScenario"
@onEditScenario="editScenario"
@onDuplicateScenario="duplicateScenario"
:scenarios="scenarios"
:deleteLoaded="$store.loaded"/>
<br/>
<br/>
</div>
</template>

<script>
import Vuex from 'vuex'
const $ = require('jquery');
import ScenarioList from '../components/Scenario/ScenarioList.vue'

export default {
  name: 'Scenarios',
  components: {
    ScenarioList
  },
  data: function(){
    return {
      sizes:{
        input:9,
        label:3
      },
      type:[
        {text: 'Scenario', value: 'scenario'},
        {text: 'Action', value: 'action'},
      ],
      scenario: {},
      creation:true,
      deleteLoaded: true,
      operatorList: [
        {label : 'is egual to', value:'=='},
        {label : 'is greater than', value:'>'},
        {label : 'is lower than', value:'<'},
        {label : 'is greater or egual to', value:'>='},
        {label : 'is lower or egual to', value:'<='}
      ],
      actionType: [
        {label : 'Set', value:'set'},
        {label : 'Execute', value:'exec'},
      ],
      actionType: [
        {label : 'value', value:'set'},
        {label : 'Execute', value:'exec'},
      ],
      typeList: [
        {label: 'when', value:'when'},
        {label: 'or', value:'or'},
        {label: 'and', value:'and'},
      ],

    }
  },
  computed:{
    ...Vuex.mapGetters({ isConnected : 'socketIsConnected'}),
    ...Vuex.mapGetters(['scenarios', 'list', 'sensors']),
    title :function(){
      if(this.creation){
        return 'Create a new scenario'
      }
      return 'Edit '+ this.scenario.name +'(#' + this.scenario.id +')'
    },
  },
  created: function () {
    this.reset()
  },
  watch: {
    isConnected() {
      if(this.isConnected){
        this.initialize()
      }
    }
  },
  mounted:function(){
    if(this.isConnected){
      this.initialize()
    }
  },
  methods: {
    ...Vuex.mapActions([
      'getScenarios',
      'getSensors',
      'socketSend',
      'socketPublish'
    ]),
    initialize: function(){
      this.socketSend({ action: 'getScenarios', params: {type: 'all'} });
      this.socketSend({ action: 'getSensors', params: {type: 'all'} });
    },
    duplicateScenario: function (scenario){
      this.reset()
      // clone
      var s = JSON.parse(JSON.stringify(scenario));
      s.id = null
      this.socketSend({ action: 'saveScenario', params: {scenario: s} });
      this.reset()
    },
    editScenario: function(scenario){
      this.$refs.create.show()
      this.scenario = scenario;
      this.creation = false
      window.scrollTo(100, 0);
    },
    deleteScenario: function(scenario){
      this.socketSend({ action: 'deleteScenario', params: {scenario: scenario} });
    },
    cancel: function(){
      this.reset();
    },
    reset(){
      this.creation = true
      this.scenario = {
        active : true,
        type: 'action',
        actions : [
          {  sensor : null, value : null}
        ],
        conditions : [
          { type: 'when', sensor : null, operator : '>', value : '1'}
        ]
      }
    },
    save:function () {
      this.socketSend({ action: 'saveScenario', params: {scenario: this.scenario} });
    },
    removeCondition: function(index){
      this.scenario.conditions.splice(index, 1);
    },
    addCondition: function($event, type){
      this.scenario.conditions.push({ type: type, sensor : null, operator : '>', value : '1'})
    },
    removeAction: function(index){
      this.scenario.actions.splice(index, 1);
    },
    addAction: function($event){
      this.scenario.actions.push({  sensor : null, value : null})
    }

  }
}
</script>
