import _ from 'lodash'
import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import auth from './auth/routes.js'
import user from './user/routes.js'
import booking from './booking/routes.js'

import authStore from './auth/store.js'

const routes = [
    {
        path: '/',
        beforeEnter: function(to, from, next) {
            if (authStore.getters.isAuthenticated(authStore.state)) {
                next('/bookings/agenda')
                return
            }
            next('/login')
        },
    },
]

export default new VueRouter({
    mode: 'history',
    routes: _.flatten([
        routes,
        auth.routes,
        user.routes,
        booking.routes,
    ]),
})
