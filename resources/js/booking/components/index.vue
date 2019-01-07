<template>
    <div>
        <!-- errors -->
        <errors-list
            :errors="errors"
            :list-errors="true"
            ></errors-list>
        <!-- date filtering -->
        <div class="field is-horizontal has-addons has-addons-centered">
            <p class="control">
                <a class="button is-static">
                    Date
                </a>
            </p>
            <p class="control">
                <date-picker type="date" lang="en" format="DD-MM-YYYY" :clearable="false"
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
        <!-- tabs -->
        <div class="tabs is-centered">
            <ul>
                <li :class="{ 'is-active': component == 'booking-table' }">
                    <a @click.prevent="component = 'booking-table'">
                        <span class="icon is-small">
                            <i class="fas fa-list-ul" aria-hidden="true"></i>
                        </span>
                        <span>List view</span>
                    </a>
                </li>
                <li :class="{ 'is-active': component == 'booking-agenda' }">
                    <a @click.prevent="component = 'booking-agenda'">
                        <span class="icon is-small">
                            <i class="fas fa-calendar" aria-hidden="true"></i>
                        </span>
                        <span>Agenda view</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- list of bookings -->
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
import ErrorsList from '../../error/components/errors.vue'
import BookingTable from './table.vue'
import BookingAgenda from './agenda.vue'

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
    components: { BookingTable, BookingAgenda, DatePicker, ErrorsList },
    props: {
        year: {
            required: false,
        },
        month: {
            required: false,
        },
        day: {
            required: false,
        },
        agenda: {
            type: Boolean,
            default: false,
            required: false,
        },
    },
    created: function() {
        // Set selected date from props.
        this.selectedDate = toMoment(this.year, this.month, this.day)
    },
    data: function() {
        let component = 'booking-table'
        if (this.agenda) {
            component = 'booking-agenda'
        }
        return {
            component: component,
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
