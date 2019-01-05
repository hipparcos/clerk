<template>
    <select :value="value" @input="$emit('input', Number($event.target.value))">
        <option disabled :selected="value == 0" value="0">Select a room...</option>
        <option v-for="r in rooms" :key="r.id" :value="r.id" :selected="value == r.id">
        {{ r.name }}
        </option>
    </select>
</template>

<script>
import { ROOMS_REQUEST } from '../../room/actions.js'

export default {
    props: {
        value: {
            type: Number,
            default: 0,
        },
    },
    computed: {
        rooms: function() {
            if (!this.$store.getters.areRoomsLoaded) {
                this.$store.dispatch(ROOMS_REQUEST)
                    .catch(err => {
                        console.log(err)
                    })
            }
            return this.$store.getters.getRooms
        },
    },
}
</script>
