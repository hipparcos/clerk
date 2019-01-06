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
        <container v-else-if="bookings.length > 0" class="container"
            :is="component"
            :bookings="bookings"
            @errors="onErrors"
            ></container>
        <p v-else class="has-text-centered has-text-weight-bold">
            No bookings {{ prettySelectedDate }}.
        </p>
    </div>
</template>

<script>
import moment from 'moment'

import DatePicker from 'vue2-datepicker'
import BookingTable from './table.vue'
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
    components: { BookingTable, DatePicker, ErrorsList },
    props: [ 'year', 'month', 'day' ],
    created: function() {
        // Set selected date from props.
        this.selectedDate = toMoment(this.year, this.month, this.day)
    },
    data: function() {
        return {
            component: 'booking-table',
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
