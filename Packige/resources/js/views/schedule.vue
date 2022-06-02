<script setup>
import {computed, ref} from 'vue';
import TheNav from '../components/TheNav.vue';
import deadlinesVue from './subviews/deadlines.vue';
import planningVue from './subviews/planning.vue';

const msg = ref('');

 const subRoutes = {
    '#schedule#planning': {
      label: 'Horaire',
      component: planningVue,
    },
    '#schedule#deadlines': {
      label: 'Deadline',
      component: deadlinesVue,
    }
};

const hash = ref(window.location.hash);

  window.addEventListener('hashchange', () => hash.value = window.location.hash);

  const curHash = computed(() => subRoutes[hash.value] ? hash.value : Object.keys(subRoutes)[0]);
  const curComponent = computed(() => subRoutes[curHash.value].component);

</script>

<template>
	<h1>Page Schedule</h1>

	<the-nav :routes="subRoutes" :curHash="curHash"></the-nav>

	<main>

    <template v-for="(subRoutes, hash) of subRoutes">
      <div v-show="hash == curHash">
        <component :is="subRoutes.component"/>
      </div>
    </template>

  </main>
</template>