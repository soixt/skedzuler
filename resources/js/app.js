window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Vue = require('vue');

import Vuex, { mapGetters, mapActions } from 'vuex'
import App from './components/App.vue'

const store = new Vuex.Store({
    state: {
        auth: {
            user: null,
            token: null
        },
        loading: true
    },
    actions: {
        setAuthUser ({commit}, user) {
            commit('SET_AUTH_USER', user)
        },
        setAuthToken ({commit}, token) {
            commit('SET_AUTH_TOKEN', token)
        },
        setLoading ({commit}, load) {
            commit('SET_LOADING', load)
        }
    },
    mutations: {
        SET_AUTH_USER (state, user) {
            state.auth.user = user
        },
        SET_AUTH_TOKEN (state, token) {
            state.auth.token = token
        },
        SET_LOADING (state, load) {
            state.loading = load
        }
    },
    getters: {
        getAuth: (state) => {
            return state.auth
        },
        isLoggedIn: (state) => {
            return state.auth.token && state.auth.user
        },
        loading: (state) => {
            return state.loading
        }
    }
});

const app = new Vue({
    el: '#app',
    render: function (h) {
        return h(App)
    },
    store,
    methods: {
        ...mapActions([
            'setAuthToken',
            'setAuthUser',
            'setLoading'
        ]),
        appInit () {
            let token = localStorage.getItem('scheduler_user_token')
            if (token && token != '') {
                this.setAuthToken(token)
                this.getUser(token);
            } else {
                this.setLoading(false)
            }
        },
        getUser (token) {
            axios.post('http://127.0.0.1:8000/api/me', {}, {
                headers: {"Authorization" : token }
            }).then(response => {
                console.log(response.data)
                this.setAuthUser(response.data)
                this.setLoading(false)
            }).catch(error => {
                console.log(error.response.data.message)
                this.setLoading(false)
            });
        }
    },
    computed: {
        ...mapGetters(['getAuth'])
    },
    mounted () {
        this.appInit()
    }
});