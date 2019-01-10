<template>
    <form class="register-form">
        <errors-list
            :errors="errors"
            ></errors-list>
        <div class="field">
            <label class="label">Name</label>
            <div class="control">
                <input class="input" type="text" placeholder="Name"
                    v-model="name"
                    v-bind:class="{ 'is-danger': errors.hasErrors('name')}">
                <errors-for-field
                    :errors="errors"
                    field="name"
                    ></errors-for-field>
            </div>
        </div>
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" placeholder="Email"
                    v-model="email"
                    v-bind:class="{ 'is-danger': errors.hasErrors('email') }">
                <errors-for-field
                    :errors="errors"
                    field="email"
                    ></errors-for-field>
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" placeholder="Password"
                    v-model="password"
                    v-bind:class="{ 'is-danger': errors.hasErrors('password') }">
                <errors-for-field
                    :errors="errors"
                    field="password"
                    ></errors-for-field>
            </div>
        </div>
        <div class="field">
            <label class="label">Password confirmation</label>
            <div class="control">
                <input class="input" type="password" placeholder="Password confirmation"
                    v-model="password_confirmation"
                    v-bind:class="{ 'is-danger': errors.hasErrors('password_confirmation')}">
                <errors-for-field
                    :errors="errors"
                    field="password_confirmation"
                    ></errors-for-field>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button class="button is-link" @click.prevent="submit">Register</button>
            </div>
            <div class="control">
                <button class="button is-text" @click.prevent="clear">Clear</button>
            </div>
        </div>
    </form>
</template>

<script>
import api from './../api.js'
import { AUTH_REQUEST } from '../../auth/actions.js'
import { NOTIFICATIONS_PUSH } from '../../notification/actions.js'
import notif from '../../notification/lib.js'

import ErrorsList from '../../error/components/errors.vue'
import ErrorsForField from '../../error/components/errorsForField.vue'

export default {
    components: { ErrorsList, ErrorsForField, },
    data: function() {
        return {
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            errors: new api.UserError({}),
        }
    },
    methods: {
        submit: function() {
            this.clearErrors()
            api.register(this.name, this.email, this.password, this.password_confirmation)
                .then(function (user) {
                    let { email, password } = this
                    // Log in.
                    this.$store.dispatch(AUTH_REQUEST, { email, password })
                        .then(function(token) {
                            this.clear()
                            this.$store.dispatch(NOTIFICATIONS_PUSH, new notif.Notification({
                                type: notif.Type.success,
                                message: 'Registration completed.',
                            }))
                            this.$router.push('/')
                        }.bind(this))
                        .catch(function (error) {
                            this.$store.dispatch(NOTIFICATIONS_PUSH, new notif.Notification({
                                type: notif.Type.error,
                                message: 'Fail to log in after registration.',
                            }))
                        }.bind(this));
                }.bind(this))
                .catch(function (error) {
                    this.$set(this.$data, 'errors', error)
                }.bind(this));
        },
        clear: function() {
            this.name = ""
            this.email = ""
            this.password = ""
            this.password_confirmation = ""
            this.clearErrors()
        },
        clearErrors: function() {
            this.$set(this.$data, 'errors', new api.UserError({}))
        }
    }
}
</script>

<style>
.register-form {
    max-width: 20em;
}
</style>
