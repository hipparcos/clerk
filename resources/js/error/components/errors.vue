<template>
    <div v-if="showErrors && errors.hasErrors()" class="notification is-danger">
        <button class="delete" @click.prevent="showErrors = false"></button>
        <h6 class="title is-6">{{ errors.description() }}</h6>
        <div v-if="listErrors" class="content">
            <ul>
                <template v-for="(_, f) in errors.fields">
                    <li v-for="err in errors.errorsFor(f)">{{ err }}</li>
                </template>
            </ul>
        </div>
    </div>
</template>

<script>
import lib from '../lib.js'

export default {
    props: {
        errors: {
            type: lib.APIError,
            required: true,
        },
        listErrors: {
            type: Boolean,
            required: false,
            default: false,
        },
    },
    data: function() {
        return {
            showErrors: true,
        }
    },
    watch: {
        errors: function() {
            this.showErrors = true
        },
    },
}
</script>
