import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)

import auth from './components/auth/store.js'
import user from './components/user/store.js'

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
    modules: {
        auth,
        user,
    },
    strict: debug,
})
