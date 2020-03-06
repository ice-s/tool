<template>
    <div class="login-box">
        <div class="login-logo">
            <a href="../../index2.html"><b>Admin</b>LTE</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <div class="alert alert-danger" v-if="error">
                    <p>Check username or password</p>
                </div>
                <form id="form-login" role="form" @submit.prevent="login" method="post">
                    <div class="input-group mb-3">
                        <input v-model="email" type="email" class="form-control" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" v-model="password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import LayoutLogin from '../LayoutLogin';

    export default {
        data() {
            return {
                email: null,
                password: null,
                error: false
            }
        },
        mounted() {

        },
        beforeCreate() {
            this.$emit('update:layout', LayoutLogin);
        },
        created() {

        },
        methods: {
            login() {
                let app = this;
                this.$auth.login({
                    params: {
                        email: app.email,
                        password: app.password
                    },
                    success: function (response) {
                        app.$router.push({'name': 'dashboard.index'});
                    },
                    error: function () {
                        this.error = true
                    },
                    rememberMe: true,
                    redirect: false,
                    fetchUser: true,
                });
            },
        }
    }
</script>
