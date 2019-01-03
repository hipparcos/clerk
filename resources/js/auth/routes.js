import store from './store.js'

import RegisterComponent from './components/register.vue'
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
        path: '/register',
        name: 'register',
        component: RegisterComponent,
        beforeEnter: ifNotAuthenticated,
        meta: {
            displayName: 'Register',
        },
    },
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
