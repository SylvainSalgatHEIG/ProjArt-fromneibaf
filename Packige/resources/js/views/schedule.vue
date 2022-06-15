<script setup>
import {computed, ref} from 'vue';
import TheNav from '../components/TheNav.vue';
import deadlinesVue from './subviews/deadlines.vue';
import planningVue from './subviews/planning.vue';

/* Subroutes for schedule page  */
const subRoutes = {
  '#schedule#planning': {
    label: 'Horaire',
    component: planningVue,
  },
  '#schedule#deadlines': {
    label: 'Tâches',
    component: deadlinesVue,
  }
}

const hash = ref(window.location.hash);
window.addEventListener('hashchange', () => hash.value = window.location.hash);
const curHash = computed(() => subRoutes[hash.value] ? hash.value : Object.keys(subRoutes)[0]);
const curComponent = computed(() => subRoutes[curHash.value].component);

</script>

<template>
	<h1>Planification</h1>

	<the-nav id="scheduleMenu" :routes="subRoutes" :curHash="curHash"></the-nav>

	<main>

    <template v-for="(subRoutes, hash) of subRoutes">
      <div v-show="hash == curHash">
        <component :is="subRoutes.component"/>
      </div>
    </template>

  </main>
</template>

<style>
  #scheduleMenu {
    text-align: center;

    font-family: 'Inter', sans-serif;
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 22px;
    letter-spacing: -0.02em;
  }

  #scheduleMenu ul li {
    width: 156px;
    border-bottom: 1px solid white;
  }

  #scheduleMenu ul li.active {
    width: 156px;
    border-bottom: 1px solid #F84E35;
  }

  #scheduleMenu ul li a {
    opacity: 1;
    color: #FFFFFF;
  }

  #scheduleMenu ul li a.active {
    font-weight: 600;
    color: #F84E35;
  }
</style>