<template>
    <tr>
        <td>{{ booking.relationships.room.data.attributes.name }}</td>
        <td>{{ startTime }}</td>
        <td>{{ booking.attributes.duration }} min</td>
        <td>{{ booking.relationships.user.data.attributes.name }}</td>
        <td>{{ booking.relationships.user.data.attributes.email }}</td>
        <td>
            <!-- TODO hide actions if current user is not the owner -->
            <span class="buttons">
                <a class="button is-small" @click.prevent="edit">
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
import moment from 'moment'

export default {
    props: {
        booking: Object,
    },
    data: function() {
        return {
        }
    },
    computed: {
        start: function() {
            return moment(this.booking.attributes.start)
        },
        startTime: function() {
            return this.start.format('h:mm a')
        },
    },
    methods: {
        edit: function() {
            // TODO implement edit booking.
        },
        remove: function() {
            // TODO implemnt delete booking.
        },
    },
}
</script>
