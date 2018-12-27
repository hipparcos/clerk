import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import navComponent from './components/nav.vue'
import registerComponent from './components/form/register.vue'

const root = { template: '<p>This is clerk.</p>' }

const routes = [
  { path: '/', component: root },
  { path: '/register', component: registerComponent },
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
        token: ""
    }
}).$mount('#app')
