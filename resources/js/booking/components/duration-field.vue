<!--
duration-field is a component to set a booking duration.
-->
<template>
    <span class="field has-addons">
        <p class="control">
            <input class="input" type="text"
                maxlength="4" :style="{ 'max-width': width }"
                :class="classInput"
                v-model="duration">
        </p>
        <p class="control">
            <a class="button" :class="classes" @click.prevent="incDuration">+</a>
        </p>
        <p class="control">
            <a class="button" :class="classes" @click.prevent="decDuration">-</a>
        </p>
        <p class="control">
            <a class="button is-static" :class="classes">minutes</a>
        </p>
    </span>
</template>

<script>
import lib from '../lib.js'

export default {
    props: {
        value: {
            required: true,
        },
        // Width in em/px...
        width: {
            type: String,
            default: "4em",
        },
        classes: {
            type: String,
        },
        // Outlined if true.
        hasErrors: {
            type: Boolean,
            default: false,
        },
    },
    created: function() {
        // Round duration to the next slot value.
        // @param {Number} duration
        // @param {Function} rounding function to be used (default: Math.round)
        this.roundToSlot = function(duration, roundFn = Math.round) {
            duration = roundFn(duration / lib.slot) * lib.slot
            if (duration == 0) {
                return lib.slot
            }
            return duration
        }
    },
    computed: {
        duration: {
            get: function() {
                return this.value
            },
            set: function(value) {
                let duration = Math.round(Number(value))
                if (!duration || duration <= 0) {
                    duration = lib.slot
                }
                this.$emit('input', duration)
            }
        },
        classInput: function() {
            let classes = this.classes
            if (this.hasErrors) {
                classes = classes + " is-danger"
            }
            return classes
        },
    },
    methods: {
        incDuration: function() {
            let roundedDuration = this.roundToSlot(this.duration, Math.ceil)
            if (roundedDuration == this.duration) {
                this.duration += lib.slot
            } else {
                this.duration = roundedDuration
            }
        },
        decDuration: function() {
            let roundedDuration = this.roundToSlot(this.duration, Math.floor)
            if (roundedDuration == this.duration && this.duration >= 2 * lib.slot) {
                this.duration -= lib.slot
            } else {
                this.duration = roundedDuration
            }
        },
    },
}
</script>
