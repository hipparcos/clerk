<template>
    <form class="register-form">
        <div v-if="errors.message" class="notification is-danger">
            <button class="delete" @click="errors.message = ''"></button>
            <p class="strong">{{ errors.message }}</p>
            <p v-if="errors.hint"><strong>Hint:</strong> {{ errors.hint }}</p>
        </div>
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
                <button class="button is-link" @click="submit">Log in</button>
            </div>
        </div>
    </form>
</template>

<script>
import axios from 'axios'

export default {
    data: function() {
        return {
            email: "",
            password: "",
            errors: {
                message: "",
                hint: "",
            },
        }
    },
    methods: {
        submit: function() {
            // Clear.
            this.clearErrors()
            // Request.
            axios({
                method: 'post',
                url: '/oauth/token',
                data: {
                    grant_type: "password",
                    client_id: 1,
                    client_secret: "DvYKWsQPGXUPrRH41PsHtrMgtMMwfalJ0BjsoVhF",
                    username: this.email,
                    password: this.password,
                    scope: "*"
                }
            })
            .then(function (response) {
                this.clear()
                this.$router.push('/', function() {
                    this.$emit('token', response.data.access_token)
                    this.$emit('flash-success', 'You are now logged in.')
                }.bind(this))
            }.bind(this))
            .catch(function (error) {
                let data = error.response.data
                this.errors.message = data.message
                this.errors.hint = data.hint.replace('username', 'email')
            }.bind(this));
        },
        clear: function() {
            this.email = ""
            this.password = ""
            this.clearErrors()
        },
        clearErrors: function() {
            this.errors.message = ""
        }
    }
}
</script>

<style>
.register-form {
    max-width: 20em;
}
</style>
