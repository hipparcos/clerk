<template>
    <div class="navbar-item">
        <!-- register/log in -->
        <div class="buttons" v-if="!$store.getters.isAuthenticated">
            <router-link to="/register" class="button is-primary">
                <strong>Register</strong>
            </router-link>
            <router-link to="/login" class="button is-light">
                Log in
            </router-link>
        </div>
        <!-- log out -->
        <div class="buttons" v-else>
            <a class="button is-light" @click="logout">
                Log out
            </a>
        </div>
    </div>
</template>

<script>
import { AUTH_LOGOUT } from '../actions.js'
import { NOTIFICATIONS_PUSH } from '../../notification/actions.js'
import notif from '../../notification/lib.js'

export default {
    methods: {
        logout: function() {
            this.$store.dispatch(AUTH_LOGOUT)
                .then(function(response) {
                    this.$store.dispatch(NOTIFICATIONS_PUSH, new notif.Notification({
                        type: notif.Type.success,
                        message: 'You are now logged out.',
                    }))
                    this.$router.push('/')
                }.bind(this))
                .catch(function (error) {
                    this.$store.dispatch(NOTIFICATIONS_PUSH, new notif.Notification({
                        type: notif.Type.error,
                        message: 'Fail to log out.',
                    }))
                }.bind(this));
        }
    }
}
</script>
