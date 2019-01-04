<template>
    <form class="booking-form">
        <div v-if="errors.message" class="notification is-danger">
            <button class="delete" @click.prevent="errors.message = ''"></button>
            <h6 class="title is-6">{{ errors.message }}</h6>
            <div v-if="hasErrors" class="content">
                <ul>
                    <li v-for="err in errors.fields.room">{{ err }}</li>
                    <li v-for="err in errors.fields.start">{{ err }}</li>
                    <li v-for="err in errors.fields.duration">{{ err }}</li>
                </ul>
            </div>
        </div>
        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">Room</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <div class="control">
                        <div class="select is-fullwidth"
                            v-bind:class="{ 'is-danger': errors.fields.room.length > 0 }"
                            >
                            <select
                                v-model="room"
                                >
                                <option disabled selected value="0">Select a room...</option>
                                <option v-for="r in rooms" :key="r.id" :value="r.id">
                                    {{ r.name }}
                                </option>
                            </select>
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
                            v-bind:class="{ 'is-danger': errors.fields.start.length > 0 }"
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
                            v-bind:class="{ 'is-danger': errors.fields.duration.length > 0 }">
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
import axios from 'axios'
import DatePicker from 'vue2-datepicker'

import { ROOMS_REQUEST } from '../../room/actions.js'
import lib from '../lib.js'

export default {
    components: { DatePicker },
    props: {
    },
    created: function() {
        this.updateRooms = _.debounce(function() {
            this.$store.dispatch(ROOMS_REQUEST)
        }.bind(this), 1000)
    },
    data: function() {
        return {
            room: 0,
            start: lib.nextSlot(),
            durationData: lib.slot,
            override: false,
            errors: {
                message: "",
                fields: {
                    room: [],
                    start: [],
                    duration: [],
                },
            },
            // DatePicker
            startTimePickerOptions:{
                start: '08:00',
                step: '00:15',
                end: '18:00'
            }
        }
    },
    computed: {
        rooms: function() {
            if (!this.$store.getters.areRoomsLoaded) {
                this.updateRooms()
            }
            return this.$store.getters.getRooms
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
            let d = new Date()
            d.setHours(0)
            d.setMinutes(0)
            d.setSeconds(0)
            d.setMilliseconds(0)
            return d
        },
        hasErrors: function() {
            return Object.values(this.errors.fields)
                .reduce(function(acc, f) { return acc + f.length }, 0) > 0
        },
    },
    methods: {
        submit: function() {
            // Clear.
            this.clearErrors()
            // Request.
            axios({
                method: 'post',
                url: '/api/bookings',
                data: {
                    data: {
                        type: "booking",
                        attributes: {
                            start: this.start,
                            duration: this.duration,
                        },
                        relationships: {
                            room: {
                                data: { id: this.room }
                            }
                        },
                    },
                    meta: {
                        overrideUserCollision: this.override
                    }
                }
            })
                .then(function (response) {
                    let date = moment(this.start).format('/YYYY/MM/DD')
                    this.$router.push('/bookings' + date, function() {
                        this.$emit('flash-success', 'Booking saved.')
                    }.bind(this))
                }.bind(this))
                .catch(function (error) {
                    let data = error.response.data
                    this.errors.message
                        = data.message
                    if ('data.relationships.room.data.id' in data.errors) {
                        this.errors.fields.room
                            = data.errors['data.relationships.room.data.id']
                    }
                    if ('data.attributes.start' in data.errors) {
                        this.errors.fields.start
                            = data.errors['data.attributes.start']
                    }
                    if ('data.attributes.duration' in data.errors) {
                        this.errors.fields.duration
                            = data.errors['data.attributes.duration']
                    }
                    // If conflict, try to override.
                    if (error.response.status == 409) {
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
            this.errors.message = ""
            this.errors.fields.room = []
            this.errors.fields.start = []
            this.errors.fields.duration = []
        }
    }
}
</script>

<style>
.booking-form {
    max-width: 26em;
}
</style>
