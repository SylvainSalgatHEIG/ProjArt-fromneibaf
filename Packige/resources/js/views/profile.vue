<script setup>
import { computed, ref, watchEffect } from "vue";
import { useFetch, usePost } from "../composables/fetch";
import NotConnected from "../components/NotConnected.vue";
import { userInfos } from "../stores/userInfos.js";

const { data: connexionStatus } = useFetch("/api/connexion/status");

// let view = ref('list');
let view = ref('');
// let view = computed('');

watchEffect(() => {
  if(userInfos.value != null){
    view.value = userInfos.value.schedule_type
  }
})

function save(){
  console.log(view.value);
  userInfos.value.schedule_type = view.value
  usePost({ url: "/api/user/changeScheduleView", data: {type: view.value} })
}

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
    <hr />
    <h2>Préférences</h2>
    Vue du calendrier :
    <form @submit.prevent="save">
      <label for="calendarView">Calendrier</label>
      <input
        type="radio"
        id="calendatView"
        name="view"
        value="calendar"
        v-model="view"
      />
      <label for="list">Liste</label>
      <input
        type="radio"
        id="listView"
        name="view"
        value="list"
        v-model="view"
      />
      <button>Enregistrer</button>
    </form>
    <br />
    <button @click="logout">Déconnexion</button>
  </div>
</template>
<style>
</style>