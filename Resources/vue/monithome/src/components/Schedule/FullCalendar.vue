<template>
  <div ref="calendar" id="calendar"></div>
</template>

<script>
import defaultsDeep from 'lodash.defaultsdeep'
import {Calendar} from 'fullcalendar'
import $ from 'jquery'
import Vue from 'vue'
Vue.use(require('vue-moment'));

export default {
  props: {
    config: {
      default() {
        return []
      },
    },
    events: {
      default() {
        return []
      },
    },

    eventSources: {
      default() {
        return []
      },
    },

    editable: {
      default() {
        return true
      },
    },

    selectable: {
      default() {
        return true
      },
    },

    selectHelper: {
      default() {
        return true
      },
    },

    header: {
      default() {
        return {
          left:   'prev,next today',
          center: 'title',
          right:  'month,agendaWeek,agendaDay'
        }
      },
    },

    defaultView: {
      default() {
        return 'agendaWeek'
      },
    },
    sync: {
      default() {
        return false
      }
    },
    config: {
      type: Object,
      default() {
        return {}
      },
    },
  },
  computed: {
    eventsRendering() {
      return this.events;
    }
  },
  watch: {
    eventsRendering() {
      this.render()
    }
  },
  mounted() {
    this.render()
  },
  methods: {
    addEvent : function(event){
      this.calendar.addEvent(event)
    },
    render:function(){
      var calendarEl = document.getElementById('calendar');

      if(this.calendar){
        this.calendar.destroy()
      }
      this.calendar = new Calendar(calendarEl, {
        eventClick: (e, jsEvent, view)=>  {
          var events =  this.$store.getters.events.fullcalendar
          var index =   _.findIndex(events, (o) => { return o.id ==  e.event.id; });
          var event = events[index];
          event.rrule.dtstart = Vue.moment(event.rrule.dtstart).toDate();
          if(event.rrule.until){
            event.rrule.until = Vue.moment(event.rrule.until).toDate();
          }
          this.$emit('onEventClicked', events[index]);
        },
        ...this.config,
        events: this.events
      });
      this.calendar.render();
    }
  }
}
</script>
