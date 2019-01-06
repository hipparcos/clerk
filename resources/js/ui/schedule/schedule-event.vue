<template>
    <li class="single-event"
        :style="style"
        >
        {{ event.name }}
        {{ event.start.format(format) }}
        {{ end.format(format) }}
    </li>
</template>

<script>
import moment from 'moment'

import lib from './lib.js'

export default {
    props: {
        from: {
            type: Object,
            default: () => moment().startOf('day').hours(8),
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
        slotHeight: {
            type: Number,
            default: 50,
        },
        event: {
            type: lib.Event,
        },
    },
    computed: {
        end: function() {
            return this.event.start.clone().add(this.event.duration, this.unit)
        },
        top: function() {
            let diff = this.event.start.diff(this.from, this.unit)
            return this.slotHeight * (diff / this.step)
        },
        height: function() {
            return this.slotHeight * (this.event.duration / this.step)
        },
        style: function() {
            return {
                top: this.top + 'px',
                height: this.height + 'px',
                // TODO define background color.
                background: '#3273dc',
            }
        },
    },
}
</script>

<style>
.schedule .events .single-event {
    /* force them to stay on one line */
    -ms-flex-negative: 0;
    flex-shrink: 0;
    float: left;
    height: 150px;
    width: 70%;
    max-width: 300px;
    box-shadow: inset 0 -3px 0 rgba(0, 0, 0, 0.2);
    margin-right: 20px;
    -webkit-transition: opacity .2s, background .2s;
    transition: opacity .2s, background .2s;
    color: #ffffff;
}

.schedule .events .single-event:last-of-type {
    margin-right: 5%;
}

.schedule .events .single-event a {
    display: block;
    height: 100%;
    padding: .8em;
}

@media only screen and (min-width: 550px) {
    .schedule .events .single-event {
        width: 40%;
    }
}

@media only screen and (min-width: 800px) {
    .schedule .events .single-event {
        position: absolute;
        z-index: 3;
        /* top position and height will be set using js */
        width: calc(100% + 2px);
        left: -1px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1), inset 0 -3px 0 rgba(0, 0, 0, 0.2);
        /* reset style */
        -ms-flex-negative: 1;
        flex-shrink: 1;
        height: auto;
        max-width: none;
        margin-right: 0;
    }
    .schedule .events .single-event a {
        padding: 1.2em;
    }
    .schedule .events .single-event:last-of-type {
        /* reset style */
        margin-right: 0;
    }
    .schedule .events .single-event.selected-event {
        /* the .selected-event class is added when an user select the event */
        visibility: hidden;
    }
}

.schedule .event-name,
.schedule .event-date {
    display: block;
    color: white;
    font-weight: bold;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.schedule .event-name {
    font-size: 2.4rem;
}

@media only screen and (min-width: 800px) {
    .schedule .event-name {
        font-size: 2rem;
    }
}

.schedule .event-date {
    /* they are not included in the the HTML but added using JavScript */
    font-size: 1.4rem;
    opacity: .7;
    line-height: 1.2;
    margin-bottom: .2em;
}

.schedule .single-event[data-event="event-1"],
.schedule [data-event="event-1"] .header-bg {
    /* this is used to set a background color for the event */
    background: #577F92;
}

.schedule .single-event[data-event="event-1"]:hover {
    background: #618da1;
}

.schedule .single-event[data-event="event-2"],
.schedule [data-event="event-2"] .header-bg {
    background: #443453;
}

.schedule .single-event[data-event="event-2"]:hover {
    background: #513e63;
}

.schedule .single-event[data-event="event-3"],
.schedule [data-event="event-3"] .header-bg {
    background: #A2B9B2;
}

.schedule .single-event[data-event="event-3"]:hover {
    background: #b1c4be;
}

.schedule .single-event[data-event="event-4"],
.schedule [data-event="event-4"] .header-bg {
    background: #f6b067;
}

.schedule .single-event[data-event="event-4"]:hover {
    background: #f7bd7f;
}
</style>
