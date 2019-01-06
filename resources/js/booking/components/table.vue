<template>
    <div>
        <errors-list
            :errors="errors"
            :list-errors="true"
            ></errors-list>
        <div class="field is-horizontal has-addons has-addons-centered">
            <p class="control">
                <a class="button is-static">
                    Date
                </a>
            </p>
            <p class="control">
                <date-picker type="date" lang="en" format="DD-MM-YYYY"
                    v-model="selectedDate"
                    ></date-picker>
            </p>
            <p class="control">
                <a class="button"
                    @click.prevent="onRefresh"
                    :class="{ 'is-loading': $store.getters.areBookingsLoading }">
                    <span class="icon"><i class="fas fa-sync-alt"></i></span>
                </a>
            </p>
        </div>
        <p v-if="$store.getters.areBookingsLoading" class="has-text-centered has-text-weight-bold">
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
                    @errors="onErrors"
                    @flash="onFlash"
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
import BookingTr from './table-row.vue'
import ErrorsList from '../../error/components/errors.vue'

import api from '../api.js'
import store from '../store.js'
import { BOOKINGS_SET_DATE, BOOKINGS_REQUEST, BOOKINGS_SORT, } from '../actions.js'

const toMoment = (year, month, day) => {
    if (year && month && day) {
        return moment(`${year}-${month}-${day}`)
    }
    return moment()
}

export default {
    components: { BookingTr, DatePicker, ErrorsList, },
    props: [ 'year', 'month', 'day' ],
    created: function() {
        // Sorters
        this.roomSorter = (l, r) => {
            let lroom = l.room.name
            let rroom = r.room.name
            return lroom.localeCompare(rroom)
        }
        this.userSorter = (l, r) => {
            let luser = l.user.name
            let ruser = r.user.name
            return luser.localeCompare(ruser)
        }
        this.timeSorter = (l, r) => {
            return l.start.isBefore(r.start) ? -1 : 1
        }
        // Set selected date from props.
        this.selectedDate = toMoment(this.year, this.month, this.day)
    },
    data: function() {
        return {
            sorter: this.timeSorter,
            errors: new api.BookingError({}),
        }
    },
    computed: {
        bookings: function() {
            return this.$store.getters.getBookings
        },
        selectedDate: {
            get: function() {
                return this.$store.getters.getSelectedDate
            },
            set: function(d) {
                let self = this
                let date = moment(d)
                this.$store.dispatch(BOOKINGS_SET_DATE, { date })
                    .then(bookings => {
                        self.sortBookings()
                    })
                    .catch(err => {
                        console.log(err)
                    })
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
    },
    methods: {
        sortBookings: function(sorter) {
            this.sorter = sorter || this.sorter || this.timeSorter
            this.$store.dispatch(BOOKINGS_SORT, { sorter: this.sorter })
                .catch(err => console.log(err))
        },
        onErrors: function(errors) {
            this.$set(this.$data, 'errors', errors)
        },
        onFlash: function(flash) {
            this.$emit('flash', flash)
        },
        onRefresh: function() {
            this.$store.dispatch(BOOKINGS_REQUEST)
        },
    },
}
</script>

<style>
.has-addons .mx-input {
    border-radius: 0;
}
</style>
