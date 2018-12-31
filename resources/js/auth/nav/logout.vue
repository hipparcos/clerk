<template>
    <div class="navbar-item">
        <a class="button is-light" @click="logout">
            Log out
        </a>
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
