import VueRouter from 'vue-router';
import AuthRoute from './auth/AuthRoute';
import ErrorsRoute from './errors/ErrorsRoute';
import DashboardRoute from "./dashboard/DashboardRoute";

const router = new VueRouter({
    mode: 'history',
    routes: [
        ...AuthRoute,
        ...ErrorsRoute,
        ...DashboardRoute
    ]
});

router.beforeEach((to, from, next) => {
    next();
});

export default router;
