<template>
    <table class="table">
        <thead>
            <tr>
                <th><a @click.prevent="sortBookings(roomSorter)">
                    Room
                    <span v-if="sorter == roomSorter" class="icon"><i class="fas fa-chevron-circle-down"></i></span>
                </a></th>
                <th><a @click.prevent="sortBookings(timeSorter)">
                    Start
                    <span v-if="sorter == timeSorter" class="icon"><i class="fas fa-chevron-circle-down"></i></span>
                </a></th>
                <th>Duration</th>
                <th><a @click.prevent="sortBookings(userSorter)">
                    User
                    <span v-if="sorter == userSorter" class="icon"><i class="fas fa-chevron-circle-down"></i></span>
                </a></th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <booking-tr
                v-for="booking in bookings"
                :key="booking.id"
                :booking="booking"
                @errors="onErrors"
                >
            </booking-tr>
        </tbody>
    </table>
</template>

<script>
import BookingTr from './table-row.vue'

import api from '../api.js'
import lib from '../lib.js'
import { BOOKINGS_SET_DATE, BOOKINGS_REQUEST, BOOKINGS_SORT, } from '../actions.js'

export default {
    components: { BookingTr },
    props: {
        bookings: {
            type: Array,
            required: true,
            $each: {
                type: api.Booking,
            },
        },
    },
    created: function() {
        this.roomSorter = lib.roomSorter
        this.userSorter = lib.userSorter
        this.timeSorter = lib.timeSorter
    },
    computed: {
        sorter: function() {
            return this.$store.state.booking.sorter
        },
    },
    methods: {
        sortBookings: function(sorter) {
            this.$store.dispatch(BOOKINGS_SORT, { sorter })
                .catch(err => console.log(err))
        },
        onErrors: function(errors) {
            this.$emit('errors', errors)
        },
    },
}
</script>
