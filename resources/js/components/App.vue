<template>
    <div>
        <section class="hero is-primary">
            <div class="hero-body">
                <div class="container" :class="isLoggedIn ? 'has-text-right' : ''">
                    <h1 class="title">{{ isLoggedIn ? getAuth.user.username : 'Login to see calendar' }}</h1>
                    <a href="#" @click.prevent="logout" class="subtitle" v-if="isLoggedIn">Logout</a>
                </div>
            </div>
        </section>
        <section class="container">
            <calendar v-if="isLoggedIn"></calendar>
            <div class="columns pt-3">
                <div class="column is-4 is-offset-1 has-text-centered" v-if="!isLoggedIn">
                <h3 class="title has-text-black">Login</h3>
                <hr class="login-hr">
                <div class="box">
                    <form>
                        <p class="help is-danger mb-3" v-if="loginForm.error">{{loginForm.error}}</p>
                        <div class="field">
                            <div class="control">
                                <input class="input is-large" v-model="loginForm.username" placeholder="Please enter your username" type="text" autofocus="">
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <input class="input is-large" v-model="loginForm.password" placeholder="Please enter your password" type="password">
                            </div>
                        </div>
                        <button class="button is-block is-info is-large is-fullwidth" @click.prevent="login">Login <i class="fa fa-sign-in" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>
            <div class="column is-4 is-offset-1 has-text-centered" v-if="!isLoggedIn">
                <h3 class="title has-text-black">Register</h3>
                <hr class="login-hr">
                <div class="box">
                    <form>
                        <div class="field">
                            <div class="control">
                                <input class="input is-large" v-model="registerForm.username" placeholder="Please enter your username" type="text" autofocus="">
                            </div>
                            <p class="help is-danger" v-if="registerForm.errors && registerForm.errors.username">{{registerForm.errors.username[0]}}</p>
                        </div>

                        <div class="field">
                            <div class="control">
                                <input class="input is-large" v-model="registerForm.password" placeholder="Please enter your password" type="password">
                            </div>
                            <p class="help is-danger" v-if="registerForm.errors && registerForm.errors.password">{{registerForm.errors.password[0]}}</p>
                        </div>
                        <button class="button is-block is-info is-large is-fullwidth" @click.prevent="register">Register <i class="fa fa-sign-in" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>
            </div>
        </section>
        <loader v-if="loading"></loader>
    </div>
</template>

<script>
import Calendar from './Calendar.vue'
import { mapActions, mapGetters } from 'vuex'
import Loader from './Loader.vue'
export default {
    data () {
        return {
            loginForm: {
                username: '',
                password: '',
                error: null
            },
            registerForm: {
                username: '',
                password: '',
                errors: null
            },
        }
    },
    components: {
        'calendar': Calendar,
        'loader': Loader
    },
    methods: {
        ...mapActions([
            'setAuthToken',
            'setAuthUser'
        ]),
        login () {
            axios.post('http://127.0.0.1:8000/api/login', {
                username: this.loginForm.username,
                password: this.loginForm.password
            }).then(response => {
                localStorage.setItem('scheduler_user_token', 'Bearer ' + response.data.access_token);
                this.setAuthToken('Bearer ' + response.data.access_token)
                this.getUser('Bearer ' + response.data.access_token);
            }).catch(error => {
                this.loginForm.error = error.response.data.message
            });
        },
        register () {
            axios.post('http://127.0.0.1:8000/api/register', {
                username: this.registerForm.username,
                password: this.registerForm.password
            }).then(response => {
                alert(response.data.message);
                this.registerForm.username = ''
                this.registerForm.password = ''
            }).catch(error => {
                this.registerForm.errors = error.response.data.errors
            });
        },
        logout () {
            axios.post('http://127.0.0.1:8000/api/logout', {}, {
                headers: {"Authorization" : this.getAuth.token }
            }).then(response => {
                alert(response.data.message);
                this.setAuthToken(null)
                this.setAuthUser(null)
                localStorage.setItem('scheduler_user_token', '')
            }).catch(error => {
                alert('Something went wrong!')
            });
        },
        getUser (token) {
            axios.post('http://127.0.0.1:8000/api/me', {}, {
                headers: {"Authorization" : token }
            }).then(response => {
                this.setAuthUser(response.data)
            }).catch(error => {
                alert('Something went wrong!')
            });
        }
    },
    mounted () {

    },
    computed: {
        ...mapGetters(['getAuth', 'isLoggedIn', 'loading'])
    }
}
</script>