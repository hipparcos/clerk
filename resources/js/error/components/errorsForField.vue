<template>
    <ul v-if="errors.hasErrors(field)" class="help is-danger">
        <li v-for="err in errors.errorsFor(field)">{{ err }}</li>
    </ul>
</template>

<script>
import lib from '../lib.js'

export default {
    props: {
        errors: {
            type: lib.APIError,
            required: true,
        },
        field: {
            type: String,
            required: true,
        },
    },
}
</script>
