<script setup>
import { computed, ref } from "vue";
import { useFetch } from "../composables/fetch";
import NotConnected from "../components/NotConnected.vue";

const { data: connexionStatus } = useFetch("/api/connexion/status");
</script>

<template>
  <h1>Mon compte</h1>
  <div id="notConnected" v-if="!connexionStatus">
    <not-connected></not-connected>
  </div>
  <div id="connected" v-if="connexionStatus"></div>
</template>
<style>
</style>