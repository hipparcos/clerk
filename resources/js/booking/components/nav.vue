<template>
    <div class="navbar-item">
        <div class="buttons">
            <router-link to="/bookings" class="button is-outlined">
                <span @click="onClickToday">View today's bookings</span>
            </router-link>
            <router-link to="/bookings/new" class="button is-link">
                <strong>Book a room</strong>
            </router-link>
        </div>
    </div>
</template>

<script>
import moment from 'moment'

import { BOOKINGS_SET_DATE } from '../actions.js'

export default {
    methods: {
        onClickToday: function() {
            let date = moment()
            this.$store.dispatch(BOOKINGS_SET_DATE, { date })
                .catch(err => {
                    console.log(err)
                })
        },
    },
}
</script>
