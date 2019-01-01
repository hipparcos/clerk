<template>
    <span>
        <div class="modal is-active" v-if="display">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Confirm action</p>
                    <button class="delete" aria-label="close"
                        @click.prevent="onCancel"
                        ></button>
                </header>
                <section class="modal-card-body">
                Do you confirm this action?
                </section>
                <footer class="modal-card-foot">
                    <button class="button is-success"
                        @click.prevent="onConfirm"
                        >Confirm</button>
                    <button class="button"
                        @click.prevent="onCancel"
                        >Cancel</button>
                </footer>
            </div>
        </div>
        <a :class="classes" @click.prevent="onClick">
            <slot></slot>
        </a>
    </span>
</template>

<script>
export default {
    props: {
        classes: String,
    },
    data: function() {
        return {
            display: false,
        }
    },
    methods: {
        onClick: function() {
            this.display = true
        },
        onConfirm: function() {
            this.display = false
            this.$emit('confirmed')
        },
        onCancel: function() {
            this.display = false
            this.$emit('cancelled')
        },
    },
}
</script>
