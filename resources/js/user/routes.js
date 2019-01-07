import store from './store.js'
import auth from '../auth/routes.js'

import RegisterComponent from './components/register.vue'

const routes = [
    {
        path: '/register',
        name: 'register',
        component: RegisterComponent,
        beforeEnter: auth.ifNotAuthenticated,
        meta: {
            displayName: 'Register',
        },
    },
]

export default {
    routes,
}
