<template>
    <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <router-link to="/" class="navbar-item">
                <i class="fas fa-building"></i>&nbsp;{{ app }}
            </router-link>

            <a role="button" class="navbar-burger burger"
                :class="{ 'is-active': hamburger }"
                @click.prevent="hamburger = !hamburger"
                aria-label="menu"
                aria-expanded="false"
                data-target="navbar">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbar" class="navbar-menu"
            :class="{ 'is-active': hamburger }"
            >
            <div class="navbar-start">
                <template v-if="$store.getters.isAuthenticated">
                    <booking-nav></booking-nav>
                </template>
                <!-- route name -->
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
                <user-nav
                    ></user-nav>
                <auth-nav
                    ></auth-nav>
            </div>
        </div>
    </nav>
</template>

<script>
import AuthNavComponent from './auth/components/nav.vue'
import UserNavComponent from './user/components/nav.vue'
import BookingNavComponent from './booking/components/nav.vue'

export default {
    props: {
        app: String,
    },
    data: function() {
        return {
            hamburger: false,
        }
    },
    components: {
        'auth-nav': AuthNavComponent,
        'user-nav': UserNavComponent,
        'booking-nav': BookingNavComponent,
    },
}
</script>
