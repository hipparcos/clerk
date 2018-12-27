<template>
    <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                <i class="fas fa-building"></i>&nbsp;{{ app }}
            </a>
        </div>

        <div id="navbar" class="navbar-menu">
            <div class="navbar-start">
                <a class="navbar-item">
                    Home
                </a>
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
