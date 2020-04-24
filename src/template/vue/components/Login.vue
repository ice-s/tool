<template>
    <a-form
            id="components-form-demo-normal-login"
            :form="form"
            class="login-form"
            @submit="handleSubmit"
    >
        <a-form-item>
            <a-input
                    v-decorator="['email',
                          { rules: [{ required: true, type: 'email', message: 'Please input your email!' }] },
                        ]"
                    placeholder="Username"
            >
                <a-icon slot="prefix" type="user" style="color: rgba(0,0,0,.25)"/>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-input
                    v-decorator="[
          'password',
          { rules: [{ required: true, message: 'Please input your Password!' }] },
        ]"
                    type="password"
                    placeholder="Password"
            >
                <a-icon slot="prefix" type="lock" style="color: rgba(0,0,0,.25)"/>
            </a-input>
        </a-form-item>
        <a-form-item>
            <a-checkbox
                    v-decorator="[
          'remember',
          {
            valuePropName: 'checked',
            initialValue: true,
          },
        ]"
            >
                Remember me
            </a-checkbox>
            <a class="login-form-forgot" href="">
                Forgot password
            </a>
            <a-button type="primary" html-type="submit" class="login-form-button">
                Log in
            </a-button>
            Or
            <a href="">
                register now!
            </a>
        </a-form-item>
    </a-form>
</template>

<script>
    import LayoutLogin from './LayoutLogin';
    import Role from "../consts/role";

    export default {
        layout: LayoutLogin,
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
            let app = this;
            this.$emit('update:layout', LayoutLogin);
            this.form = this.$form.createForm(this, {name: 'normal_login'});
            if (this.$auth.user().roles && this.$auth.user().roles[0] !== undefined) {
                let role = this.$auth.user().roles[0];
                switch (role) {
                    case Role.ROLE_ADMIN:
                        app.$router.push({'name': 'cp.admin.home'});
                        break;
                }
            }
        },
        created() {
        },
        methods: {
            handleSubmit(e) {
                e.preventDefault();
                let app = this;
                this.form.validateFields((err, values) => {
                    if (!err) {
                        console.log('Received values of form: ', values);
                    }

                    app.$auth.login({
                        params: values,
                        success: function (response) {
                            app.$router.push({'name': 'cp.admin.home'});
                        },
                        error: function () {
                            this.error = true
                        },
                        rememberMe: true,
                        redirect: false,
                        fetchUser: true,
                    });
                });
            }
        }
    }
</script>

<style>
    #components-form-demo-normal-login .login-form {
        max-width: 300px;
    }

    #components-form-demo-normal-login .login-form-forgot {
        float: right;
    }

    #components-form-demo-normal-login .login-form-button {
        width: 100%;
    }
</style>