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
                    :initialBooking="booking"
                    @booking-update="onUpdate"
                    @booking-delete="onDelete"
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
import axios from 'axios'
import moment from 'moment'
import DatePicker from 'vue2-datepicker'
import BookingTrComponent from './table-row.vue'

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
            let lroom = l.relationships.room.data.attributes.name
            let rroom = r.relationships.room.data.attributes.name
            return lroom > rroom
        }
        this.userSorter = (l, r) => {
            let luser = l.relationships.user.data.attributes.name
            let ruser = r.relationships.user.data.attributes.name
            return luser > ruser
        }
        this.timeSorter = (l, r) => {
            return r.attributes.start.isBefore(l.attributes.start)
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
            axios({
                method: 'get',
                url: '/api/bookings/' +
                    this.selectedDate.format('YYYY/MM/DD'),
            })
                .then(function(response) {
                    this.bookings = response.data.data
                    this.bookings.map(b => b.attributes.start = moment(b.attributes.start))
                    this.sortBookings(this.timeSorter)
                    this.status = "success"
                }.bind(this))
                .catch(function(error) {
                    this.$emit('flash-error', 'Error getting bookings: <br>'
                        + error.response.status + ' ' + error.response.data
                        )
                    this.status = "error"
                }.bind(this))
        },
        sortBookings: function(sorter) {
            this.sorter = sorter || this.sorter || this.timeSorter
            this.bookings.sort(this.sorter)
        },
        onUpdate: function(booking) {
            let idx = this.bookings.findIndex(b => b.id == booking.id)
            let newStart = moment(this.bookings[idx].attributes.start)
            // If the date has been changed, remove the booking.
            let today = this.date.clone()
            if (newStart.isBefore(today.startOf('day')) || newStart.isAfter(today.endOf('day'))) {
                this.onDelete(booking)
            } else {
                booking.attributes.start = moment(booking.attributes.start)
                this.bookings[idx] = booking
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
