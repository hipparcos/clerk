<template>
    <div class="box">
        <div class="content">
            Booking in
            <strong>{{ booking.room.name }}</strong>
            <br>
            From {{ booking.start.calendar() }}
            to {{ booking.end.calendar() }}
            ({{ booking.duration }} minutes)
        </div>
    </div>
</template>

<script>
import api from '../api.js'

export default {
    props: {
        booking: {
            type: api.Booking,
            required: true,
        },
    },
}
</script>
