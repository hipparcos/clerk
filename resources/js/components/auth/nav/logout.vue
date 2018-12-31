<template>
    <div class="navbar-item">
        <div class="buttons">
            <a class="button is-light" @click="logout">
                Log out
            </a>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        logout: function() {
            this.$emit('token', '')
            this.$emit('flash-success', 'You are now logged out.')
            this.$router.push('/')
        }
    }
}
</script>
