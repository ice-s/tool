<template>
    <a-layout-sider width="200" style="background: #fff">
        <a-menu
                mode="inline"
                :defaultSelectedKeys="['1']"
                :defaultOpenKeys="['sub1']"
                :style="{ height: '100%', borderRight: 0 }"
        >
            <a-sub-menu key="sub1">
                <span slot="title"><a-icon type="user" />Management</span>
                <a-menu-item key="1">option1</a-menu-item>
                <a-menu-item key="2">option2</a-menu-item>
                <a-menu-item key="3">option3</a-menu-item>
                <a-menu-item key="4">option4</a-menu-item>
            </a-sub-menu>
            <a-menu-item key="menu1" v-on:click="clickLogout">
                <a-icon type="logout" />
                <span class="">Logout</span>
            </a-menu-item>
        </a-menu>
    </a-layout-sider>
</template>

<script>
    export default {
        data: function () {
            return {

            }
        },
        methods: {
            clickLogout() {
                this.logout();
            }
        }
    }
</script>
