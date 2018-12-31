import store from './store.js'

import AuthRegisterComponent from './form/register.vue'
import AuthLoginComponent    from './form/login.vue'

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
]

export default {
    routes,
    ifAuthenticated,
    ifNotAuthenticated,
}
