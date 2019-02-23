<template>
  <div ref="calendar" id="timeline"></div>
</template>

<style>
.fc-event{
  position: absolute!important;
}
</style>
<script>
import $ from 'jquery'
import Vue from 'vue'
Vue.use(require('vue-moment'));

import defaultsDeep from 'lodash.defaultsdeep'
import 'fullcalendar'
import 'fullcalendar-scheduler'
import "fullcalendar-scheduler/dist/scheduler.min.css";

export default {
  props: ['events', 'config', 'resources'],
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
      let cal = $('#timeline');
      if(this.calendar){
        cal.fullCalendar( 'destroy' )
      }
      this.calendar = cal.fullCalendar({
          resourceGroupField: 'groupId',
        defaultView: 'timelineDay',
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
      //  editable: true, // enable draggable events
      //  aspectRatio: 1.8,
        header: {
          left: 'today prev,next',
          center: 'title',
          right: 'timelineDay,timelineThreeDays'
        },
        views: {
          timelineThreeDays: {
            type: 'timeline',
            duration: { days: 3 }
          },
        },
        resourceLabelText: '',
        resources: this.resources,
              now: '2018-04-07',
        events:[
          { id: '1', overlap:true, resourceId: '38', start: '2018-04-07T02:00:00', end: '2018-04-07T07:00:00', title: 'event 1' },
       { id: '2', overlap:true, resourceId: '38', start: '2018-04-07T05:00:00', end: '2018-04-07T22:00:00', title: 'event 2' },
       { id: '3', overlap:true, resourceId: '38', start: '2018-04-06', end: '2018-04-08', title: 'event 3' },
       { id: '4',overlap:true,  resourceId: '38', start: '2018-04-07T03:00:00', end: '2018-04-07T08:00:00', title: 'event 4' },
       { id: '5', overlap:true, resourceId: '38', start: '2018-04-07T00:30:00', end: '2018-04-07T02:30:00', title: 'event 5' }
     ],
     slotEventOverlap: true,
       slotDuration:'00:00:01'
      });

      $('.fc-timeline-event').css({'top' : 0})
      //$('.fc-widget-content>div:first-child').css({height:40})
      //this.calendar.render();

    /*

    this.calendar = new Calendar(calendarEl, {
    eventClick: (e, jsEvent, view)=>  {
    /*
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
*/
}
}
}
</script>
