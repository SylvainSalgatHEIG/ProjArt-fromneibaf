<script setup>
import {ref, computed } from 'vue';
import { useFetch } from '../composables/fetch';
import { useLocalstorage } from '../composables/localstorage';
import { apiEvents} from '../config/apiUrls.js';

const {data: events} = useFetch(apiEvents);
const year = ref(new Date().getFullYear());
console.log(events);
</script>

<template>
	<h1>Hello {{}}</h1>
	<ul>
		<li v-for="event in events">
			<img :src="event.imageLink" :alt="event.name">
			{{event.day}} {{event.month}} {{year}}<a :href="event.link">{{event.name}}</a>
			
		</li>
	</ul>
</template>