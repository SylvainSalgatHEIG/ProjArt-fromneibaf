<script setup>
import {computed, ref} from 'vue';
import { useFetch } from '../../composables/fetch';


const msg = ref('');

const {data: schedules} = useFetch('https://chabloz.eu/files/horaires/all.json');

const {data: groups} = useFetch('/api/groups/');

function strToDate (str){
	return new Date(str);
	// console.log(str)
	// const date =  new Date(Date.UTC(
	// 	str.substr(0, 4),
	// 	str.substr(5, 2) - 1,
	// 	str.substr(7, 2),
	// 	str.substr(9, 2),
	// 	str.substr(11, 2),
	// 	str.substr(13, 2)
	// ));
	// console.log(new Date(str));
	// return date;
} 

const dateToFrCh = date => {
  let mapDay = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
  let day = date.getDate();
  let dayName = mapDay[date.getDay()];
  let month = date.getMonth() + 1;  
  let year = date.getFullYear();
  if (month < 10) month = '0' + month;
  if (day < 10) day = '0' + day;
  return `${dayName} ${day}.${month}`;
}

const dateToHours = date => {
  let hours = date.getHours();
  let minutes = date.getMinutes();
  if (hours < 10) hours = '0' + hours;
  if (minutes < 10) minutes = '0' + minutes;    
  return `${hours}:${minutes}`;
}

const compareEvents = (event1, event2) => {
  let date1 = event1.start;
  let date2 = event2.start;
  return date1.localeCompare(date2);
}
function isInTheFuture(event) { 
  let dateStart = strToDate(event.end); 
  return dateStart >= new Date();
}

// const tmplBufferedEvent = (buffer, event) => {  
//   let tmpl = TMPL_EVENT.clone();
//   $.each(event, (key, val) => tmpl.find(`.${key}`).text(val));
//   return buffer.append(tmpl);
// }

const makeReadable = event => {
   let dateStart = strToDate(event.start);
   let dateEnd = strToDate(event.end);
   let hoursStart = dateToHours(dateStart);
   let hoursEnd = dateToHours(dateEnd);
//    let course = event.label.split("-").slice(0, -2).join("-");
   let course = event.label
   let room = event.class.split(",").slice(0, 1).join("");
   return {
     date: dateToFrCh(dateStart),
     hours: `${hoursStart}-${hoursEnd}`,
     course,
     room
   }
}

// const schedulesFutur
const schedulesFiltered = computed(() => {
	// console.log(schedules.value)
	if (!schedules.value) return  []
	let events = schedules.value.filter(isInTheFuture).sort(compareEvents);
	// return schedules.value.filter(isInTheFuture).sort(compareEvents);
	// console.log(events);
	return events.map(makeReadable);
});

// console.log(groups.value);

</script>

<template>
	<h1>Horaires</h1>
	<p ></p>
	<div id="listSchedule">
		<select>
			<option value="">Choisir un groupe</option>
			<option v-for="group in groups" :value="group.id">{{group.promotion.name}}-{{group.name}}</option>
		</select>
		<ul>
			<li v-for="event of schedulesFiltered">
				<span class="date">{{event.date}}</span>
				{{event.hours}}
				{{event.course}}
				{{event.room}}
			</li>
		</ul>
	</div>
	<!-- <table id="schedule">
		<thead>
          <tr class="template template-course">
            <th class="date">Date</th>
            <th class="hours">Heure</th>
            <th class="course">Cours</th>
            <th class="room">Classe</th>
          </tr>
        </thead>
        <tbody>
          <tr class="template template-course" v-for="course of schedules">
            <td class="date">{{course.start}}</td>
            <td class="hours">{{course.hours}}</td>
            <td class="course">{{course.course}}</td>
            <td class="room">{{course.room}}</td>
          </tr>
        </tbody>
      </table> -->
</template>

<style scoped>
#schedule {
  border-collapse: collapse;
  width: 60%;
  margin: auto;
  border: 1px solid black;
}
</style>

