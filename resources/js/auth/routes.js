import store from './store.js'

import LoginComponent    from './components/login.vue'

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
        path: '/login',
        name: 'login',
        component: LoginComponent,
        beforeEnter: ifNotAuthenticated,
        meta: {
            displayName: 'Log in',
        },
    },
]

export default {
    routes,
    ifAuthenticated,
    ifNotAuthenticated,
}
