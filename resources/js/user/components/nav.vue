<template>
    <div class="navbar-item">
        <strong>{{ username }}</strong>&nbsp;
    </div>
</template>

<script>
export default {
    computed: {
        username: function() {
            if (this.$store.getters.isProfileLoaded) {
                return this.$store.getters.getProfile.attributes.name
            } else {
                return ""
            }
        }
    },
}
</script>
