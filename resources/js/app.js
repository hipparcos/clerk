import axios from 'axios'
import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import store from './store.js'
import { USER_REQUEST } from './components/user/actions.js'
import { ROOMS_REQUEST } from './components/room/actions.js'

import NavComponent          from './components/nav.vue'
import AuthRegisterComponent from './components/auth/form/register.vue'
import AuthLoginComponent    from './components/auth/form/login.vue'
import BookingIndexComponent from './components/booking/table.vue'
import BookingNewComponent   from './components/booking/new.vue'

const root = { template: '<p></p>' }

const ifAuthenticated = function(to, from, next) {
    if (store.getters.isAuthenticated) {
        next()
        return
    }
    next('/login')
}
const ifNotAuthenticated = function(to, from, next) {
    if (!store.getters.ifAuthenticated) {
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
    mode: 'history',
    routes: routes
})

const app = new Vue({
    router: router,
    store: store,
    created: function () {
        axios.interceptors.response.use(undefined, function (err) {
            return new Promise(function (resolve, reject) {
                if (err.status === 401 && err.config && !err.config.__isRetryRequest) {
                    this.$store.dispatch(AUTH_LOGOUT)
                    this.$router.push('/login')
                }
                throw err;
            });
        });
        if (store.getters.isAuthenticated) {
            // Load user profile.
            store.dispatch(USER_REQUEST)
            // Load list of rooms.
            store.dispatch(ROOMS_REQUEST)
        }
    },
    components: {
        'clerk-nav': NavComponent,
    },
    data: {
        flashSuccessData: "",
    },
    methods: {
        onFlashSuccess: function(message) {
            this.flashSuccessData = message
            setTimeout(function() {
                this.flashSuccessData = ''
            }.bind(this), 5000);
        },
    }
}).$mount('#app')
