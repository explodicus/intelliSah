
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('jquery');

window.Vue = require('vue');

window.VueEvents = new Vue({});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('subscribers', require('./components/Subscribers.vue').default);
Vue.component('notification', require('./components/Notification.vue').default);
Vue.component('game-table', require('./components/GameTable.vue').default);
Vue.component('current-playing', require('./components/CurrentPlaying.vue').default);

const app = new Vue({
    el: '#app'
});
