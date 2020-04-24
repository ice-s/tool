<template>
    <a-layout-header class="header">
        <div class="logo" />
        <a-menu
                theme="dark"
                mode="horizontal"
                :defaultSelectedKeys="['2']"
                :style="{ lineHeight: '64px' }"
        >
            <a-menu-item key="3">
                <a href="#" class="">
                    <span class="pull-left">{{ name }}</span>
                    <span class="pull-right"><i class="fa fa-sign-out"></i></span>
                </a>
            </a-menu-item>
        </a-menu>
    </a-layout-header>
</template>

<script>

    export default {
        props: [ 'user' ],
        data: function () {
            return {
                name: this.$auth.user().name,
                language: 'ja'
            }
        },
        created() {
            this.loadLanguage();
        },
        replace: false,
        methods: {
            changeLanguage() {
                let app = this;
            },
            loadLanguage() {
                let app = this;
            }
        }
    }
</script>
