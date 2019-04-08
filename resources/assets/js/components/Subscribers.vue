<template>
    <div class="float-right" v-on:update-subscribers="updateSubscribers()">
        <span class="badge badge-success mr-1" v-for="subscriber in subscribers">
            {{ subscriber.name }}
        </span>
    </div>
</template>

<script>
    export default {
        data: () => ({
           subscribers: [],
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
                    this.subscribers.push(e.user);
                    VueEvents.$emit('notification', {
                        text: `User '${e.user.name}' subscribed to this game.`
                    });
                });
        },

        methods: {
            updateSubscribers() {
                axios.get(this.subscribersUri).then(response => {
                    this.subscribers = response.data;
                });
            }
        }
    }
</script>
