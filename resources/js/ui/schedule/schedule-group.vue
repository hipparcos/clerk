<--
Schedule group component.

A column in the schedule.
Can be a day, a category...
-->
<template>
    <li class="events-group">
        <div class="top-info" refs="datum"><span>{{ group }}</span></div>
        <ul>
            <schedule-event
                v-for="event in filteredEvents"
                :key="event.id"
                :event="event"
                :slot-height="slotHeight"
                :from="from"
                :step="step"
                :unit="unit"
                :format="format"
                >
                <template slot-scope="{ event }">
                    <slot :event="event"></slot>
                </template>
            </schedule-event>
        </ul>
    </li>
</template>

<script>
import moment from 'moment'

import scheduleEvent from './schedule-event.vue'
import lib from './lib.js'

export default {
    components: {
        scheduleEvent,
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
        format: {
            type: String,
            default: 'H:mm',
        },
        group: {
            type: String,
            default: '',
        },
        events: {
            type: Array,
            default: () => [],
            $each: {
                type: lib.Event,
            },
        },
    },
    data: function() {
        return {
            slotHeight: 50,
        }
    },
    beforeUpdate: function() {
        this.slotHeight = this.$refs.datum.clientHeight
    },
    computed: {
        filteredEvents: function() {
            let self = this
            return this.events.filter(evt => {
                return evt.start.clone().add(evt.duration, self.unit).isAfter(self.from)
                    && evt.start.isBefore(self.to)
            })
        }
    },
}
</script>

<style>
.schedule .events {
    position: relative;
    z-index: 1;
}

.schedule .events .events-group {
    margin-bottom: 30px;
}

.schedule .events .top-info {
    width: 100%;
    padding: 0 5%;
}

.schedule .events .top-info > span {
    display: inline-block;
    line-height: 1.2;
    margin-bottom: 10px;
    font-weight: bold;
}

.schedule .events .events-group > ul {
    position: relative;
    padding: 0 5%;
    /* force its children to stay on one line */
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    overflow-x: scroll;
    -webkit-overflow-scrolling: touch;
}

.schedule .events .events-group > ul::after {
    /* never visible - used to add a right padding to .events-group > ul */
    display: inline-block;
    content: '-';
    width: 1px;
    height: 100%;
    opacity: 0;
    color: transparent;
}

@media only screen and (min-width: 800px) {
    .schedule .events {
        float: left;
        width: 100%;
    }
    .schedule .events .events-group {
        width: 20%;
        float: left;
        border: 1px solid #EAEAEA;
        /* reset style */
        margin-bottom: 0;
    }
    .schedule .events .events-group:not(:first-of-type) {
        border-left-width: 0;
    }
    .schedule .events .top-info {
        /* vertically center its content */
        display: table;
        height: 50px;
        border-bottom: 1px solid #EAEAEA;
        /* reset style */
        padding: 0;
    }
    .schedule .events .top-info > span {
        /* vertically center inside its parent */
        display: table-cell;
        vertical-align: middle;
        padding: 0 .5em;
        text-align: center;
        /* reset style */
        font-weight: normal;
        margin-bottom: 0;
    }
    .schedule .events .events-group > ul {
        height: 950px;
        /* reset style */
        display: block;
        overflow: visible;
        padding: 0;
    }
    .schedule .events .events-group > ul::after {
        clear: both;
        content: "";
        display: block;
    }
    .schedule .events .events-group > ul::after {
        /* reset style */
        display: none;
    }
}

@media only screen and (min-width: 1000px) {
    .schedule .events {
        /* 60px is the .timeline element width */
        width: calc(100% - 60px);
        margin-left: 60px;
    }
}
</style>
