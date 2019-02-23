import Vue from 'vue'
import Router from 'vue-router'
import Stats from './views/Stats.vue'
import Sensors from './views/Sensors.vue'
import Nodes from './views/Nodes.vue'
import Scenarios from './views/Scenarios.vue'
import Settings from './views/Settings.vue'
import Topology from './views/Topology.vue'
import Schedule from './views/Schedule.vue'

Vue.use(Router)

var router = new Router({
  //mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      redirect: '/nodes'
    },
    {
      path: '/nodes',
      name: 'nodes',
      component: Nodes
    },
    {
      path: '/stats',
      name: 'stats',
      component: Stats
    },
    {
      path: '/sensors',
      name: 'sensors',
      component: Sensors
    },
    {
      path: '/scenarios',
      name: 'scenarios',
      component: Scenarios
    },
    {
      path: '/settings',
      name: 'settings',
      component: Settings
    },
    {
      path: '/topology',
      name: 'topology',
      component: Topology
    },
    {
      path: '/schedule',
      name: 'schedule',
      component: Schedule
    },

  ]
})
export default router
