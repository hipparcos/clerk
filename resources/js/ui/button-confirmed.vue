<template>
    <span>
        <div class="modal is-active" v-if="display">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">
                        <slot name="title">
                            Confirm action
                        </slot>
                    </p>
                    <button class="delete" aria-label="close"
                        @click.prevent="onCancel"
                        ></button>
                </header>
                <section class="modal-card-body">
                    <slot name="message">
                        Do you confirm this action?
                    </slot>
                </section>
                <footer class="modal-card-foot">
                    <a :class="classesConfirm ||classes"
                        @click.prevent="onConfirm">
                        <slot name="confirm">
                            Confirm
                        </slot>
                    </a>
                    <a :class="classesCancel" @click.prevent="onCancel">
                        <slot name="cancel">
                            Cancel
                        </slot>
                    </a>
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
        classes: {
            type: String,
            default: "button is-danger",
        },
        classesConfirm: {
            type: String,
            default: "button is-danger",
        },
        classesCancel: {
            type: String,
            default: "button",
        },
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
