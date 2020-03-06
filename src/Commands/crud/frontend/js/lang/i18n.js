import Vue from 'vue';
import VueI18n from 'vue-i18n';
import ja from './src/ja.json';
import en from './src/en.json';

Vue.use(VueI18n);
const messages = {
    ja: ja,
    en: en,
};
const i18n = new VueI18n({
    locale: 'ja', // set locale
    messages,
    fallbackLocale: 'ja',
});

export default i18n;
