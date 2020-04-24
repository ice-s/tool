import AdminHome from "../../components/admin/Home";
import AdminTest from "../../components/admin/test/Test";
import Role from "../../consts/role";

export default [
    {
        path: Role.ROLE_ADMIN_PREFIX_URL,
        name: 'cp.admin.home',
        component: AdminHome,
        meta: Role.ROLE_AUTH_ADMIN_CONFIG
    },
    {
        path: Role.ROLE_ADMIN_PREFIX_URL + "test",
        name: 'cp.admin.test',
        component: AdminTest,
        meta: Role.ROLE_AUTH_ADMIN_CONFIG
    },
];
