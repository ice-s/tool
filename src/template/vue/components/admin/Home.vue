<template>
    <div>
        <a-breadcrumb style="margin: 16px 0">
            <a-breadcrumb-item>Home</a-breadcrumb-item>
            <a-breadcrumb-item>Admin</a-breadcrumb-item>
            <a-breadcrumb-item>Dashboard</a-breadcrumb-item>
        </a-breadcrumb>
        <a-layout-content>
            <h3>Admin Page</h3>
        </a-layout-content>
    </div>
</template>

<script>
    import Layout from "../Layout";

    export default {
        beforeCreate() {
            this.$emit('update:layout', Layout);
            console.log("Home");
        }
    }
</script>

<style scoped>

</style>
