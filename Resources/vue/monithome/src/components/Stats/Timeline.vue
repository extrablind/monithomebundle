<template>
  <canvas ref="calendar" id="timeline"></canvas>
</template>

<style>
</style>

<script>
import $ from 'jquery'
import Vue from 'vue'
Vue.use(require('vue-moment'));
import Chart from 'chart.js'
import Timeline from '../../libs/timeline.js'

export default {
  props: ['events', 'labels', 'resources'],
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
    render:function(){
      var ctx = document.getElementById("timeline").getContext('2d');
      var timeline = new Chart(ctx, {
        "type": "timeline",
        "options": {
          "elements": {
            "colorFunction": function(text, data, dataset, index) {
              return Color(data.color);
            },
            "showText": true,
            "textPadding": 10
          }
        },
        "data": {
          "labels": this.labels,
          "datasets": this.events
        },
      });
    }
  }
}
</script>
