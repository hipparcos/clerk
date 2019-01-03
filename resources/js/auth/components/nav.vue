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

export default {
    methods: {
        logout: function() {
            this.$store.dispatch(AUTH_LOGOUT)
                .then(function(response) {
                    this.$router.push('/', function() {
                        this.$emit('flash-success', 'You are now logged out.')
                    }.bind(this))
                }.bind(this))
                .catch(function (error) {
                    console.log("Fail to log out.")
                }.bind(this));
        }
    }
}
</script>
