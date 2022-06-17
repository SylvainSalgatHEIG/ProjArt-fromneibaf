<script setup>

</script>
<template>
    <div id="notLogMessage">
        <p>Tu dois être connecté pour accéder à cette page</p>

        <a href="/login">Se connecter</a>
    </div>
</template>
<style scoped>
    #notLogMessage {
        width: 312px;
        margin: 25px auto 0 auto;

    }

    p {
       font-family: 'Inter';
        font-style: normal;
        font-weight: 400;
        font-size: 14px;
        line-height: 22px;

        letter-spacing: -0.02em;
        font-feature-settings: 'calt' off;

        color: #FFFFFF; 
    }

    a {
        font-family: 'Inter';
        font-style: normal;
        font-weight: 600;
        font-size: 14px;
        line-height: 22px;

        letter-spacing: -0.02em;
        font-feature-settings: 'calt' off;

        color: #C8D7F4;
    }
</style>