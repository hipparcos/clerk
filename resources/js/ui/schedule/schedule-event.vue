<--
Schedule single event component.

A single event is displayed as a block relative to the top of the schedule.

Some colors for events background:
- blue: #577F92 (accent #618da1);
- violet: #443453 (accent #513e63);
- grey: #A2B9B2 (accent #b1c4be);
- orange: #f6b067 (accent #f7bd7f);
-->

<template>
    <li class="single-event"
        :style="style"
        >
        <slot :event="event"></slot>
    </li>
</template>

<script>
import moment from 'moment'

import lib from './lib.js'

export default {
    props: {
        from: {
            type: moment,
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
            default: 'h:mm a',
        },
        slotHeight: {
            type: Number,
            default: 50,
        },
        event: {
            type: lib.Event,
        },
    },
    data: function() {
        return {
            offset: 0, // correct height when event starts before the grid.
        }
    },
    computed: {
        top: function() {
            let diff = this.event.start.diff(this.from, this.unit)
            let realTop = this.slotHeight * (diff / this.step)
            let top = (realTop > 0) ? realTop : 0
            this.offset = top - realTop
            return top
        },
        height: function() {
            return this.slotHeight * (this.event.duration / this.step)
        },
        style: function() {
            return {
                top: this.top - 1 + 'px',
                height: this.height - this.offset - 1 + 'px',
                background: this.event.background || '#3273dc',
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

.schedule .events .single-event {
    display: block;
    height: 100%;
    padding: .375em .8em;
    font-size: 0.75rem;
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
}
</style>
