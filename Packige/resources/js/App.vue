<script setup>
import {computed, ref} from 'vue';

import TheNav from './components/TheNav.vue';
import eventsVue from './views/events.vue';
import gradesVue from './views/grades.vue';
import linksVue from './views/links.vue';
import scheduleVue from './views/schedule.vue';
import registerVue from './views/register.vue';



const msg = ref('');
let data = ref();

 const routes = {
    '#schedule': {
      label: 'Horaire',
      component: scheduleVue,
    },
    '#grades': {
      label: 'Notes',
      component: gradesVue,
    },
    '#events': {
      label: 'AGE',
      component: eventsVue,
    },
    '#links': {
      label: 'Liens',
      component: linksVue,
    }
};

const hash = ref(window.location.hash);

  window.addEventListener('hashchange', () => hash.value = window.location.hash);

  const curHash = computed(() => routes[hash.value] ? hash.value : Object.keys(routes)[0]);
  const curComponent = computed(() => routes[curHash.value].component);

</script>

<template>

	<the-nav :routes="routes" :curHash="curHash"></the-nav>

	<main>
    <template v-for="(route, hash) of routes">
      <div v-show="hash == curHash">
        <component :is="route.component"/>
      </div>
    </template>
  </main>
<register-vue></register-vue>
</template>