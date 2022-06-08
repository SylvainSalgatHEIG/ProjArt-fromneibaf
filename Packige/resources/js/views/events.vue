<script setup>
import {ref, computed } from 'vue';
import { useFetch } from '../composables/fetch';
import { useLocalstorage } from '../composables/localstorage';
import { apiEvents} from '../config/apiUrls.js';

const {data: events} = useFetch(apiEvents);
const year = ref(new Date().getFullYear());

const {value: theEvents} = useLocalstorage('events', events.value);
const months = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];

function getFormattedDate(day, month, year) {
	return day + "." + formatTwoDigits(months.indexOf(month)) + "." + year;
}

/**
 * Function that format the digit if it is less than 10
 * Example: 1 => 01 --> 01.01.2020 instead of 1.01.2020
 * @param {date} Date
 * @return {date}
 */
function formatTwoDigits(date) {
  if (date < 10)
    return `0${date}`;
  else
    return date;
}


const allEvents = computed(() => {
	if (!theEvents.value) {
		theEvents.value = events.value;
	}
	theEvents.value.forEach((value, index, array) => {
		value.date = getFormattedDate(value.day, value.month, new Date().getFullYear());
	})
  return theEvents.value;
});
</script>

<template>
	<div id="events-cards">
		<div v-for="event in allEvents" class="event-card">
			<img :src="event.imageLink" :alt="event.name">
			<h1 class="title">{{event.name}}</h1>
			<div class="date">
				<span>{{event.date}}</span>
			</div>
			<a :href="event.link">{{event.name}}</a>
		</div>
	</div>
</template>

<style scoped>
	#events-cards {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-around;
	}

	#events-cards .event-card {
		background: linear-gradient(177.03deg, rgba(30, 17, 41, 0.6) -119.33%, rgba(115, 195, 207, 0.6) 379.63%);
		box-shadow: -1px 1px 8px 4px rgba(0, 0, 0, 0.25);
		border-radius: 15px;
		/* width: 30%;
		margin: 1%;
		padding: 1%;
		background-color: #fff;
		border-radius: 5px;
		box-shadow: 0px 0px 5px #000; */
	}

	#events-cards .event-card .date {
		padding: 10px;
		position: relative;
		top: 0;
		left: 10px;

		background: #F84E35;
		border-radius: 15px;
	}
</style>