<template>
    <form class="register-form">
        <div class="field">
            <label class="label">Name</label>
            <div class="control">
                <input class="input" type="text" placeholder="Name"
                    id="register.name">
            </div>
        </div>
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" placeholder="Email"
                    id="register.email">
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" placeholder="Password"
                    id="register-password">
            </div>
        </div>
        <div class="field">
            <label class="label">Password confirmation</label>
            <div class="control">
                <input class="input" type="password" placeholder="Password confirmation"
                    id="register-confirmation">
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button class="button is-link">Register</button>
            </div>
            <div class="control">
                <button class="button is-text" @click="clear">Clear</button>
            </div>
        </div>
    </form>
</template>

<script>
export default {
}
</script>

<style>
.register-form {
    max-width: 20em;
}
</style>
