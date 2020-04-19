<template>
    <div>
        <a-breadcrumb style="margin: 16px 0">
            <a-breadcrumb-item>Home</a-breadcrumb-item>
            <a-breadcrumb-item>Admin</a-breadcrumb-item>
            <a-breadcrumb-item>Test</a-breadcrumb-item>
        </a-breadcrumb>
        <a-layout-content>
            <h3>Admin Test</h3>
        </a-layout-content>
    </div>
</template>

<script>
    export default {
        beforeCreate() {
            console.log("test");
        }
    }
</script>

<style scoped>

</style>
