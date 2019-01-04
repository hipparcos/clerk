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
        <p v-if="status == 'loading'" class="has-text-centered has-text-weight-bold">
            Loading bookings...
        </p>
        <table v-else-if="bookings.length > 0" class="table container">
            <thead>
                <tr>
                    <th><a @click.prevent="sortBookings(roomSorter)">Room</a></th>
                    <th><a @click.prevent="sortBookings(timeSorter)">Start</a></th>
                    <th>Duration</th>
                    <th><a @click.prevent="sortBookings(userSorter)">User</a></th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <booking-tr
                    v-for="booking in bookings"
                    :key="booking.id"
                    :booking="booking"
                    @update="onUpdate"
                    @delete="onDelete"
                    @errors="onErrors"
                    >
                </booking-tr>
            </tbody>
        </table>
        <p v-else class="has-text-centered has-text-weight-bold">
            No bookings {{ prettySelectedDate }}.
        </p>
    </div>
</template>

<script>
import _ from 'lodash'
import moment from 'moment'
import DatePicker from 'vue2-datepicker'
import BookingTrComponent from './table-row.vue'

import api from '../api.js'

const toMoment = (year, month, day) => {
    if (year && month && day) {
        return moment(`${year}-${month}-${day}`)
    }
    return moment()
}

export default {
    components: {
        'booking-tr': BookingTrComponent,
        DatePicker,
    },
    props: [ 'year', 'month', 'day' ],
    created: function() {
        this.getBookings()
        this.debounceGetBookings = _.debounce(this.getBookings, 500)
        // Sorters
        this.roomSorter = (l, r) => {
            let lroom = l.room.name
            let rroom = r.room.name
            return lroom > rroom
        }
        this.userSorter = (l, r) => {
            let luser = l.user.name
            let ruser = r.user.name
            return luser > ruser
        }
        this.timeSorter = (l, r) => {
            return r.start.isBefore(l.start)
        }
    },
    beforeRouteEnter: function(to, from, next) {
        next(vm => {
            vm.date = toMoment(vm.year, vm.month, vm.day),
            vm.debounceGetBookings()
        })
    },
    data: function() {
        return {
            date: toMoment(this.year, this.month, this.day),
            bookings: [],
            status: "loading",
            sorter: this.timeSorter,
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
        prettySelectedDate: function() {
            return moment(this.selectedDate).calendar(null,{
                lastDay : '[yesterday]',
                sameDay : '[today]',
                nextDay : '[tomorrow]',
                lastWeek : '[on last] dddd',
                nextWeek : '[on] dddd',
                sameElse : '[on the] DD/MM/YYYY'
            });
        },
        hasErrors: function() {
            return Object.values(this.errors.fields)
                .reduce(function(acc, f) { return acc + f.length }, 0) > 0
        },
    },
    watch: {
        date: function() {
            this.status = "loading"
            this.debounceGetBookings()
        },
    },
    methods: {
        getBookings: function() {
            this.status = "loading"
            api.all(this.selectedDate)
                .then(function(bookings) {
                    this.bookings = bookings
                    this.sortBookings(this.timeSorter)
                    this.status = "success"
                }.bind(this))
                .catch(function(error) {
                    console.log('Error getting bookings.')
                    this.status = "error"
                }.bind(this))
        },
        sortBookings: function(sorter) {
            this.sorter = sorter || this.sorter || this.timeSorter
            this.bookings.sort(this.sorter)
        },
        onUpdate: function(booking) {
            // Remove the booking.
            this.onDelete(booking)
            // If the booking is the same day as selectedDate, add it back to bookings.
            let today = this.selectedDate.clone()
            if (booking.start.isAfter(today.startOf('day'))
                    && booking.start.isBefore(today.endOf('day'))) {
                this.bookings.push(booking)
                this.sortBookings()
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
