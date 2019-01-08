<--
Schedule timeline component.

The list of instants on the side of the schedule.
Can be a list of hours, days...
-->
<template>
    <div class="timeline">
        <ul>
            <li v-for="i in instants">
                <span>{{ i.format(format) }}</span>
            </li>
        </ul>
    </div> <!-- .timeline -->
</template>

<script>
import moment from 'moment'

export default {
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
        width: {
            type: Number,
            default: 1,
        },
    },
    computed: {
        instants: function() {
            let instants = []
            for (let i = this.from.clone();
                 i.isBefore(this.to);
                 i.add(this.step, this.unit)) {
                instants.push(i.clone())
            }
            return instants
        },
    },
}
</script>

<style>
.schedule .timeline {
    display: none;
}

@media only screen and (min-width: 800px) {
    .schedule .timeline {
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        padding-top: 50px;
    }
    .schedule .timeline li {
        position: relative;
        height: 50px;
    }
    .schedule .timeline li::after {
        /* this is used to create the table horizontal lines */
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background: #EAEAEA;
    }
    .schedule .timeline li:last-of-type::after {
        display: none;
    }
    .schedule .timeline li span {
        display: none;
    }
}

@media only screen and (min-width: 1000px) {
    .schedule .timeline li::after {
        width: calc(100% - 60px);
        left: 60px;
    }
    .schedule .timeline li span {
        display: inline-block;
        -webkit-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
    }
    .schedule .timeline li:nth-of-type(2n) span {
        display: none;
    }
}
</style>
