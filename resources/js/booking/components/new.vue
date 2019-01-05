<template>
    <form class="booking-form">
        <errors-list
            :errors="errors"
            :list-errors="true"
            ></errors-list>
        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">Room</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <div class="control">
                        <div class="select is-fullwidth"
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
                <label class="label">Date</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <div class="control is-fullwidth">
                        <date-picker type="datetime" lang="en" format="DD-MM-YYYY H:mm"
                            v-bind:class="{ 'is-danger': errors.hasErrors('start') }"
                            :time-picker-options="startTimePickerOptions"
                            :not-before="today"
                            :width="'23em'"
                            v-model="start"
                            ></date-picker>
                    </div>
                </div>
            </div>
        </div>
        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">Duration</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <div class="control">
                        <input class="input" type="text"
                            maxlength="4" style="max-width: 4em;"
                            v-model="duration"
                            v-bind:class="{ 'is-danger': errors.hasErrors('duration') }">
                    </div>
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
                            :class="{
                                'is-link': !override,
                                'is-warning': override
                            }"
                            @click.prevent="submit">
                            Book
                        </button>
                    </div>
                    <div class="control">
                        <button class="button is-text" @click.prevent="clear">Clear</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import _ from 'lodash'
import moment from 'moment'
import DatePicker from 'vue2-datepicker'

import ErrorsList from '../../error/components/errors.vue'
import RoomSelect from '../../room/components/select.vue'

import { BOOKINGS_CREATE, BOOKINGS_SET_DATE } from '../actions.js'
import api from '../api.js'
import room from '../../room/api.js'
import lib from '../lib.js'

export default {
    components: { DatePicker, ErrorsList, RoomSelect },
    props: {
    },
    created: function() {
        // Force set to trigger update.
        this.start = this.startData
    },
    data: function() {
        return {
            room: 0,
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
        start: {
            get: function() {
                return this.startData
            },
            set: function(newStart) {
                this.startData = moment(newStart)
                this.$store.dispatch(BOOKINGS_SET_DATE, { date: moment(this.startData) })
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
    },
    methods: {
        submit: function() {
            this.clearErrors()
            let booking = new api.Booking({
                start: this.start,
                duration: this.duration,
                room: new room.Room({
                    id: this.room,
                })
            })
            this.$store.dispatch(BOOKINGS_CREATE, { booking, override: this.override })
                .then(function (booking) {
                    let date = moment(this.start).format('/YYYY/MM/DD')
                    this.$router.push('/bookings' + date, function() {
                        this.$emit('flash', {
                            type: 'success',
                            message: 'Booking created.',
                        })
                    }.bind(this))
                }.bind(this))
                .catch(function (error) {
                    this.$set(this.$data, 'errors', error)
                    // If conflict, try to override.
                    if (error.status == 409) {
                        this.override = true
                    }
                }.bind(this));
        },
        clear: function() {
            this.room = 0
            this.start = lib.nextSlot()
            this.duration = lib.slot
            this.override = false
            this.clearErrors()
        },
        clearErrors: function() {
            this.$set(this.$data, 'errors', new api.BookingError({}))
        }
    }
}
</script>

<style>
.booking-form {
    max-width: 26em;
}
</style>
