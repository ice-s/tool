<template>
    <div class="login-wrapper">
        <!-- START Login Background Pic Wrapper-->
        <slot></slot>
        <!-- END Login Right Container-->
    </div>
</template>

<script>
    export default {
        beforeCreate() {

        },
        methods: {

        },
    }
</script>
<style>
    .login-wrapper {
        max-width: 600px;
        margin: 100px auto;
    }
</style>
