<template>
    <schedule
        :from="from"
        :to="to"
        :groups="groups"
        :events="events"
        >
        <template slot-scope="{ event }">
            <strong class="has-text-white">
                From {{ event.start.format('h:mm a') }}
                to {{ event.end.format('h:mm a') }}
            </strong> in {{ event.room.name }}
            <span v-if="event.duration <= 30">
                by {{ event.user.name }}
            </span>
            <div v-else-if="event.duration >= 30">
                <span class="icon is-small">
                    <i class="fas fa-user"></i>
                </span>
                {{ event.user.name }}
                <br>
                <span class="icon is-small">
                    <i class="fas fa-envelope"></i>
                </span>
                {{ event.user.email }}
            </div>
        </template>
    </schedule>
</template>

<script>
import _ from 'lodash'

import ScheduleComponent from '../../ui/schedule/schedule.vue'

import api from '../api.js'
import lib from '../lib.js'
import schedule from '../../ui/schedule/lib.js'
import { ROOMS_REQUEST } from '../../room/actions.js'

export default {
    components: { schedule: ScheduleComponent },
    props: {
        bookings: {
            type: Array,
            required: true,
            $each: {
                type: api.Booking,
            },
        },
    },
    computed: {
        from: function() {
            return this.$store.getters.getSelectedDate.clone()
                .startOf('day').hours(8)
        },
        to: function() {
            return this.$store.getters.getSelectedDate.clone()
                .startOf('day').hours(18)
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
            })
            return events
        }
    },
}
</script>
