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
          <img v-if="route.icon" class="icon" :class="{ active: hash == curHash }" :src="'./icons/' + route.icon + '.svg'"><br>
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
  }
  li {
    display: inline-block;
  }
  a {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #FFFFFF;
    opacity: 0.6;
    display: inline-block;
    width: 100%;
  }
  .active {
    opacity: 1;
    color: #F84E35;
  }

  .icon.active {
    opacity: 1;
    filter: invert(57%) sepia(81%) saturate(5663%) hue-rotate(342deg) brightness(101%) contrast(94%);
  }
</style>