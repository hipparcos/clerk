<template>
    <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <router-link to="/" class="navbar-item">
                <i class="fas fa-building"></i>&nbsp;{{ app }}
            </router-link>
        </div>

        <div id="navbar" class="navbar-menu">
            <div class="navbar-start">
                <template v-if="token">
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
                <template v-if="token">
                    <logout
                        v-on:token="onToken"
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
import AuthNavLoginComponent  from './auth/nav/login.vue'
import AuthNavLogoutComponent from './auth/nav/logout.vue'
import BookingNavComponent    from './booking/nav.vue'

export default {
    props: {
        app: String,
        token: String
    },
    components: {
        login: AuthNavLoginComponent,
        logout: AuthNavLogoutComponent,
        booking: BookingNavComponent,
    },
    methods: {
        onToken: function(token) {
            this.$emit('token', token)
        },
        onFlashSuccess: function(message) {
            this.$emit('flash-success', message)
        },
    }
}
</script>
