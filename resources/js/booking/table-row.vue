<template>
    <tr>
        <td>
            <template v-if="editMode">
                <span class="control">
                    <span class="select is-small">
                        <select
                            v-model="booking.relationships.room.data.id"
                            >
                            <option disabled selected value="0">Select a room...</option>
                            <option v-for="r in rooms" :key="r.id" :value="r.id">
                                {{ r.attributes.name }}
                            </option>
                        </select>
                    </span>
                </span>
            </template>
            <template v-else>
                {{ booking.relationships.room.data.attributes.name }}
            </template>
        </td>
        <td>
            <template v-if="editMode">
                <span class="control">
                    <date-picker type="datetime" lang="en" format="DD-MM-YYYY H:mm"
                        class="inplace"
                        :time-picker-options="startTimePickerOptions"
                        :not-before="today"
                        v-model="booking.attributes.start"
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
                        v-model="booking.attributes.duration"
                        @keyup.enter="edit"
                        >
                </span>
            </template>
            <template v-else>
                {{ booking.attributes.duration }}
            </template>
        </td>
        <td>{{ booking.relationships.user.data.attributes.name }}</td>
        <td>{{ booking.relationships.user.data.attributes.email }}</td>
        <td>
            <!-- TODO hide actions if current user is not the owner -->
            <span class="buttons">
                <a class="button is-small"
                    :class="{ 'is-link': editMode }"
                    @click.prevent="edit"
                    >
                    <span>Edit</span>
                    <span class="icon is-small">
                        <i class="fas fa-edit"></i>
                    </span>
                </a>
                <a class="button is-small is-danger is-outlined" @click.prevent="remove">
                    <span>Delete</span>
                    <span class="icon is-small">
                        <i class="fas fa-times"></i>
                    </span>
                </a>
            </span>
        </td>
    </tr>
</template>

<script>
import axios from 'axios'
import moment from 'moment'
import DatePicker from 'vue2-datepicker'

export default {
    components: { DatePicker },
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
            editMode: false,
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
        start: function() {
            return moment(this.booking.attributes.start)
        },
        startTime: function() {
            return this.start.format('h:mm a')
        },
        today: function() {
            let d = new Date()
            d.setHours(0)
            d.setMinutes(0)
            d.setSeconds(0)
            d.setMilliseconds(0)
            return d
        },
    },
    watch: {
        'booking.relationships.room.data.id': function(id) {
            this.booking.relationships.room.data.attributes.name =
                this.rooms.filter(r => r.id == id)[0].attributes.name
        },
    },
    methods: {
        edit: function() {
            if (!this.editMode) {
                this.editMode = true
                return
            }
            axios({
                method: 'patch',
                url: '/api/bookings/' + this.booking.id,
                data: {
                    data: {
                        type: "booking",
                        id: this.booking.id,
                        attributes: {
                            start: this.booking.attributes.start,
                            duration: this.booking.attributes.duration,
                        },
                        relationships: {
                            room: {
                                data: { id: this.booking.relationships.room.data.id }
                            }
                        },
                    },
                }
            })
                .then(function (response) {
                    this.$emit('booking-update', this.booking)
                    this.editMode = false
                }.bind(this))
                .catch(function (error) {
                    console.log("Can't edit booking "+ this.booking.id + ".")
                }.bind(this));
        },
        remove: function() {
            axios({
                method: 'delete',
                url: '/api/bookings/' + this.booking.id,
            })
                .then(function(response) {
                    this.$emit('booking-delete', this.booking)
                }.bind(this))
                .catch(function(error) {
                    console.log("Can't delete booking "+ this.booking.id + ".")
                }.bind(this))
        },
    },
}
</script>

<style>
.inplace .mx-input {
    height: 1.8em;
}
</style>
