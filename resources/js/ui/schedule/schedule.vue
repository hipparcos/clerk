<template>
    <div class="schedule">
        <schedule-timeline
            :from="from"
            :to="to"
            :step="step"
            :unit="unit"
            :format="format"
            :width="groups.length"
            >
        </schedule-timeline>

        <div class="events">
            <ul>
                <schedule-group
                    v-for="(group, index) in groups"
                    :key="group"
                    :group="group"
                    :events="events[index]"
                    :from="from"
                    :to="to"
                    :step="step"
                    :unit="unit"
                    :format="format"
                    >
                </schedule-group>
            </ul>
        </div> <!-- .events -->

    </div> <!-- .schedule -->
</template>

<script>
import moment from 'moment'

import scheduleTimeline from './schedule-timeline.vue'
import scheduleGroup from './schedule-group.vue'
import lib from './lib.js'

export default {
    components: {
        scheduleTimeline,
        scheduleGroup,
    },
    props: {
        from: {
            type: Object,
            default: () => moment().startOf('day').hours(8),
        },
        to: {
            type: Object,
            default: () => moment().startOf('day').hours(18),
        },
        step: {
            type: Number,
            default: 30,
        },
        unit: {
            type: String,
            default: 'minutes',
        },
        format: {
            type: String,
            default: 'H:mm',
        },
        groups: {
            type: Array,
            default: [],
            validator: function(value) {
                return value.every(function(elem) {
                    return typeof elem === "string"
                })
            },
        },
        events: {
            type: Array,
            default: [],
            validator: function(value) {
                return value.every(function(arr) {
                    return Array.isArray(arr) && arr.every(lib.validateEvent)
                })
            },
        },
    },
}
</script>

<style>
.schedule {
    position: relative;
    margin: 2em 0;
}

.schedule::before {
    /* never visible - this is used in js to check the current MQ */
    content: 'mobile';
    display: none;
}

@media only screen and (min-width: 800px) {
    .schedule {
        width: 90%;
        max-width: 1400px;
        margin: 2em auto;
    }
    .schedule::after {
        clear: both;
        content: "";
        display: block;
    }
    .schedule::before {
        content: 'desktop';
    }
}
</style>
