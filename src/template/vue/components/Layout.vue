<template>
    <a-layout id="components-layout-demo-top-side-2">
        <Header></Header>
        <a-layout :class="'admin-layout'">
            <SidebarAdmin></SidebarAdmin>
            <a-layout style="padding: 0 24px 24px">
                <slot></slot>
            </a-layout>
        </a-layout>
    </a-layout>
</template>
<script>
    import Header from "./_partials/Header"
    import SidebarAdmin from "./admin/_partials/Sidebar"
    export default {
        data() {
            return {
                collapsed: false,
            };
        },
        components: {
            'Header': Header,
            'SidebarAdmin': SidebarAdmin
        },
    };
</script>

<style>
    #components-layout-demo-top-side-2 .logo {
        width: 120px;
        height: 31px;
        background: rgba(255, 255, 255, 0.2);
        margin: 16px 28px 16px 0;
        float: left;
    }

    .admin-layout {
        min-height: calc(100vh - 64px);
    }

    .ant-layout-content{
        background: #fff;
        padding: 24px;
        margin: 0;
        min-height: 480px;
    }
</style>