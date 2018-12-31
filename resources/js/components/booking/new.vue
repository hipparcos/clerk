<template>
    <form>
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
                    <p class="select"
                        v-bind:class="{ 'is-danger': errors.fields.room.length > 0 }"
                        >
                        <select
                            v-model="room"
                            >
                            <option disabled selected value="0">Select a room...</option>
                            <option v-for="r in rooms" :key="r.id" :value="r.id">
                                {{ r.attributes.name }}
                            </option>
                        </select>
                    </p>
                </div>
            </div>
            <div class="field-label is-normal">
                <label class="label">Date</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <p class="control">
                        <date-picker type="datetime" lang="en" format="DD-MM-YYYY H:mm"
                            v-bind:class="{ 'is-danger': errors.fields.start.length > 0 }"
                            :time-picker-options="startTimePickerOptions"
                            :not-before="today"
                            v-model="start"
                            ></date-picker>
                    </p>
                </div>
            </div>
            <div class="field-label is-normal">
                <label class="label">Duration</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <p class="control">
                        <input class="input" type="text" style="max-width: 4em;"
                            maxlength="4" size="4"
                            v-model="duration"
                            v-bind:class="{ 'is-danger': errors.fields.duration.length > 0 }">
                    </p>
                </div>
            </div>
            <div class="field-label is-normal">
                <label class="label">Override?</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <p class="control">
                        <label class="checkbox">
                            <input class="checkbox" type="checkbox" v-model="override">
                            Yes
                        </label>
                    </p>
                </div>
            </div>
            <div class="field-body">
                <div class="field is-grouped">
                    <p class="control">
                        <button class="button is-link" @click.prevent="submit">Book</button>
                    </p>
                    <p class="control">
                        <button class="button is-text" @click.prevent="clear">Clear</button>
                    </p>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import axios from 'axios'
import DatePicker from 'vue2-datepicker'


export default {
    components: { DatePicker },
    props: {
        token: String,
    },
    created: function() {
        axios({
            method: 'get',
            url: '/api/rooms',
            headers: {
                'Authorization': 'Bearer ' + this.token,
            },
        })
            .then(function (response) {
                this.rooms = response.data.data
            }.bind(this))
            .catch(function (error) {
                console.log(error.message)
            }.bind(this))
    },
    data: function() {
        return {
            room: 0,
            rooms: [],
            start: new Date(Date.now()),
            durationData: 0,
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
                headers: {
                    'Authorization': 'Bearer ' + this.token,
                },
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
                    this.$emit('flash-success', 'Booking saved.')
                    this.override = false
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
                }.bind(this));
        },
        clear: function() {
            this.room = ""
            this.start = ""
            this.duration = 0
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
