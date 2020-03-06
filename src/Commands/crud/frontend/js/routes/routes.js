import VueRouter from 'vue-router';
import AuthRoute from './auth/AuthRoute';
import DashboardRoute from "./dashboard/DashboardRoute";

const router = new VueRouter({
    mode: 'history',
    routes: [
        ...AuthRoute,
        ...DashboardRoute
    ]
});

router.beforeEach((to, from, next) => {
    next();
});

export default router;
