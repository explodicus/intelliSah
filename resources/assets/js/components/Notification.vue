<template>
    <div v-show="show" id="notification" class="col-3">
        {{ text }}
    </div>
</template>
<style>
    #notification {
        position: fixed;
        left: 80%;
        top: 90%;
        padding: 20px 20px 20px 20px;
        background-color: white;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 3px 10px 0 rgba(0, 0, 0, 0.19);
    }
</style>
<script>
    export default {
        data: () => ({
            show: false,
            text: '',
        }),

        props: {
            timeout: {
                type: Number,
                default: 6000,
            },
        },

        mounted() {
            VueEvents.$on('notification', e => this.showNotification(e))
        },

        methods: {
            showNotification(event) {
                this.text = event.text;
                this.show = true;
                setTimeout(() => this.show = false, this.timeout);
            },
        }
    }
</script>
