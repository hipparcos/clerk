import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import navComponent from './components/nav.vue'
import registerComponent from './components/form/register.vue'
import loginComponent from './components/form/login.vue'
import bookingIndexComponent from './components/list/booking.vue'
import bookingNewComponent from './components/form/booking.vue'

const root = { template: '<p></p>' }

const state = {
    authenticated: false,
}

const ifAuthenticated = function(to, from, next) {
    if (state.authenticated) {
        next()
        return
    }
    next('/login')
}
const ifNotAuthenticated = function(to, from, next) {
    if (!state.authenticated) {
        next()
        return
    }
    next('/')
}

const routes = [
    {
        path: '/',
        name: 'root',
        component: root
    },
    {
        path: '/register',
        name: 'register',
        component: registerComponent,
        beforeEnter: ifNotAuthenticated,
    },
    {
        path: '/login',
        name: 'login',
        component: loginComponent,
        beforeEnter: ifNotAuthenticated,
    },
    {
        path: '/bookings/new',
        name: 'bookings.new',
        component: bookingNewComponent,
        beforeEnter: ifAuthenticated,
    },
    {
        path: '/bookings',
        name: 'bookings.index',
        component: bookingIndexComponent,
        beforeEnter: ifAuthenticated,
    },
]

const router = new VueRouter({
    routes: routes
})

const app = new Vue({
    router: router,
    mounted: function() {
        state.authenticated = this.authenticated
    },
    components: {
        'clerk-nav': navComponent,
    },
    data: {
        token: localStorage.getItem('token') || '',
        flashSuccessData: "",
    },
    watch: {
        token: function(oldVal, newVal) {
            state.authenticated = this.authenticated
        },
    },
    computed: {
        // authenticated tells if the current user is authenticated.
        // @return boolean
        authenticated: function() {
            return (this.token) ? true : false;
        }
    },
    methods: {
        onToken: function(token) {
            this.token = token
            if (token) {
                localStorage.setItem('token', token)
            } else {
                localStorage.removeItem('token')
            }
        },
        onFlashSuccess: function(message) {
            this.flashSuccessData = message
        }
    }
}).$mount('#app')
