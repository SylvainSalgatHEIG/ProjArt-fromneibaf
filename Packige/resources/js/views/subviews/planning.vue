<script setup>
import {computed, ref} from 'vue';
import { useFetch } from '../../composables/fetch';


const msg = ref('');

const {data: schedules} = useFetch('https://chabloz.eu/files/horaires/all.json');

const {data: groups} = useFetch('/api/groups/');

const daysShort = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];

function getWeekNumber(date) {
    let currentDate = new Date(date.split(' ')[0]);
    let startDate = new Date(currentDate.getFullYear(), 0, 1);
    let days = Math.floor((currentDate - startDate) / (24 * 60 * 60 * 1000));
         
    let weekNumber = Math.ceil(days / 7);

    return weekNumber;
}


let groupSelected = ref('IM49-2');

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
     date: dateEnd,
     dateFr: dateToFrCh(dateStart),
     hours: `${hoursStart}-${hoursEnd}`,
     weekNumber: getWeekNumber(event.end),
     day: daysShort[dateEnd.getDay()],
     course,
     room,
     
   }
}

// function groupToGroupName(group) {
//   let groupPromotion = group.promotion.name;
//   let groupName = group.name > 0 ? `-${group.name}` : '';
//   return `${groupPromotion}${groupName}`;
// } 

const groupNames = computed(() => {
  if (!schedules.value) return [];
  return [...new Set(schedules.value.map(schedule => schedule.class))];
  // return schedules.value.map(schedule => schedule.class);
});

// const schedulesFutur
const schedulesFiltered = computed(() => {
	// console.log(schedules.value)
	if (!schedules.value) return  []
  // let events = schedules.value.filter(isInTheFuture).sort(compareEvents);
  let events = schedules.value.sort(compareEvents);
  if (groupNames) {
    events = events.filter(schedule => {
      return schedule.class === groupSelected.value;
    });
    // return events;
  }
    return events.map(makeReadable);
});

function formatDayZero(date) {
  if (date.getUTCDate() < 10)
    return `0${date.getUTCDate()}`;
  else
    return date.getUTCDate();
}

function formatMonthZero(date) {
  if (date.getUTCMonth() + 1 < 10)
    return `0${date.getUTCMonth() + 1}`;
  else
    return date.getUTCMonth() + 1 ;
}

function getDateRange(dates) {
  let end = new Date(dates[dates.length - 1]);
  if (end.getDay() != 5) {
    end.setDate(end.getDate() + (5 - end.getDay()));
  }
  let start = new Date(dates[dates.length - 1]);
  start.setDate(start.getDate() - 5);
  // start = start.getUTCDate === 0 ? start : new Date(start.getTime() + 24 * 60 * 60 * 1000);
  // end = end.getUTCDate === 6 ? end : new Date(end.getTime() + 24 * 60 * 60 * 1000);
  return {
    start: formatDayZero(start) + '.' + formatMonthZero(start),
    end: formatDayZero(end) + '.' + formatMonthZero(end)
  };
}

const schedulesShowable = computed(() => {
  if (!schedulesFiltered.value) return [];
  let crtWeek = ref(0);
  let crtDay = ref('');
  let allWeeks = [... new Set(schedulesFiltered.value.map(value => value.weekNumber))];
  // return allWeeks;
  let myArray = {};
  allWeeks.forEach((week, index, array) => {
    let weekEvents = schedulesFiltered.value.filter(value => value.weekNumber === week);
    let dateRange = getDateRange(weekEvents.map(value => value.date));
    let date = [... new Set(weekEvents.map(value => value.date))];
    let daysEvent = daysShort.map((day, index, array) => {
      let events = schedulesFiltered.value.filter(event => {
        return event.weekNumber === week && event.day === day;
      });
      return {
        day,
        events
      }
      
    });
    myArray[week] = {
      dates: `${dateRange.start}-${dateRange.end} `, 
      daysEvent
      };
  });
  return myArray;
  // let newArray = schedulesFiltered.value.map((value, index, array) => {
  //   let newValue = {};
  //   if (crtWeek != value.weekNumber) {
  //     crtWeek = value.weekNumber;
  //     newValue = weeks;
  //   }
  //   weeks[`${crtWeek}`] = value;
  //   return newValue;
  // });
  // return newArray;
});

const currentWeek = ref(0);
const currentDay = ref('')



</script>

<template>
	<h1>Horaires</h1>
	<p>{{}}</p>
  <ul>
    <li v-for="(schedule, weekNb) of schedulesShowable">
      <h2>{{weekNb}} - {{schedule.dates}}</h2>
        <li v-for="event of schedule.daysEvent">
          <p>{{event.day}}</p>
            <ul>
              <li v-for="course of event.events">
                <p>{{course.hours}}</p>
                <p>{{course.course}}</p>
                <p>{{course.room}}</p>
              </li>
            </ul>
        </li>
    </li>
  </ul>
	<div id="listSchedule">
		<select v-model="groupSelected">
			<option v-for="groupName in groupNames" :value="groupName">{{groupName}}</option>
			<!-- <option v-for="group in groups" :value="group.id">{{group.promotion.name}}-{{group.name}}</option> -->
		</select>
		<ul>
			<li v-for="event of schedulesFiltered">
				<span class="date" v-if="currentWeek != event.weekNumber">
          semaine: {{currentWeek = event.weekNumber}}
          </span> --
        <span class="day" v-if="currentDay != event.day">
          Jour: {{currentDay = event.day}}
        </span> -- 
				<!-- <span class="date">date: {{event.dateFr}}</span> -->
				-- {{event.hours}} -- 
				{{event.course}} -- 
				{{event.room}} -- 
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

