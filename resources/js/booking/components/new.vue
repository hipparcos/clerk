<template>
    <form class="booking-form">
        <errors-list
            :errors="errors"
            :list-errors="true"
            ></errors-list>
        <div class="message is-link">
        <div class="message-header">
            <p v-if="editMode">Edit a booking</p>
            <p v-else>Book a room</p>
            <button class="delete" aria-label="delete"
                @click.prevent="close"></button>
        </div>
        <div class="message-body">
        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">Date</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <div class="control">
                        <date-picker type="datetime" lang="en" format="DD-MM-YYYY H:mm"
                            v-bind:class="{ 'is-danger': errors.hasErrors('start') }"
                            :clearable="false"
                            :time-picker-options="startTimePickerOptions"
                            :not-before="today"
                            v-model="start"
                            ></date-picker>
                    </div>
                </div>
            </div>
        </div>
        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">Room</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <div class="control">
                        <div class="select"
                            v-bind:class="{ 'is-danger': errors.hasErrors('room') }"
                            >
                            <room-select v-model="room"></room-select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">Duration</label>
            </div>
            <div class="field-body">
                <div class="field has-addons">
                    <p class="control">
                        <input class="input" type="text"
                            maxlength="4" style="max-width: 4em;"
                            v-model="duration"
                            v-bind:class="{ 'is-danger': errors.hasErrors('duration') }">
                    </p>
                    <p class="control">
                        <a class="button is-static">minutes</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="field is-horizontal">
            <div class="field-label is-normal">
            </div>
            <div class="field-body">
                <div class="field is-grouped">
                    <div class="control">
                        <button
                            class="button"
                            :class="submitClass"
                            :disabled="submitState"
                            @click.prevent="submit">
                            <span v-if="editMode">Edit</span>
                            <span v-else>Book</span>
                        </button>
                    </div>
                    <div class="control">
                        <button class="button is-text" @click.prevent="clear">Clear</button>
                    </div>
                </div>
            </div>
        </div>
        </div> <!-- .message-body -->
        </div> <!-- .message -->
    </form>
</template>

<script>
import _ from 'lodash'
import moment from 'moment'
import DatePicker from 'vue2-datepicker'

import ErrorsList from '../../error/components/errors.vue'
import RoomSelect from '../../room/components/select.vue'

import { BOOKINGS_CREATE, BOOKINGS_UPDATE, BOOKINGS_SET_DATE } from '../actions.js'
import api from '../api.js'
import room from '../../room/api.js'
import lib from '../lib.js'

export default {
    components: { DatePicker, ErrorsList, RoomSelect },
    props: {
        id: {
            required: false,
        },
    },
    created: function() {
        // Force set to trigger update.
        this.start = this.startData

        this.updateBookingData = function(id) {
            // Assume the booking is already loaded for now.
            id = Number(id)
            let booking = this.$store.state.booking.bookings.find(b => b.id == id)
            if (booking) {
                this.idData = id
                this.room = booking.room.id
                this.startData = booking.start
                this.duration = booking.duration
            } else {
                this.clear()
            }
        }
    },
    watch: {
        id: function(newId) {
            this.updateBookingData(newId)
        },
    },
    beforeRouteEnter: function(to, from, next) {
        // Needed to display initial data when editing.
        next(vm => {
            vm.updateBookingData(to.params.id)
        })
    },
    data: function() {
        return {
            idData: 0,
            room: 0,
            roomCausingConflict: 0,
            startData: lib.nextSlot(),
            durationData: lib.slot,
            override: false,
            errors: new api.BookingError({}),
            // DatePicker
            startTimePickerOptions:{
                start: '08:00',
                step: '00:15',
                end: '18:00'
            }
        }
    },
    computed: {
        editMode: function() {
            return !!this.idData
        },
        start: {
            get: function() {
                return this.startData
            },
            set: function(newStart) {
                this.startData = moment(newStart)
                this.$store.dispatch(BOOKINGS_SET_DATE, { date: this.startData })
            },
        },
        duration: {
            get: function() {
                return this.durationData
            },
            set: function(value) {
                value = Math.round(value)
                if (value > 0) {
                    this.durationData = value
                } else {
                    this.durationData = 0
                }
            }
        },
        today: function() {
            return moment().startOf('day')
        },
        submitClass: function() {
            if (this.errors.conflict()) {
                if (!this.errors.isOverridable()
                    && this.room == this.roomCausingConflict) {
                    return { 'is-danger': true }
                }
                return { 'is-warning': true }
            }
            return { 'is-link': true }
        },
        submitState: function() {
            return this.errors.conflict() && !this.errors.isOverridable()
                && this.room == this.roomCausingConflict
        },
        thisDayBookingsURL: function() {
            return moment(this.start).format('[/bookings]/YYYY/MM/DD')
        },
    },
    methods: {
        submit: function() {
            this.clearErrors()
            let booking = new api.Booking({
                id: this.idData,
                start: this.start,
                duration: this.duration,
                room: new room.Room({
                    id: this.room,
                })
            })
            let action = BOOKINGS_CREATE
            // If editing.
            if (this.editMode) {
                action = BOOKINGS_UPDATE
            }
            this.$store.dispatch(action, { booking, override: this.override })
                .then(function (booking) {
                    this.$router.push(this.thisDayBookingsURL, function() {
                        if (this.editMode) {
                            this.$emit('flash', {
                                type: 'success',
                                message: 'Booking updated.',
                            })
                        } else {
                            this.$emit('flash', {
                                type: 'success',
                                message: 'Booking created.',
                            })
                        }
                    }.bind(this))
                }.bind(this))
                .catch(function (error) {
                    this.$set(this.$data, 'errors', error)
                    // If conflict, try to override.
                    if (error.conflict()) {
                        if (error.isOverridable()) {
                            this.override = true
                        } else {
                            this.roomCausingConflict = booking.room.id
                        }
                    }
                }.bind(this));
        },
        clear: function() {
            this.isData = 0
            this.room = 0
            this.roomCausingConflict = 0
            this.start = lib.nextSlot()
            this.duration = lib.slot
            this.override = false
            this.clearErrors()
        },
        clearErrors: function() {
            this.$set(this.$data, 'errors', new api.BookingError({}))
        },
        close: function() {
            this.$router.push(this.thisDayBookingsURL)
        },
    }
}
</script>

<style>
.booking-form {
    max-width: 26em;
    margin-bottom: 1em;
}

.booking-form .field-body {
    max-width: 16em;
}

.booking-form .field-body div.select {
   width: 14em;
}
.booking-form .field-body div.select select {
    width: 14em;
    padding-right: 2.5em;
}
.booking-form .field-body div.select::after {
    right: 0.75em;
}

.booking-form .field-body .mx-datepicker {
    width: 16em;
}
.booking-form .field-body .mx-input {
    width: 14em;
    padding-right: 30px;
}
</style>
