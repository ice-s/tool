<template>
    <div>
        Dashboard
    </div>
</template>
<script>
    import Layout from '../Layout';

    export default {
        data: function () {

        },
        created() {
            this.$emit('update:layout', Layout);
        },
        components: {
            Layout
        },
        methods: {

        }
    };
</script>
