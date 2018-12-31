<template>
    <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <router-link to="/" class="navbar-item">
                <i class="fas fa-building"></i>&nbsp;{{ app }}
            </router-link>
        </div>

        <div id="navbar" class="navbar-menu">
            <div class="navbar-start">
                <template v-if="$store.getters.isAuthenticated">
                    <booking></booking>
                </template>
                <h1 class="navbar-item" v-if="$route.meta.displayName">
                    <span class="icon">
                        <i class="fas fa-chevron-circle-right"></i>
                    </span>
                    <span>
                        <strong>{{ $route.meta.displayName }}</strong>
                    </span>
                </h1>
            </div>

            <div class="navbar-end">
                <template v-if="$store.getters.isAuthenticated">
                    <user-nav></user-nav>
                    <logout
                        v-on:flash-success="onFlashSuccess"
                        ></logout>
                </template>
                <template v-else>
                    <login></login>
                </template>
            </div>
        </div>
    </nav>
</template>

<script>
import AuthNavUserComponent   from './auth/nav/user.vue'
import AuthNavLoginComponent  from './auth/nav/login.vue'
import AuthNavLogoutComponent from './auth/nav/logout.vue'
import BookingNavComponent    from './booking/nav.vue'

export default {
    props: {
        app: String,
    },
    components: {
        'user-nav': AuthNavUserComponent,
        login: AuthNavLoginComponent,
        logout: AuthNavLogoutComponent,
        booking: BookingNavComponent,
    },
    methods: {
        onFlashSuccess: function(message) {
            this.$emit('flash-success', message)
        },
    }
}
</script>
