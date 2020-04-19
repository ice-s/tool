import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex);

export default new Vuex.Store({
    plugins: [createPersistedState({
        storage: window.sessionStorage,
    })],
    state: {
        user: []
    },
    mutations: {
        SAVE_LOGIN_USER_INFO(state, user) {
            state.user = user
        }
    },
    actions: {
        saveUser(context, user) {
            context.commit('SAVE_LOGIN_USER_INFO', user)
        }
    },
    modules: { }
});
