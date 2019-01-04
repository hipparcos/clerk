import _ from 'lodash'
import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

const root = { template: '<p></p>' }

const routes = [
    {
        path: '/',
        name: 'root',
        component: root
    },
]

import auth from './auth/routes.js'
import user from './user/routes.js'
import booking from './booking/routes.js'

export default new VueRouter({
    mode: 'history',
    routes: _.flatten([
        routes,
        auth.routes,
        user.routes,
        booking.routes,
    ]),
})
