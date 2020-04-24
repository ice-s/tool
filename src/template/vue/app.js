require('./bootstrap');

import Vue from 'vue';
import axios from 'axios';
import Antd from 'ant-design-vue';
import App from './App.vue';
import AppFilter from './filters/AppFilter';
import 'ant-design-vue/dist/antd.css';

import Config from './config';
import i18n from './lang/i18n';
import './validate/vee-validate';
import VueAxios from 'vue-axios';
import VueRouter from 'vue-router';
import Const from './consts/app.js';
import Route from './routes/routes';
import mixins from './mixins/mixins';
import VueCookies from 'vue-cookies';
import Paginate from 'vuejs-paginate';
import { ValidationObserver, ValidationProvider } from 'vee-validate';
import { localize } from "vee-validate";
import store from "./store"

Vue.component('paginate', Paginate);
Vue.component('ValidationObserver', ValidationObserver);
Vue.component('ValidationProvider', ValidationProvider);
Vue.use(VueRouter);
Vue.use(VueCookies);
Vue.use(VueAxios, axios);
Vue.use(Antd);

axios.defaults.baseURL = Config.apiUrl;

Vue.router = Route;
Vue.store = store;
Vue.prototype._const = Const;
Vue.prototype._appConfig = Config;
Vue.prototype._httpRequest = axios;

Vue.use(require('@websanova/vue-auth'), {
    auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
});

App.router = Vue.router;
App.store = Vue.store;
App.i18n = i18n;
App.$cookies = VueCookies;
App.$localize = localize;

App.router.beforeEach((to, from, next) => {
    let language = 'ja';

    App.$localize(language);
    App.i18n.locale = language;
    next();
});

Vue.mixin(mixins);

new Vue(App).$mount('#app');
