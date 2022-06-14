<script setup>
import { computed, ref, watchEffect } from "vue";
import { useFetch, usePost } from "../composables/fetch";
import NotConnected from "../components/NotConnected.vue";
import { userInfos } from "../stores/userInfos.js";
import { useLocalstorage } from "../composables/localstorage";

const { data: connexionStatus } = useFetch("/api/connexion/status");

// let view = ref('list');
let view = ref("");
// let view = computed('');

watchEffect(() => {
  if (userInfos.value != null) {
    view.value = userInfos.value.schedule_type;
  }
});

function save() {
  console.log(view.value);
  userInfos.value.schedule_type = view.value;
  usePost({ url: "/api/user/changeScheduleView", data: { type: view.value } });
}

function changeViewPlanning(event) {
  const typeView = event.target.value;
  save();
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
    <h1 v-if="userInfos" id="userFullname">{{ userInfos.fullname }}</h1>
    <div class="content">
      <h2>Infos</h2>
      <h3>Email</h3>
      <p class="userInfoOpacity" v-if="userInfos">{{ userInfos.email }}</p>
      <h3>Classe</h3>
      <ul v-if="userInfos">
        <li class="userInfoOpacity" v-for="group of userInfos['groups']">
          {{ group }}
        </li>
      </ul>
      <hr />
      <h2>Préférences</h2>
      <div class="left">
        <p>Vue du calendrier :</p>
      </div>
      <div class="right">
        <label for="calendarView">Calendrier</label>
        <input
          @change="changeViewPlanning($event)"
          type="radio"
          id="calendatView"
          name="view"
          value="calendar"
          v-model="view"
        />
        <br />
        <br />
        <label for="list">Liste</label>
        <input
          @change="changeViewPlanning($event)"
          type="radio"
          id="listView"
          name="view"
          value="list"
          v-model="view"
        />
      </div>
      <br />
      <br />
      <button @click="logout" id="logoutBtn">Déconnexion</button>
    </div>
  </div>
</template>

<style scoped>
#userFullname {
  font-family: "Outfit";
  font-style: normal;
  font-weight: 700;
  font-size: 24px;
  line-height: 36px;

  text-align: center;
  letter-spacing: -0.02em;

  margin-top: 14px;
}

h2 {
  font-family: "Outfit";
  font-style: normal;
  font-weight: 700;
  font-size: 24px;
  line-height: 36px;

  letter-spacing: -0.02em;
  font-feature-settings: "calt" off;
}

h3 {
  font-family: "Inter";
  font-style: normal;
  font-weight: 400;
  font-size: 16px;
  line-height: 22px;

  letter-spacing: -0.02em;
  font-feature-settings: "calt" off;

  margin-top: 30px;
}

h3:first-of-type {
  margin-top: 14px;
}

.userInfoOpacity {
  color: rgb(255, 255, 255, 0.6);
}

ul {
  padding: 0;
}

ul li {
  display: inline;
}

ul li:after {
  content: " | ";
}

ul li:last-child:after {
  content: "";
}

.left,
.right {
  width: 50%;
  height: 80px;
}

.left {
  float: left;
}

.right {
  float: right;
}

input[type="radio"] {
  height: 25px;
  width: 25px;
  appearance: none;
  background-color: #0c223f;
  margin: 0;
  color: #0c223f;
  border: 0.15em solid rgba(255, 255, 255, 0.6);
  border-radius: 50%;
  margin: 0 0px -6px 15px;

  float: right;
  margin-right: 35px;
}
input[type="radio"]:first {
  margin-right: 35px;
}

input[type="radio"]:checked {
  border: 2px solid #77b0c5;
  background-color: #77b0c5;
  box-shadow: 0px 0px 0px 4px #0c223f inset;
}
.container input:checked .checkmark {
  background-color: #2196f3;
}

#logoutBtn {
  width: 177px;
  height: 45px;

  align-self: center;

  background: #f84e35;
  border-radius: 40px;

  border: none;

  font-family: "Outfit";
  font-style: normal;
  font-weight: 600;
  font-size: 20px;
  line-height: 25px;

  color: #ffffff;

  display: grid;
  margin: 70px auto 0 auto;

  padding-top: 8px;
}
</style>