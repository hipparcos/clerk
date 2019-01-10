<template>
    <div class="agenda">
    <div class="field is-horizontal has-addons has-addons-centered">
        <p class="control">
            <a class="button is-static">
                From
            </a>
        </p>
        <p class="control">
            <date-picker type="time" lang="en"
                format="HH:mm"
                placeholder="Select time"
                :clearable="false"
                :time-picker-options="timePickerOptions"
                :minute-step="step"
                width="100"
                v-model="from"
                ></date-picker>
        </p>
        <p class="control">
            <a class="button is-static">
                To
            </a>
        </p>
        <p class="control">
            <date-picker type="time" lang="en"
                format="HH:mm"
                placeholder="Select time"
                :clearable="false"
                :time-picker-options="timePickerOptions"
                :minute-step="step"
                width="100"
                v-model="to"
                ></date-picker>
        </p>
    </div>
    <schedule
        :from="from"
        :to="to"
        :groups="groups"
        :events="events"
        >
        <template slot-scope="{ event: booking, smallViewportMode }">
            <!-- delete button -->
            <span style="float: right; line-height: 1 !important;"
                v-if="booking.user.id == $store.getters.getProfile.id">
                <a class="icon is-small is-size-7 has-text-white"
                    @click.prevent="$router.push('/bookings/'+booking.id+'/edit')">
                    <i class="fas fa-edit"></i>
                </a>
                <delete-button :booking="booking" classes="icon is-small is-size-7 has-text-white">
                    <i class="fas fa-times"></i>
                </delete-button>
            </span>
            <section class="event-body">
            <!-- booking infos -->
                <strong class="has-text-white">
                    From {{ booking.start.format('h:mm a') }}
                    to {{ booking.end.format('h:mm a') }}
                </strong>
                <!-- room infos -->
                <span v-if="!smallViewportMode">in {{ booking.room.name }}</span>
                <!-- user infos -->
                <span v-if="booking.duration <= 30">
                    by {{ booking.user.name }}
                </span>
                <div v-else-if="booking.duration >= 30">
                    <span class="icon is-small">
                        <i class="fas fa-user"></i>
                    </span>
                    {{ booking.user.name }}
                    <br>
                    <span class="icon is-small">
                        <i class="fas fa-envelope"></i>
                    </span>
                    {{ booking.user.email }}
                </div>
            </section>
        </template>
    </schedule>
    </div>
</template>

<script>
import _ from 'lodash'
import moment from 'moment'

import DatePicker from 'vue2-datepicker'
import Schedule from '../../ui/schedule/schedule.vue'
import DeleteButton from './delete-button.vue'

import api from '../api.js'
import lib from '../lib.js'
import schedule from '../../ui/schedule/lib.js'
import { ROOMS_REQUEST } from '../../room/actions.js'


const colors = ['#618da1', '#513e63', '#b1c4be', '#f7bd7f'];

export default {
    components: { Schedule, DatePicker, DeleteButton },
    props: {
        bookings: {
            type: Array,
            required: true,
            $each: {
                type: api.Booking,
            },
        },
    },
    data: function() {
        let step = 15
        return {
            fromData: this.$store.getters.getSelectedDate.clone()
                .startOf('day').hours(8),
            toData: this.$store.getters.getSelectedDate.clone()
                .startOf('day').hours(18),
            step: step,
            timePickerOptions:{
                start: '00:00',
                step: '00:' + step,
                start: '00:00',
            },
        }
    },
    computed: {
        from: {
            get: function() {
                return this.fromData
            },
            set: function(value) {
                let from = this.$store.getters.getSelectedDate.clone().startOf('day')
                let m = moment(value)
                from.hours(m.hours())
                from.minutes(m.minutes())
                if (from.isBefore(this.toData)) {
                    this.fromData = from
                }
            }
        },
        to: {
            get: function() {
                return this.toData
            },
            set: function(value) {
                let to = this.$store.getters.getSelectedDate.clone().startOf('day')
                let m = moment(value)
                to.hours(m.hours())
                to.minutes(m.minutes())
                if (to.isAfter(this.fromData)) {
                    this.toData = to
                }
            }
        },
        groups: function() {
            if (!this.$store.getters.areRoomsLoaded) {
                this.$store.dispatch(ROOMS_REQUEST)
            }
            return this.$store.getters.getRooms.map(r => r.name)
        },
        events: function() {
            if (this.groups.length == 0) {
                return []
            }
            let events = _.fill(Array(this.groups.length), [])
            this.groups.forEach((g, idx) => {
                events[idx] = this.bookings.filter(b => g == b.room.name)
                let color = colors[idx % this.groups.length]
                events[idx].forEach(e => {
                    e.background = color
                })
            })
            return events
        }
    },
}
</script>
