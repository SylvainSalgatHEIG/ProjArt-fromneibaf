<script setup>
import { computed, ref } from "vue";

import TheNav from "./components/TheNav.vue";
import eventsVue from "./views/events.vue";
import gradesVue from "./views/grades.vue";
import linksVue from "./views/links.vue";
import scheduleVue from "./views/schedule.vue";
import registerVue from "./views/register.vue";

import Modal from "./components/Modal.vue";

let showModal = ref(false);

const msg = ref("");
let data = ref();

 const routes = {
    '#schedule': {
      label: 'Planification',
      icon: 'calendar',
      component: scheduleVue,
    },
    '#grades': {
      label: 'Notes',
      icon: 'graph',
      component: gradesVue,
    },
    '#events': {
      label: 'Actus',
      icon: 'people',
      component: eventsVue,
    },
    '#links': {
      label: 'Liens',
      icon: 'chains',
      component: linksVue,
    }
};

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

  <div id="login"><a href="/login">Se connecter</a></div>
	<the-nav id="mainMenu" :routes="routes" :curHash="curHash"></the-nav>
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

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap');

  * {
    overscroll-behavior: none;
    -webkit-overflow-scrolling: none;
  }

  body {
    background-color: #0C223F;
    color: #FFFFFF;
  }

  main {
    padding-bottom: 93px;
  }

  h1 {
    margin-top: 55px;
    font-family: 'outfit', sans-serif;
    font-style: normal;
    font-weight: 700;
    font-size: 32px;
    line-height: 36px;
    text-align: center;
    letter-spacing: -0.02em;
  }

  #mainMenu {
    position: fixed;
    width: 100%;
    bottom: 0;
    
    background: linear-gradient(#112A4A 22.56%, #F84E35 331.81%);
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
    
    font-family: 'Outfit', sans-serif;
    font-style: normal;
    font-weight: 400;
    font-size: 10px;
    line-height: 32px;
  }

  #mainMenu ul li .active {
    opacity: 1 !important;
    color: #F84E35;
  }

</style>
