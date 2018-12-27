<template>
    <form class="register-form">
        <div v-if="errors.message" class="notification is-danger">
            <button class="delete" @click="errors.message = ''"></button>
            <h6 class="title is-6">{{ errors.message }}</h6>
        </div>
        <div class="field">
            <label class="label">Name</label>
            <div class="control">
                <input class="input" type="text" placeholder="Name"
                    v-model="name"
                    v-bind:class="{ 'is-danger': errors.fields.name.length > 0 }">
                <ul v-if="errors.fields.name.length > 0" class="help is-danger">
                    <li v-for="err in errors.fields.name">{{ err }}</li>
                </ul>
            </div>
        </div>
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" placeholder="Email"
                    v-model="email"
                    v-bind:class="{ 'is-danger': errors.fields.email.length > 0 }">
                <ul v-if="errors.fields.email.length > 0" class="help is-danger">
                    <li v-for="err in errors.fields.email">{{ err }}</li>
                </ul>
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" placeholder="Password"
                    v-model="password"
                    v-bind:class="{ 'is-danger': errors.fields.password.length > 0 }">
                <ul v-if="errors.fields.password.length > 0" class="help is-danger">
                    <li v-for="err in errors.fields.password">{{ err }}</li>
                </ul>
            </div>
        </div>
        <div class="field">
            <label class="label">Password confirmation</label>
            <div class="control">
                <input class="input" type="password" placeholder="Password confirmation"
                    v-model="password_confirmation"
                    v-bind:class="{ 'is-danger': errors.fields.password_confirmation.length > 0 }">
                <ul v-if="errors.fields.password_confirmation.length > 0" class="help is-danger">
                    <li v-for="err in errors.fields.password_confirmation">{{ err }}</li>
                </ul>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button class="button is-link" @click="submit">Register</button>
            </div>
            <div class="control">
                <button class="button is-text" @click="clear">Clear</button>
            </div>
        </div>
    </form>
</template>

<script>
import axios from 'axios'

export default {
    data: function() {
        return {
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            errors: {
                message: "",
                fields: {
                    name: [],
                    email: [],
                    password: [],
                    password_confirmation: [],
                },
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
                url: '/api/register',
                data: {
                    data: {
                        type: "user",
                        attributes: {
                            name: this.name,
                            email: this.email,
                            password: this.password,
                            password_confirmation: this.password_confirmation,
                        }
                    }
                }
            })
            .then(function (response) {
                this.clear()
                //TODO login user.
                this.$router.push('/', function() {
                    this.$emit('flash-success', 'Registration completed.')
                }.bind(this))
            }.bind(this))
            .catch(function (error) {
                let data = error.response.data
                this.errors.message
                    = data.message
                if ('data.attributes.name' in data.errors) {
                    this.errors.fields.name
                        = data.errors['data.attributes.name']
                }
                if ('data.attributes.email' in data.errors) {
                    this.errors.fields.email
                        = data.errors['data.attributes.email']
                }
                if ('data.attributes.password' in data.errors) {
                    this.errors.fields.password
                        = data.errors['data.attributes.password']
                }
                if ('data.attributes.password_confirmation' in data.errors) {
                    this.errors.fields.password_confirmation
                        = data.errors['data.attributes.password_confirmation']
                }
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
            this.errors.message = ""
            this.errors.fields.name = []
            this.errors.fields.email = []
            this.errors.fields.password = []
            this.errors.fields.password_confirmation = []
        }
    }
}
</script>

<style>
.register-form {
    max-width: 20em;
}
</style>
