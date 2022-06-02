<script setup>
import {ref} from 'vue';
import { useFetch } from '../composables/fetch';


const msg = ref('');

const {data: schedule} = useFetch('https://chabloz.eu/files/horaires/all.json');

</script>

<template>
	<h1>Page Schedule</h1>
	<p v-if="msg">{{schedule}}</p>
	<table id="schedule">
		<thead>
          <tr class="template template-course">
            <th class="date">Date</th>
            <th class="hours">Heure</th>
            <th class="course">Cours</th>
            <th class="room">Classe</th>
          </tr>
        </thead>
        <tbody>
          <tr class="template template-course" v-for="course of scheduleFiltered">
            <td class="date">{{course.start}}-{{course.end}}</td>
            <td class="hours"></td>
            <td class="course">{{course.label}}</td>
            <td class="room">{{course.room}}</td>
          </tr>
        </tbody>
      </table>
</template>

<style scoped>
#schedule {
  border-collapse: collapse;
  width: 60%;
  margin: auto;
  border: 1px solid black;
}
</style>

