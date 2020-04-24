import Redirect from '../../components/Redirect';
import Login from '../../components/Login';

export default [
    {
        path: '/cp/',
        name: 'cp',
        component: Redirect,
    },
    {
        path: '/cp/login',
        name: 'cp.login',
        component: Login
    },
];
