<template>
    <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <router-link to="/" class="navbar-item">
                <i class="fas fa-building"></i>&nbsp;{{ app }}
            </router-link>
        </div>

        <div id="navbar" class="navbar-menu">
            <div class="navbar-start">
                <router-link to="/" class="navbar-item">
                    Home
                </router-link>
            </div>

            <div class="navbar-end">
                <template v-if="token">
                    <logout></logout>
                </template>
                <template v-else>
                    <login></login>
                </template>
            </div>
        </div>
    </nav>
</template>

<script>
import LoginComponent from './nav/login.vue'
import LogoutComponent from './nav/logout.vue'

export default {
    props: {
        app: String,
        token: String
    },
    components: {
        login: LoginComponent,
        logout: LogoutComponent
    },
}
</script>
