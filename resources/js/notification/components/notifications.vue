<template>
    <aside class="notifications" v-if="hasNotifications">
        <notification
            v-for="notification in notifications"
            v-bind:key="notification.id"
            v-bind="notification"></notification>
    </aside>
</template>

<script>
import Notification from './notification.vue'
import lib from '../lib.js'

export default {
    components: { Notification },
    computed: {
        notifications() {
            return this.$store.getters.getNotifications
        },
        hasNotifications() {
            return this.$store.getters.hasNotifications
        },
    },
}
</script>

<style>
.notifications {
    position: fixed;
    z-index: 30;
    top: 3.5em; /* Positioned under navbar. */
    right: 0;
    min-width: 20em;
    padding-right: 0.75em; /* Aligned with navbar-item button. */
}
</style>
