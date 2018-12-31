import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import NavComponent          from './components/nav.vue'
import AuthRegisterComponent from './components/auth/form/register.vue'
import AuthLoginComponent    from './components/auth/form/login.vue'
import BookingIndexComponent from './components/booking/table.vue'
import BookingNewComponent   from './components/booking/new.vue'

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
        component: AuthRegisterComponent,
        beforeEnter: ifNotAuthenticated,
        meta: {
            displayName: 'Register',
        },
    },
    {
        path: '/login',
        name: 'login',
        component: AuthLoginComponent,
        beforeEnter: ifNotAuthenticated,
        meta: {
            displayName: 'Log in',
        },
    },
    {
        path: '/bookings/new',
        name: 'bookings.new',
        component: BookingNewComponent,
        beforeEnter: ifAuthenticated,
        meta: {
            displayName: 'Book a room',
        },
    },
    {
        path: '/bookings',
        name: 'bookings.index',
        component: BookingIndexComponent,
        beforeEnter: ifAuthenticated,
        meta: {
            displayName: 'View bookings',
        },
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
        'clerk-nav': NavComponent,
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
