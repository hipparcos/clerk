<template>
    <tr>
        <td>
            <template v-if="editMode">
                <span class="control">
                    <span class="select is-small"
                        :class="{ 'is-danger': errors.hasErrors('room') }"
                        >
                        <select
                            v-model="room"
                            >
                            <option disabled selected value="0">Select a room...</option>
                            <option v-for="r in rooms" :key="r.id" :value="r.id">
                                {{ r.name }}
                            </option>
                        </select>
                    </span>
                </span>
            </template>
            <template v-else>
                {{ booking.room.name }}
            </template>
        </td>
        <td>
            <template v-if="editMode">
                <span class="control">
                    <date-picker type="datetime" lang="en" format="DD-MM-YYYY H:mm"
                        class="inplace"
                        :time-picker-options="startTimePickerOptions"
                        :not-before="today"
                        :class="{ 'is-danger': errors.hasErrors('start') }"
                        v-model="start"
                        ></date-picker>
                </span>
            </template>
            <template v-else>
                {{ startTime }}
            </template>
        </td>
        <td>
            <template v-if="editMode">
                <span class="control">
                    <input class="input is-small" type="text" ref="duration"
                        size="4" maxlength="4" style="width: 4em;"
                        v-model="duration"
                        @keyup.enter="edit"
                        :class="{ 'is-danger': errors.hasErrors('duration') }"
                        >
                </span>
            </template>
            <template v-else>
                {{ booking.duration }}
            </template>
        </td>
        <td>{{ booking.user.name }}</td>
        <td>{{ booking.user.email }}</td>
        <td>
            <span class="buttons" v-if="booking.user.id == $store.getters.getProfile.id">
                <a class="button is-small"
                    :class="{
                        'is-link': editMode && !override,
                        'is-warning': editMode && override
                    }"
                    @click.prevent="edit"
                    >
                    <span>Edit</span>
                    <span class="icon is-small">
                        <i class="fas fa-edit"></i>
                    </span>
                </a>
                <a v-if="editMode" class="button is-small"
                    @click.prevent="cancel"
                    >
                    <span>Cancel</span>
                    <span class="icon is-small">
                        <i class="fas fa-ban"></i>
                    </span>
                </a>
                <button-confirmed
                    classes="button is-small is-danger is-outlined"
                    @confirmed="remove">
                    <template slot="title">
                        Confirm booking deletion
                    </template>

                    <template slot="message">
                        Do you really want to delete this booking?
                        <booking-box :booking="booking"></booking-box>
                    </template>

                    <span>Delete</span>
                    <span class="icon is-small">
                        <i class="fas fa-times"></i>
                    </span>
                </button-confirmed>
            </span>
        </td>
    </tr>
</template>

<script>
import _ from 'lodash'
import moment from 'moment'

import DatePicker from 'vue2-datepicker'
import ButtonConfirmed from '../../ui/button-confirmed.vue'
import BookingBox from './box.vue'

import api from '../api.js'
import room from '../../room/api.js'

const copy = (obj) => JSON.parse(JSON.stringify(obj))

export default {
    components: { DatePicker, ButtonConfirmed, BookingBox, },
    props: {
        booking: Object,
    },
    created: function() {
        this.updateRooms = _.debounce(function() {
            this.$store.dispatch(ROOMS_REQUEST)
        }.bind(this), 1000)
    },
    data: function() {
        return {
            start: this.booking.start,
            duration: this.booking.duration,
            room: this.booking.room.id,
            editMode: false,
            override: false,
            errors: new api.BookingError({}),
            // DatePicker
            startTimePickerOptions:{
                start: '08:00',
                step: '00:15',
                end: '18:00'
            },
        }
    },
    computed: {
        rooms: function() {
            if (!this.$store.getters.areRoomsLoaded) {
                this.updateRooms()
            }
            return this.$store.getters.getRooms
        },
        startTime: function() {
            return this.booking.start.format('h:mm a')
        },
        today: function() {
            return moment().startOf('day')
        },
    },
    watch: {
        booking: function() {
            this.reset()
        },
    },
    methods: {
        cancel: function() {
            this.editMode = false
            this.reset()
        },
        edit: function() {
            if (!this.editMode) {
                this.reset()
                this.editMode = true
                return
            }
            let booking = new api.Booking({
                id: this.booking.id,
                start: this.start,
                duration: this.duration,
                room: new room.Room({
                    id: this.room,
                })
            })
            api.update(booking, this.override)
                .then(function (booking) {
                    this.$emit('update', booking)
                    this.reset()
                }.bind(this))
                .catch(function (errors) {
                    this.$set(this.$data, 'errors', errors)
                    this.$emit('errors', this.errors)
                    // If conflict, try to override.
                    if (errors.status == 409) {
                        this.override = true
                    }
                }.bind(this));
        },
        remove: function() {
            api.remove(this.booking.id)
                .then(function() {
                    this.$emit('delete', this.booking)
                }.bind(this))
                .catch(function(err) {
                    console.log(err)
                })
        },
        reset: function() {
            this.editMode = false
            this.start = this.booking.start
            this.duration = this.booking.duration
            this.room = this.booking.room.id
            this.override = false
            this.clearErrors()
        },
        clearErrors: function() {
            this.$set(this.$data, 'errors', new api.BookingError({}))
            this.$emit('errors', this.errors)
        },
    },
}
</script>

<style>
.inplace .mx-input {
    height: 1.8em;
}

.box {
    margin-top: 1rem;
}
</style>
