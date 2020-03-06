require('./bootstrap');

import Vue from 'vue';
import axios from 'axios';
import App from './App';
import Config from './config';
import i18n from './lang/i18n';
import VueAxios from 'vue-axios';
import VueRouter from 'vue-router';
import Route from './routes/routes';

Vue.use(VueRouter);
Vue.use(VueAxios, axios);

axios.defaults.baseURL = Config.apiUrl;

Vue.router = Route;
Vue.prototype._appConfig = Config;
Vue.prototype._httpRequest = axios;

Vue.use(require('@websanova/vue-auth'), {
    auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
});

App.router = Vue.router;
App.i18n = i18n;

App.router.beforeEach((to, from, next) => {
    App.i18n.locale = 'en';
    next();
});

new Vue(App).$mount('#app');
