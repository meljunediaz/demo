import Vue from 'vue';
import router from './router'
import App from './components/App';
import BootstrapVue from 'bootstrap-vue'

import * as VueGoogleMaps from "vue2-google-maps";



Vue.use(VueGoogleMaps, {
  load: {
    key: "AIzaSyDkPOfYe6TwEG0_2znSSeNj4tPKVuOlWpQ",
    libraries: "places" // necessary for places input
  }
});


Vue.use(BootstrapVue)

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'







new Vue({
    el: '#app',
    router,
    template : '<App/>',
    components: { App },
    
});