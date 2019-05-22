<template>
    <div class="float-right" v-on:update-subscribers="updateSubscribers()">
        <span :class="'bg-side-' + subscription.side" class="badge mr-1" v-for="subscription in subscriptions">
            {{ subscription.user.name }}
        </span>
    </div>
</template>

<script>
    export default {
        data: () => ({
           subscriptions: [],
        }),

        props: {
            subscribersUri: {
                type: String,
            },
            gameSession: {
                type: Number,
            }
        },

        mounted() {
            this.updateSubscribers();
            Echo.private(`sah.subscriber.${this.gameSession}`)
                .listen('SubscribeEvent', (e) => {
                    this.subscriptions.push(e);
                    VueEvents.$emit('notification', {
                        text: `User '${e.user.name}' subscribed to this game.`
                    });
                });
        },

        methods: {
            updateSubscribers() {
                axios.get(this.subscribersUri).then(response => {
                    this.subscriptions = response.data;
                });
            }
        }
    }
</script>
