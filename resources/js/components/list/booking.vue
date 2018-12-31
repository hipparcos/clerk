<template>
    <table class="table">
        <thead>
            <tr>
                <th>Room</th>
                <th>Start</th>
                <th>Duration</th>
                <th>User</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <booking-tr
                v-for="booking in bookings"
                :key="booking.id"
                :booking="booking"
                >
            </booking-tr>
        </tbody>
    </table>
</template>

<script>
import axios from 'axios'
import bookingTrComponent from './booking-tr.vue'

export default {
    components: { 'booking-tr': bookingTrComponent },
    props: {
        token: String,
    },
    created: function() {
        this.getBookings()
    },
    data: function() {
        return {
            bookings: [],
        }
    },
    methods: {
        getBookings: function() {
            axios({
                method: 'get',
                url: '/api/bookings',
                headers: {
                    'Authorization': 'Bearer ' + this.token,
                },
            })
                .then(function(response) {
                    this.bookings = response.data.data
                }.bind(this))
                .catch(function(error) {
                    this.$emit('flash-error', 'Error getting bookings: <br>'
                        + error.response.status + ' ' + error.response.data
                        )
                }.bind(this))
        },
    },
}
</script>
