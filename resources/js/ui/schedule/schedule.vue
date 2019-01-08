<--
Schedule main component.

A schedule displays a list of events organised by groups and positioned on a timeline.
An event can be a meeting, a group can be the day of a week, the timeline hours of each day.
-->
<template>
    <div class="schedule">
        <schedule-timeline
            :from="from"
            :to="to"
            :step="step"
            :unit="unit"
            :slot-height="slotHeight"
            :format="format"
            :width="groups.length"
            :small-viewport-mode="smallViewportMode"
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
                    :slot-height="slotHeight"
                    :format="format"
                    :style="style"
                    :small-viewport-mode="smallViewportMode"
                    >
                    <template slot-scope="{ event }">
                        <slot :event="event" :small-viewport-mode="smallViewportMode"></slot>
                    </template>
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
            type: moment,
            default: () => moment().startOf('day').hours(8),
        },
        to: {
            type: moment,
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
        slotHeight: {
            type: Number,
            default: 50,
        },
        format: {
            type: String,
            default: 'H:mm',
        },
        groups: {
            type: Array,
            default: () => [],
            validator: function(value) {
                return value.every(function(elem) {
                    return typeof elem === "string"
                })
            },
        },
        events: {
            type: Array,
            default: () => [],
            $each: {
                type: Array,
                default: () => [],
                $each: {
                    type: lib.Event,
                },
            },
        },
    },
    data: function() {
        return {
            windowWidth: 0,
        }
    },
    computed: {
        smallViewportMode: function() {
            return this.windowWidth < 800
        },
        style: function() {
            let width = 0;
            if (this.groups.length > 0) {
                if (this.smallViewportMode) {
                    width = 100
                } else {
                    width = 100 / this.groups.length
                }
            }
            return {
                'width': width+'%',
            }
        },
    },
    mounted: function() {
        this.$nextTick(() => {
            let self = this
            window.addEventListener('resize', () => {
                self.windowWidth = window.innerWidth
            });
        })
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
