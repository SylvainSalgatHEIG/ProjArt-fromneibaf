<script setup>

  const props = defineProps({
    routes: {
      type: Object,
      required: true
    },
    curHash: {
      type: String,
      required: true
    }
  });

</script>

<template>
  <nav>
    <ul>
      <li v-for="(route, hash) of routes">
        <a :href="hash" :class="{ active: hash == curHash }">
          {{ route.label }}
        </a>
      </li>
    </ul>
  </nav>
</template>

<style scoped>
  ul {
    padding: 0;
    margin: 0;
    border-bottom: solid black 1px;
  }
  li {
    display: inline-block;
  }
  a {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: black;
    display: inline-block;
    width: 100%;
  }
  .active {
    color: var(--color-deco);
  }
</style>