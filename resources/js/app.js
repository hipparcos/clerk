import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import navComponent from './components/nav.vue'
import registerComponent from './components/form/register.vue'
import loginComponent from './components/form/login.vue'
import bookingComponent from './components/form/booking.vue'

const root = { template: '<p>This is clerk.</p>' }

const routes = [
  { path: '/', component: root },
  { path: '/register', component: registerComponent },
  { path: '/login', component: loginComponent },
  { path: '/bookings/new', component: bookingComponent },
]

const router = new VueRouter({
    routes: routes
})

const app = new Vue({
    router: router,
    components: {
        'clerk-nav': navComponent,
    },
    data: {
        token: "",
        flashSuccessData: "",
    },
    methods: {
        onToken: function(token) {
            this.token = token
        },
        onFlashSuccess: function(message) {
            this.flashSuccessData = message
        }
    }
}).$mount('#app')
