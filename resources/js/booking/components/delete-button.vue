<template>
    <button-confirmed
        :classes="classes"
        classesConfirm="button is-danger is-outlined"
        @confirmed="remove">
        <template slot="title">
            Confirm booking deletion
        </template>

        <template slot="message">
            Do you really want to delete this booking?
            <booking-box :booking="booking"></booking-box>
        </template>

        <template slot="confirm">
            <span>Delete</span>
            <span class="icon is-small">
                <i class="fas fa-times"></i>
            </span>
        </template>

        <slot>
            <span>Delete</span>
            <span class="icon is-small">
                <i class="fas fa-times"></i>
            </span>
        </slot>
    </button-confirmed>
</template>

<script>
import ButtonConfirmed from '../../ui/button-confirmed.vue'
import BookingBox from './box.vue'

import { NOTIFICATIONS_PUSH } from '../../notification/actions.js'
import notif from '../../notification/lib.js'
import api from '../api.js'
import { BOOKINGS_DELETE } from '../actions.js'

export default {
    components: { ButtonConfirmed, BookingBox },
    props: {
        booking: {
            type: api.Booking,
            required: true,
        },
        classes: {
            type: String,
            required: false,
            default: 'button is-small is-danger is-outlined',
        },
    },
    methods: {
        remove: function() {
            this.$store.dispatch(BOOKINGS_DELETE, { id: this.booking.id })
                .then(function(id) {
                    this.$store.dispatch(NOTIFICATIONS_PUSH, new notif.Notification({
                        type: notif.Type.success,
                        message: 'Booking deleted.',
                    }))
                }.bind(this))
                .catch(function(err) {
                    console.log(err)
                })
        },
    },
}
</script>
