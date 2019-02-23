<template>
  <div class="schedule">
    <b-container fluid>
      <form  @submit.prevent="save">
        <b-row>
          <b-col :lg="6">
            <div class="card">
              <div class="card-header">

                <div class="pull-right">
                  <button v-on:click="reset()" type="button" class="btn btn-sm btn-success ">
                    <i class="fa fa-plus"></i>
                    New
                  </button>
                  &nbsp;
                  &nbsp;
                  &nbsp;
                  <button :disabled="!event.id" v-on:click="remove()" type="button" class="btn btn-sm btn-warning ">Delete</button>
                </div>

                <div class="" v-if="event.id">Edit event {{event.title}} (#{{event.id}})</div>
                <div class="" v-if="!event.id">Create new event</div>

              </div>
              <div class="card-body">
                <b-row>
                  <b-col :sm="sizes.label"><label for="input-large">Starts on</label></b-col>
                  <b-col :sm="sizes.input">
                    <input id="inputTitle" class="form-control" v-model="event.title" placeholder="Title">
                    <div class="error" v-if="!$v.event.title.required">Field is required.</div>
                    <div class="error" v-if="!$v.event.title.minLength">Minimum 3characters</div>
                  </b-col>
                </b-row>

                <b-row>
                  <b-col :sm="sizes.label"><label for="input-small">Repeat</label></b-col>
                  <b-col :sm="sizes.input">
                    <b-form-group label="">
                      <b-form-radio-group
                      buttons
                      v-model="event.rrule.freq"
                      button-variant="outline-primary"
                      :options="freqs"
                      name="radioBtnOutline" />
                    </b-form-group>
                  </b-col>
                </b-row>

                <b-row>
                  <b-col :sm="sizes.label"><label for="input-default">Day of week</label></b-col>
                  <b-col :sm="sizes.input">
                    <b-form-group label="">
                      <b-form-checkbox-group
                      v-model="event.rrule.byweekday"
                      buttons
                      :disabled="disabled.byweekday"
                      button-variant="outline-primary"
                      size=""
                      :options="days.slice(0,5)">
                    </b-form-checkbox-group>
                  </b-form-group>
                  <b-form-group label="">
                    <b-form-checkbox-group
                    v-model="event.rrule.byweekday"
                    buttons
                    :disabled="disabled.byweekday"
                    button-variant="outline-primary"
                    size=""
                    :options="days.slice(5,7)">
                  </b-form-checkbox-group>
                </b-form-group>
              </b-col>
            </b-row>

            <b-row>
              <b-col :sm="sizes.label"><label for="input-large">Starts on</label></b-col>
              <b-col :sm="sizes.input">
                <date-picker
                class="dtstart"
                :value="event.rrule.dtstart"
                @dp-hide="changeDateConvert"
                :config="{format: 'YYYY-MM-DD HH:mm:ss'}"
                />
              </b-col>
            </b-row>

            <b-row>
              <b-col :sm="sizes.label"><label for="input-large">End</label></b-col>
              <b-col :sm="sizes.input">
                <b-input-group>
                  <date-picker
                  class="until"
                  :value="event.rrule.until"
                  @dp-hide="changeDateConvert"
                  :config="{format: 'YYYY-MM-DD HH:mm:ss'}"
                  />
                  <b-input-group-append>
                    <b-btn
                    :disabled="!event.rrule.until"
                    v-on:click="removeEndDate" variant="secondary"><i class="fa fa-times"></i></b-btn>
                  </b-input-group-append>
                </b-input-group>
              </b-col>
            </b-row>

            <b-row>
              <b-col :sm="sizes.label"><label for="input-large">Action</label></b-col>
              <b-col :sm="sizes.input">
                <b-form-select
                v-model="event.extendedProps.scenario"
                :options="$store.getters.lists.scenarios.actions"
                class="mb-3"
                size="sm" />
                <div class="error" v-if="!$v.event.extendedProps.scenario.required">Field is required.</div>
              </b-col>
            </b-row>

            <b-row>
              <b-col :sm="sizes.label"><label for="input-large">Color</label></b-col>
              <b-col :sm="sizes.input">
                <b-form-input v-model="event.color"  type="color" class="mb-3" size="sm" />
              </b-col>
            </b-row>
          </div>

          <div class="card-footer">

            <nav class="nav nav-pills nav-justified pull-right">
              <button type="submit" :disabled="$v.invalid" class="btn btn-sm btn-primary">Save</button>
            </nav>
            <div class="text-muted">{{getRruleString()}}</div>
          </div>

        </div>
      </b-col>
      <b-col :lg="6">
        <FullCalendar
        ref="calendar"
        @onEventClicked="edit"
        :events="$store.getters.events.fullcalendar"
        :config="config"
        />
      </b-col>
    </b-row>
  </form>
</b-container>
<br/>
</div>
</template>

<script>
import Vue from 'vue'
import Vuex from 'vuex'
const $ = require('jquery');
import 'fullcalendar/dist/fullcalendar.css'
import 'fullcalendar/dist/plugins/rrule'; // need this! or include <script> tag instead
import BootstrapVue from 'bootstrap-vue'
import FullCalendar from '../components/Schedule/FullCalendar.vue'
import { RRule, RRuleSet, rrulestr } from 'rrule'
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import Vuelidate from 'vuelidate'
import { required, minLength, between } from 'vuelidate/lib/validators'

Vue.use(require('vue-moment'));
Vue.use(Vuelidate)
Vue.use(BootstrapVue);


export default {
  name: 'Schedule',
  components: {
    FullCalendar,
    datePicker,
    Vuelidate
  },
  data: function(){
    return {
      event : {
        rrule:{
          freq:{}
        }
      },
      sizes:{
        label:3,
        input:9
      },
      config: {
        firstDay: 1,
        defaultView: 'listWeek',
        weekends: true,
        header: {
          left:   'title',
          center: '',
          right:  'today prev,next listWeek,month prevYear,nextYear'
        },
        footer:true
      },
      freqs:[
        {text: 'Hourly', value: RRule.HOURLY},
        {text: 'Daily', value: RRule.DAILY},
        {text: 'Weekly', value: RRule.WEEKLY},
        {text: 'Monthly', value: RRule.MONTHLY},
        {text: 'Yearly', value: RRule.YEARLY},
      ],
      days: [
        {text: 'Monday', value: RRule.MO.weekday},
        {text: 'Tuesday', value: RRule.TU.weekday},
        {text: 'Wednesday', value: RRule.WE.weekday},
        {text: 'Thursday', value: RRule.TH.weekday},
        {text: 'Friday', value: RRule.FR.weekday},
        {text: 'Saturday', value: RRule.SA.weekday},
        {text: 'Sunday', value: RRule.SU.weekday}
      ],
      creation:true,
      schedule:{},
      form: {
        submitStatus: false
      },
      disabled:{
        byweekday: false
      }
    }
  },
  validations: {
    event: {
      title: {
        required: required(),
        minLength: minLength(3)
      },
      extendedProps:{
        scenario:{
          required: required()
        },
      },
      rrule: {
        dtstart: {
          required
        }
      }
    },
  },
  computed:{
    ...Vuex.mapGetters({ isConnected : 'socketIsConnected'}),
    ...Vuex.mapGetters(['scenarios', 'lists', 'events']),
    freq() {
      return this.event.rrule.freq;
    }
  },
  created: function () {

  },
  watch: {
    freq(){
      // No bwd when monthly, yearly or weekkly
      if(this.event.rrule.freq < 3){
        delete  this.event.rrule['byweekday']
        this.disabled.byweekday = true
        return;
      }
      // Reactivate the array
      if(!this.event.rrule.byweekday){
        this.event.rrule.byweekday = []
      }
      this.disabled.byweekday = false
    },
    isConnected : function() {
      if(this.isConnected){
        this.initialize()
      }
    }
  },
  created:function(){
    this.reset()
  },
  mounted:function(){
    if(this.isConnected){
      this.initialize()
    }
  },
  methods: {
    ...Vuex.mapActions([
      'socketSend',
      'socketPublish'
    ]),
    initialize: function(){
      if(this.$store.getters.socket.isConnected){
        this.socketSend({ action: 'getEvents' });
        this.socketSend({ action: 'getScenarios', params: {type: 'action'} });
      }
    },
    edit: function(event){
      this.event = event;
    },
    save: function() {
      this.$v.$touch()
      if (this.$v.$invalid) {
        this.submitStatus = 'ERROR'
        alert("invalid form, please fill title, action and start date")
        return;
      }
      var rule = new RRule(this.event.rrule);
      var message = {
        action: 'saveEvent',
        params: { event: this.event, rule: rule.toString()  }
      };
      this.socketSend(message);
      this.reset()
    },
    removeEndDate: function(){
      this.event.rrule.until = null
    },
    changeDateConvert(event){
      if($(event.currentTarget).hasClass('dtstart')){
        if(!event.date){
          delete this.event.rrule['dtstart']
          return
        }
        this.event.rrule.dtstart = new Date(event.date); //Vue.moment(event.date).toDate();
      }else if ($(event.currentTarget).hasClass('until')){
        if(!event.date){
          delete this.event.rrule['until']
          return
        }
        this.event.rrule.until = Vue.moment(event.date).toDate();
        return
      }
    },
    valid(){
      if (this.title !== ""){
        return true;
      }
      return false;
    },
    reset: function(){
      this.event = {
        title: "",
        color:"#DC3B3B",
        extendedProps:{
          scenario:undefined,
        },
        editable:false,
        rrule : {
          freq: RRule.DAILY,
          interval:1,
          until: null,
          dtstart: Vue.moment().toDate(),
          //rendering: 'inverse-background',
          //durationEditable: false,
          byweekday:[
            RRule.MO.weekday,
            RRule.TU.weekday,
            RRule.WE.weekday,
            RRule.TH.weekday,
            RRule.FR.weekday
          ],
        }
      }
    },
    remove:function(){
      this.socketSend({ action: 'removeEvent', params: {id: this.event.id} });
      this.reset()
    },
    getRruleString: function()  {
      if(this.event.rrule){
        var r = new RRule(this.event.rrule)
        return r.toText()
      }
    },
  }
}
</script>
