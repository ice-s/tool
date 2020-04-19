import AuthRoute from './auth/AuthRoute';
import AdminRoute from './admin/AdminRoute';

/*exception route*/
import VueRouter from 'vue-router';
import ErrorsRoute from './errors/ErrorsRoute';

let routes = [...AuthRoute, ...AdminRoute, ...ErrorsRoute];

const router = new VueRouter({
    mode: 'history',
    routes: routes
});

router.beforeEach((to, from, next) => {
    next();
});

export default router;
