import Role from "../consts/role";
import Layout from '../components/Layout';
import LayoutLogin from '../components/LayoutLogin';

export default {
    logout: function () {
        let app = this;
        app.$auth.logout({
            makeRequest: true,
            params: {},
            success: function () {
                sessionStorage.clear();
                window.location = "/cp/login";
                console.log("logout success");
            },
            error: function () {
                console.log("logout error");
            },
            redirect: false,
        });
    },
    redirectRole: function(keepLogin = false) {
        let app = this;
        if (this.$auth.user().roles && this.$auth.user().roles[0] !== undefined) {
            let role = this.$auth.user().roles[0];
            switch (role) {
                case Role.ROLE_ADMIN:
                    app.$router.push({'name': 'cp.admin.home'});
                    break;
            }
        } else {
            app.$router.push({'name': 'cp.login'});
        }
    }
}
