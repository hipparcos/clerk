<template>
    <form class="register-form">
        <errors-list :errors="errors">
            <p v-if="errors.hint()"><strong>Hint:</strong> {{ errors.hint() }}</p>
        </errors-list>
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" placeholder="Email"
                    v-model="email"
                    >
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" placeholder="Password"
                    v-model="password"
                    >
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button class="button is-link" @click.prevent="submit">Log in</button>
            </div>
        </div>
    </form>
</template>

<script>
import api from '../api.js'
import { AUTH_REQUEST } from '../actions.js'

import ErrorsList from '../../error/components/errors.vue'

export default {
    components: { ErrorsList, },
    data: function() {
        return {
            email: "",
            password: "",
            errors: new api.AuthError({}),
        }
    },
    methods: {
        submit: function() {
            // Clear.
            this.clearErrors()
            // Request.
            let { email, password } = this
            this.$store.dispatch(AUTH_REQUEST, { email, password })
                .then(function(token) {
                    this.clear()
                    this.$router.push('/', function() {
                        this.$emit('flash', {
                            type: 'success',
                            message: 'You are now logged in.'
                        })
                    }.bind(this))
                }.bind(this))
                .catch(function (error) {
                    this.$set(this.$data, 'errors', error)
                }.bind(this));
        },
        clear: function() {
            this.email = ""
            this.password = ""
            this.clearErrors()
        },
        clearErrors: function() {
            this.$set(this.$data, 'errors', new api.AuthError({}))
        }
    }
}
</script>

<style>
.register-form {
    max-width: 20em;
}
</style>
