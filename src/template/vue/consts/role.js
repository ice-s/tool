const ROLE_ADMIN = 'admin';
const ROLE_ADMIN_PREFIX_URL = '/cp/admin/';
const ROLE_ADMIN_PREFIX_NAME = 'cp.admin.';
const ROLE_AUTH_ADMIN_CONFIG = {
    auth: {
        roles: ROLE_ADMIN,
        redirect: {name: 'cp.login'},
        forbiddenRedirect: '/cp/403'
    }
};

const ROLE_EDITOR = 'editor';

export default {
    ROLE_ADMIN: ROLE_ADMIN,
    ROLE_AUTH_ADMIN_CONFIG: ROLE_AUTH_ADMIN_CONFIG,
    ROLE_ADMIN_PREFIX_URL: ROLE_ADMIN_PREFIX_URL,
    ROLE_ADMIN_PREFIX_NAME: ROLE_ADMIN_PREFIX_NAME,

    ROLE_EDITOR: ROLE_EDITOR,
}
