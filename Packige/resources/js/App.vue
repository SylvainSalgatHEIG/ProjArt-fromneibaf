<script setup>
import { computed, ref } from "vue";

import TheNav from "./components/TheNav.vue";
import eventsVue from "./views/events.vue";
import gradesVue from "./views/grades.vue";
import linksVue from "./views/links.vue";
import scheduleVue from "./views/schedule.vue";
import profileVue from "./views/profile.vue";
import registerVue from "./views/register.vue";

import Modal from "./components/Modal.vue";

let showModal = ref(false);

const msg = ref("");
let data = ref();

const routes = {
  "#schedule": {
    label: "Planification",
    icon: "calendar",
    component: scheduleVue,
  },
  "#grades": {
    label: "Notes",
    icon: "graph",
    component: gradesVue,
  },
  "#events": {
    label: "Actus",
    icon: "people",
    component: eventsVue,
  },
  "#links": {
    label: "Liens",
    icon: "chains",
    component: linksVue,
  },
  "#profile": {
    label: "Profile",
    icon: "chains",
    component: profileVue,
  },
};

const navRoutes = computed(() => {
  let baseRoute = ref({...routes});
  delete baseRoute.value["#profile"];
  return baseRoute.value;
});

const hash = ref(window.location.hash);

window.addEventListener(
  "hashchange",
  () => (hash.value = window.location.hash)
);

const curHash = computed(() =>
  routes[hash.value] ? hash.value : Object.keys(routes)[0]
);
const curComponent = computed(() => routes[curHash.value].component);
</script>

<template>
  <a href="#profile" v-if="hash != '#profile'">
    
    <div id="login"></div>
  </a>
  <the-nav id="mainMenu" :routes="navRoutes" :curHash="curHash"></the-nav>
  <main>
    <template v-for="(route, hash) of routes">
      <!-- v-if: recharge le composant lors du changement de page -->
      <div v-show="hash == curHash">
        <component :is="route.component" />
      </div>
    </template>
  </main>
</template>

<style>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap");

body {
  background-color: #0c223f;
  color: #ffffff;
}

main {
  padding-bottom: 93px;
}

.content {
  margin-left: auto;
  margin-right: auto;
  width: 312px;
}

h1 {
  margin-top: 55px;
  font-family: "outfit", sans-serif;
  font-style: normal;
  font-weight: 700;
  font-size: 32px;
  line-height: 36px;
  text-align: center;
  letter-spacing: -0.02em;
}

h2 {
  font-family: "Inter";
  font-style: normal;
  font-weight: 600;
  font-size: 16px;
  line-height: 22px;
}

#mainMenu {
  position: fixed;
  width: 100%;
  bottom: 0;

  background: linear-gradient(#112a4a 22.56%, #f84e35 331.81%);
  backdrop-filter: blur(30px);

  z-index: 100;
}

#mainMenu ul {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  flex-wrap: wrap;

  padding-top: 10px;
}

#mainMenu ul li {
  flex: 1 1 0px;
  text-align: center;
}

#mainMenu ul li a {
  opacity: 0.6;

  font-family: "Outfit", sans-serif;
  font-style: normal;
  font-weight: 400;
  font-size: 10px;
  line-height: 32px;
}

#mainMenu ul li .active {
  opacity: 1 !important;
  color: #f84e35;
}

#login {
  background-image: url("data:image/svg+xml,%3Csvg version='1.1' id='Calque_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 24 24' style='enable-background:new 0 0 24 24;' xml:space='preserve'%3E%3Cstyle type='text/css'%3E .st0%7Bfill:%23FFFFFF;%7D%0A%3C/style%3E%3Cpath class='st0' d='M12.1,13.5C12.1,13.5,12.1,13.5,12.1,13.5c-0.1,0-0.1,0-0.2,0c-2.3-0.1-4-1.8-4-4c0-2.2,1.8-4,4-4 c2.2,0,4,1.8,4,4C16,11.7,14.3,13.5,12.1,13.5C12.1,13.5,12.1,13.5,12.1,13.5z M12,7c-1.4,0-2.5,1.1-2.5,2.5c0,1.4,1.1,2.5,2.4,2.5 c0,0,0.1,0,0.2,0c1.3-0.1,2.4-1.2,2.4-2.5C14.5,8.1,13.4,7,12,7z'/%3E%3Cpath class='st0' d='M12,22.8c-2.7,0-5.3-1-7.2-2.8c-0.2-0.2-0.3-0.4-0.2-0.6c0.1-1.2,0.9-2.3,2.1-3.1c3-2,7.8-2,10.8,0 c1.2,0.8,2,1.9,2.1,3.1c0,0.2-0.1,0.5-0.2,0.6C17.3,21.8,14.7,22.8,12,22.8z M6.1,19.1c1.7,1.4,3.7,2.1,5.9,2.1 c2.2,0,4.3-0.8,5.9-2.1c-0.2-0.6-0.7-1.2-1.4-1.7c-2.5-1.6-6.6-1.6-9.1,0C6.7,17.9,6.3,18.5,6.1,19.1z'/%3E%3Cpath class='st0' d='M12,22.7C6.1,22.7,1.2,17.9,1.2,12S6.1,1.2,12,1.2S22.8,6.1,22.8,12S17.9,22.7,12,22.7z M12,2.7 c-5.1,0-9.2,4.2-9.2,9.2s4.2,9.2,9.2,9.2s9.2-4.1,9.2-9.2S17.1,2.7,12,2.7z'/%3E%3C/svg%3E%0A");
  background-repeat: no-repeat;
  background-size: 36px;
  height: 44px;
  width: 44px;
  background-position: calc(50%);
  position: absolute;
  margin-left: 82.5%;
  margin-right: 0;
}

.card {
  background-color: #0c223f;
}

.form-login {
  width: 312px;
  margin: 20px 0;
  box-sizing: border-box;
  outline: 0;
  border-width: 0 0 1px;
  border-color: white;
  background-color: #0c223f;
  color: white;
}

.form-check {
  margin-top: 10px;
  margin-bottom: 30px;
  padding-left: 0px;
}

.btn-primary {
  padding: 16px 50px;
  gap: 10px;
  position: absolute;
  width: 330px;
  bottom: 30px;
  background: #f84e35;
  border-radius: 40px;

  margin: 50px auto 0 auto;

  font-family: "Outfit";
  font-style: normal;
  font-weight: 600;
  font-size: 20px;
  line-height: 25px;

  color: #ffffff;

  border: none;
}

.password-forgotten {
  font-size: small;
  font-weight: 300;
  opacity: 80%;
  color: white;
}

.nav-link {
  color: white !important;
}

.login-title {
  margin-top: 10px;
  margin-bottom: 30px;
}

.register-subtitle {
  font-family: "outfit", sans-serif;
  font-style: normal;
  font-weight: 200;
  font-size: 17px;
  text-align: center;
  letter-spacing: -0.02em;
  margin-bottom: 30px;
}

.register-title {
  margin-top: 10px;
  margin-bottom: 10px;
}

.back {
  position: relative;
  display: inline-block;
  text-decoration: none;
  padding: 0px 10px 10px 40px;
}

.back div {
  top: 10px;
  left: 30px;
  content: "";
  width: 30px;
  height: 30px;
  display: block;
  overflow: hidden;
  position: absolute;
  border-radius: 50%;
  transform: scale(1);
  background-color: #e9e7f2;
  transition: transform 400ms 0s cubic-bezier(0.2, 0, 0, 1.6);
}

.back div::after {
  top: 0;
  left: 0;
  content: "";
  width: 60px;
  height: 30px;
  position: absolute;
  background-position: 0 0;
  background-image: url("https://s3-eu-west-1.amazonaws.com/thomascullen-codepen/back.svg");
  transition: transform 400ms 0s cubic-bezier(0.2, 0, 0, 1);
}

.back:hover div {
  transform: scale(1.1);
  background-color: white;
  box-shadow: 0 2px 10px 0 rgba(185, 182, 198, 0),
    0 1px 3px 0 rgba(175, 172, 189, 0.25);
}

.back:hover div::after {
  transform: translateX(-30px);
}

#group-name {
  margin: 20px 0;
  background: #0c223f;
  color: rgba(255, 255, 255, 0.6);
  outline:0px;
  border-width: 0 0 1px;
  border-color: white;
}

</style>

