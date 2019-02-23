import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
require('./assets/monithome.scss')

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
