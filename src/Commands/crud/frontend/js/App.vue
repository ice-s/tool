<template>
    <component :is="layout">
        <router-view :layout.sync="layout"/>
    </component>
</template>

<script>
    import Layout from './components/Layout';

    export default {
        name: 'App',
        data() {
            return {
                layout: Layout,
            };
        },
        created() {
            // console.log("_________App created_________");
        },
        mounted() {
            // console.log("_________App mounted_________");
        }
    };
</script>
