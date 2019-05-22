<template>
    <div v-if="name" class="form-inline">
        <label class="mr-3">Current playing: </label>
        <input class="form-control" v-model="name" readonly>
    </div>
</template>

<script>
    export default {
        data: () => ({
           name: '',
        }),

        props: {
            subscriber: null,
        },

        mounted() {
            this.name = this.subscriber ? this.subscriber.name : '';
            VueEvents.$on('current-subscription', payload => {
                this.name = payload.name;
            })
        },
    }
</script>
