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
