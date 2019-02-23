<template>
  <div class="stats">
    <div class="row row-fluid">
      <div class="col col-lg-1">
        <div class="">
          <button class="btn btn-block btn-secondary btn-sm" v-on:click="updateLogs(10,'minutes')">Ten Min.</button>
          <button class="btn btn-block btn-secondary btn-sm" v-on:click="updateLogs(30,'minutes')">Thirty Min</button>
          <button class="btn btn-block btn-secondary btn-sm" v-on:click="updateLogs(1,'hour')">Last Hour</button>          <button class="btn btn-block btn-secondary btn-sm" v-on:click="updateLogs(5,'hours')">Five Hours</button>
          <button class="btn btn-block btn-secondary btn-sm" v-on:click="updateLogs(10,'hours')">Ten Hours</button>
          <button class="btn btn-block btn-secondary btn-sm" v-on:click="updateLogs(1,'day')">Today</button>
          <button class="btn btn-block btn-secondary btn-sm" v-on:click="updateLogs(7,'days')">Last week</button>
          <button class="btn btn-block btn-secondary btn-sm" v-on:click="updateLogs(1,'month')">Last Month</button>
          <button class="btn btn-block btn-secondary btn-sm" v-on:click="updateLogs(1,'year')">Last Year</button>
        </div>
      </div>
      <div class="col-lg-11">
        <div class="wrapper" style="position:relative; width:100%; height:500px;">
          <Timeline
          :events="$store.getters.logs.history.events"
          :labels="$store.getters.logs.history.labels"
          />
          <hr/>
          <FullChart
          :events="$store.getters.events"
          />
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script>
import FullChart from '../components/Stats/FullChart.vue'
import Timeline from '../components/Stats/Timeline.vue'
import Vuex from 'vuex'
import Vue from 'vue'

Vue.use(require('vue-moment'));

export default {
  name: 'Stats',
  components: {
    FullChart,
    Timeline
  },
  data: function(){
    return {
      date:{
        format:{      },
        from: Vue.moment().subtract(1,'month'),
        to:  Vue.moment()
      }
    }
  },
  computed:{
    ...Vuex.mapGetters([ 'logs' , 'sensors']),
    ...Vuex.mapGetters({ isConnected : 'socketIsConnected'})
  },
  created: function () {
  },
  mounted: function(){
    if(this.isConnected){
      this.initialize()
    }
  },
  watch: {
    isConnected() {
      if(this.isConnected){
        this.initialize()
      }
    }
  },
  methods: {
    ...Vuex.mapActions([
      'socketPublish',
      'setDates',
    ]),
    initialize: function(){
      if(this.isConnected){
        this.updateLogs()
      }
    },
    config: function(){
      return {
      }
    },
    getDate : function(hours) {
      const currentDate = new Date();
      const currentYear = currentDate.getFullYear();
      const currentMonth = currentDate.getMonth() + 1;
      const currentDay = currentDate.getDate();
      const timeStamp = new Date(`${currentYear}-${currentMonth}-${currentDay} 00:00:00`).getTime();
      return new Date(timeStamp + hours * 60 * 60 * 1000);
    },
    tasks: function(){
      return [
        {
          id: 1,
          label: 'Make some noise',
          start: this.getDate(-24 * 5),
          duration: 5 * 24 * 60 * 60,
          progress: 85,
          type: 'task'
        }, {
          id: 2,
          label: 'With great power comes great responsibility',
          parentId: 1,
          start: this.getDate(-24 * 4),
          duration: 4 * 24 * 60 * 60,
          progress: 100,
          type: 'task',
          style: {
            fill: '#1EBC61',
            stroke: '#0EAC51'
          },
          progressBarStyle: {
            bar: {
              fill: '#0EAC51'
            }
          }
        }, {
          id: 3,
          label: 'Courage is being scared to death, but saddling up anyway.',
          parentId: 2,
          start: this.getDate(-24 * 3),
          duration: 2 * 24 * 60 * 60,
          progress: 100,
          type: 'task'
        }
      ];
    },
    updateLogs: function(number, temporality){
      var from = Vue.moment().subtract(number,temporality);
      var to = Vue.moment().subtract(1,'second');
      this.setDates({from, to})
      from = this.$store.getters.dates.format.from
      to = this.$store.getters.dates.format.to
      this.socketPublish({ action: 'getLogs', params: {from, to} });
      this.$forceUpdate();
    }

  }
}
/*
"datasets": [
  {
    "data": [
      {
        start: "2018-01-22T16:00:00.000Z",
        end : "2018-01-23T05:40:44.626Z",
        title: "Unknown",
        colorI: "red"
      },
      {
        start: "2018-01-23T06:00:00.000Z",
        end : "2018-01-23T08:00:00.626Z",
        title: "Unknown",
        color: "red"
      }
    ]
  },
  {
    "data": [
      {
        start : "2018-01-22T16:00:00.000Z",
          end : "2018-01-23T04:57:43.736Z",
          title : "On",
          color: "red"
      },
      {
        start:"2018-01-23T04:57:43.736Z",
        end:"2018-01-23T04:57:55.437Z",
        start:"Off",
        color: "red"
      },
      {
        start:"2018-01-23T04:57:55.437Z",
        end:"2018-01-23T05:40:44.626Z",
        title:"On",
        color: "green"
      }
    ]
  }
]
*/
</script>
