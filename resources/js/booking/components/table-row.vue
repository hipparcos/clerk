<template>
    <tr>
        <td>
            <template v-if="editMode">
                <span class="control">
                    <span class="select is-small"
                        :class="{ 'is-danger': errors.hasErrors('room') }"
                        >
                        <room-select v-model="room"></room-select>
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
                        :clearable="false"
                        :time-picker-options="startTimePickerOptions"
                        :not-before="today"
                        :class="{ 'is-danger': errors.hasErrors('start') }"
                        :width="145"
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
                <duration-field v-model="duration"
                    :has-errors="errors.hasErrors('duration')"
                    classes="is-small"></duration-field>
            </template>
            <template v-else>
                {{ booking.duration }} min
            </template>
        </td>
        <td>{{ booking.user.name }}</td>
        <td>{{ booking.user.email }}</td>
        <td>
            <span class="buttons" v-if="booking.user.id == $store.getters.getProfile.id">
                <a class="button is-small"
                    :class="submitClass"
                    :disabled="submitState"
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
                <delete-button
                    classes="button is-small is-danger is-outlined"
                    classesConfirm="button is-danger is-outlined"
                    :booking="booking">
                    <span>Delete</span>
                    <span class="icon is-small">
                        <i class="fas fa-times"></i>
                    </span>
                </delete-button>
            </span>
        </td>
    </tr>
</template>

<script>
import _ from 'lodash'
import moment from 'moment'

import DatePicker from 'vue2-datepicker'
import DeleteButton from './delete-button.vue'
import BookingBox from './box.vue'
import RoomSelect from '../../room/components/select.vue'
import DurationField from './duration-field.vue'

import { NOTIFICATIONS_PUSH } from '../../notification/actions.js'
import notif from '../../notification/lib.js'
import { BOOKINGS_UPDATE } from '../actions.js'
import api from '../api.js'
import room from '../../room/api.js'

const copy = (obj) => JSON.parse(JSON.stringify(obj))

export default {
    components: { DatePicker, DeleteButton, BookingBox, RoomSelect, DurationField },
    props: {
        booking: Object,
    },
    data: function() {
        return {
            start: this.booking.start,
            duration: this.booking.duration,
            room: this.booking.room.id,
            roomCausingConflict: 0,
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
        startTime: function() {
            return this.booking.start.format('h:mm a')
        },
        today: function() {
            return moment().startOf('day')
        },
        submitClass: function() {
            if (!this.editMode) {
                return ['is-link', 'is-outlined']
            }
            if (this.errors.conflict()) {
                if (!this.errors.isOverridable()
                    && this.room == this.roomCausingConflict) {
                    return ['is-danger']
                }
                return ['is-warning']
            }
            return ['is-link']
        },
        submitState: function() {
            return this.errors.conflict() && !this.errors.isOverridable()
                && this.room == this.roomCausingConflict
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
            this.$store.dispatch(BOOKINGS_UPDATE, { booking, override: this.override })
                .then(function(booking) {
                    this.reset()
                    this.$store.dispatch(NOTIFICATIONS_PUSH, new notif.Notification({
                        type: notif.Type.success,
                        message: 'Booking updated.',
                    }))
                }.bind(this))
                .catch(function (errors) {
                    console.log(errors)
                    this.$set(this.$data, 'errors', errors)
                    this.$emit('errors', this.errors)
                    // If conflict, try to override.
                    if (errors.conflict()) {
                        if (errors.isOverridable()) {
                            this.override = true
                        } else {
                            this.roomCausingConflict = booking.room.id
                        }
                    }
                }.bind(this));
        },
        reset: function() {
            this.editMode = false
            this.start = this.booking.start
            this.duration = this.booking.duration
            this.room = this.booking.room.id
            this.roomCausingConflict = 0
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
    font-size: 0.75rem !important;
}
.inplace .mx-calendar-icon {
    height: 1.125em !important;
}

.box {
    margin-top: 1rem;
}
</style>
