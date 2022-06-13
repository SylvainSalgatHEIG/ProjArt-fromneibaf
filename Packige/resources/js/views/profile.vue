<script setup>
import { computed, ref } from "vue";
import { useFetch } from "../composables/fetch";
import NotConnected from "../components/NotConnected.vue";

const { data: connexionStatus } = useFetch("/api/connexion/status");
const { data: userInfos } = useFetch("/api/user/get");

function logout() {
	window.location.href = "/logout";
}
</script>

<template>
  <h1>Mon compte</h1>
  <div id="notConnected" v-if="!connexionStatus">
    <not-connected></not-connected>
  </div>
  <div id="connected" v-if="connexionStatus">
    <h2>Infos</h2>
    <h3>Email</h3>
    <p v-if="userInfos">{{ userInfos.email }}</p>
    <h3>Classe</h3>
    <ul v-if="userInfos" v-for="group of userInfos['groups']">
      <li>{{ group }}</li>
    </ul>
    <button>Ajouter une classe +</button>
    <hr />
    <h2>Préférences</h2>
    Vue du calendrier :
	<br>
    <button @click="logout">Déconnexion</button>
  </div>
</template>
<style>
</style>