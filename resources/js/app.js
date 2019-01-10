import axios from 'axios'
import Vue from 'vue'

import router from './router.js'
import store from './store.js'
import { USER_REQUEST } from './user/actions.js'

import Navigation from './nav.vue'
import Notifications from './notification/components/notifications.vue'

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
        }
    },
    components: { Navigation, Notifications },
}).$mount('#app')
