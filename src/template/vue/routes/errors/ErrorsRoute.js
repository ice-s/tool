import E403 from '../../components/errors/E403.vue';
import E404 from '../../components/errors/E404.vue';

export default [
    {
        path: '/cp/*',
        name: 'cp.404',
        component: E404
    },
    {
        path: '/cp/403',
        name: 'cp.403',
        component: E403
    },
];
