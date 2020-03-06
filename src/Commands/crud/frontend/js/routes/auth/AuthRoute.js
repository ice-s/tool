import Redirect from '../../components/auth/Redirect';
import Login from '../../components/auth/Login';

export default [
    {
        path: '/',
        name: 'redirect_auth',
        component: Redirect,
        meta: {
            auth: true
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    }
];
