<template>
    <div class="notification adjust-margin" :class="classes">
        <button class="delete" @click.prevent="remove"></button>
        {{ message }}
    </div>
</template>

<script>
import { NOTIFICATIONS_REMOVE } from '../actions.js'
import lib from '../lib.js'

export default {
    props: {
        id: {
            required: true,
        },
        type: {
            type: String,
            required: true,
            validator(value) {
                return !!Object.keys(lib.Type).find(t => t === value)
            },
        },
        message: {
            type: String,
            required: true,
        },
        timeout: {
            type: Number,
        },
    },
    mounted() {
        if (this.timeout) {
            let self = this
            setTimeout(() => {
                this.remove()
            }, this.timeoutInMs)
        }
    },
    computed: {
        classes() {
            switch (this.type) {
                case lib.Type.success:
                    return 'is-primary'
                case lib.Type.error:
                    return 'is-danger'
                default:
                    return 'is-link'
            }
        },
        timeoutInMs() {
            return this.timeout * 1000
        },
    },
    methods: {
        remove() {
            this.$store.dispatch(NOTIFICATIONS_REMOVE, { id: this.id })
        },
    },
}
</script>

<style>
.adjust-margin {
    margin-bottom: 0.75rem !important;
}
</style>
