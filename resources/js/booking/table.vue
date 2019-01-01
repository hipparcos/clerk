<template>
    <div>
        <div v-if="errors.message" class="notification is-danger">
            <button class="delete" @click.prevent="errors.message = ''"></button>
            <h6 class="title is-6">{{ errors.message }}</h6>
            <div v-if="hasErrors" class="content">
                <ul>
                    <template v-for="f in errors.fields">
                        <li v-for="err in f">{{ err }}</li>
                    </template>
                </ul>
            </div>
        </div>
        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">Date</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <p class="control">
                        <date-picker type="date" lang="en" format="DD-MM-YYYY"
                            v-model="selectedDate"
                            ></date-picker>
                    </p>
                </div>
            </div>
        </div>
        <table class="table container">
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
                    @booking-update="onUpdate"
                    @booking-delete="onDelete"
                    @errors="onErrors"
                    >
                </booking-tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import _ from 'lodash'
import axios from 'axios'
import moment from 'moment'
import DatePicker from 'vue2-datepicker'
import BookingTrComponent from './table-row.vue'

export default {
    components: {
        'booking-tr': BookingTrComponent,
        DatePicker,
    },
    props: {
    },
    created: function() {
        this.getBookings()
        this.debounceGetBookings = _.debounce(this.getBookings, 500)
    },
    data: function() {
        return {
            date: moment(),
            bookings: [],
            errors: {
                message: "",
                fields: {},
            },
        }
    },
    computed: {
        selectedDate: {
            get: function() {
                return this.date
            },
            set: function(d) {
                this.date = moment(d)
            },
        },
        hasErrors: function() {
            return Object.values(this.errors.fields)
                .reduce(function(acc, f) { return acc + f.length }, 0) > 0
        },
    },
    watch: {
        date: function() {
            this.debounceGetBookings()
        },
    },
    methods: {
        getBookings: function() {
            axios({
                method: 'get',
                url: '/api/bookings/' +
                    this.date.format('YYYY/MM/DD'),
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
        onUpdate: function(booking) {
            let idx = this.bookings.findIndex(b => b.id == booking.id)
            let newStart = moment(this.bookings[idx].attributes.start)
            // If the date has been changed, remove the booking.
            let today = this.date.clone()
            if (newStart.isBefore(today.startOf('day')) || newStart.isAfter(today.endOf('day'))) {
                this.onDelete(booking)
            } else {
                this.bookings[idx] = booking
            }
        },
        onDelete: function(booking) {
            this.bookings = this.bookings.filter(b => b.id != booking.id)
        },
        onErrors: function(errors) {
            this.errors.message = errors.message
            this.$set(this.errors, 'fields', errors.fields)
        }
    },
}
</script>
