import axios from 'axios'
import Vue from 'vue'

import router from './router.js'
import store from './store.js'
import { USER_REQUEST } from './user/actions.js'
import { ROOMS_REQUEST } from './room/actions.js'

import NavComponent from './nav.vue'

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
