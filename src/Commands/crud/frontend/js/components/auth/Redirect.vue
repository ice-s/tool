<template>
    <div>
        <p>Redirecting...</p>
    </div>
</template>

<script>
    export default {
        name: 'redirect_auth',
        data() {
            return {};
        },
        created() {
            let app = this;
            app.$router.push({'name': 'dashboard'});
        }
    };
</script>
