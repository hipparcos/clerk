import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)

import auth from './auth/store.js'
import user from './user/store.js'
import room from './room/store.js'
import booking from './booking/store.js'

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
    modules: {
        auth,
        user,
        room,
        booking,
    },
    strict: debug,
})
