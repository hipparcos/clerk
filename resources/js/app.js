import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import VueNavComponent from './components/nav.vue'

const root = { template: '<p>This is clerk.</p>' }

const routes = [
  { path: '/', component: root },
]

const router = new VueRouter({
    routes: routes
})

const app = new Vue({
    router: router,
    components: {
        'clerk-nav': VueNavComponent
    }
}).$mount('#app')
