import Dashboard from '../../components/dashboard/Dashboard';

export default [
    {
        path: '/dashboard',
        name: 'dashboard.index',
        component: Dashboard,
        query: {},
        meta: {
            auth: true
        }
    }
];
