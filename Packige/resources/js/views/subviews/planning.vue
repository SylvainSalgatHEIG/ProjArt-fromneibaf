<script setup>
import {computed, ref} from 'vue';
import { useFetch } from '../../composables/fetch';
import { useLocalstorage } from '../../composables/localstorage';
import { apiSchedules} from '../../config/apiUrls.js';


const {data: schedules} = useFetch(apiSchedules);
const {value: theSchedule} = useLocalstorage('schedules', schedules.value);

const daysShort = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];


/**
 * Calculate the week number of a date
 * @param {Date} date
 * @returns {integer} week number
 */
function getWeekNumber(date) {
    let currentDate = new Date(date.split(' ')[0]);
    let startDate = new Date(currentDate.getFullYear(), 0, 1);
    let days = Math.floor((currentDate - startDate) / (24 * 60 * 60 * 1000));
    let weekNumber = Math.ceil(days / 7);

    return weekNumber;
}

/**
 * 
 * @param {date} date
 * @return {string}: time in format: HH:MM
 */
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
  let dateStart = new Date(event.end); 
  return dateStart >= new Date();
}

// const tmplBufferedEvent = (buffer, event) => {  
//   let tmpl = TMPL_EVENT.clone();
//   $.each(event, (key, val) => tmpl.find(`.${key}`).text(val));
//   return buffer.append(tmpl);
// }

const makeReadable = event => {
   let dateStart = new Date(event.start);
   let dateEnd = new Date(event.end);
   let hoursStart = dateToHours(dateStart);
   let hoursEnd = dateToHours(dateEnd);
//    let course = event.label.split("-").slice(0, -2).join("-");
   let course = event.label
   let room = event.class.split(",").slice(0, 1).join("");
   return {
     date: dateEnd,
    //  dateFr: dateToFrCh(dateStart),
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

	if (!theSchedule.value) {
    theSchedule.value = schedules.value;
  }
  // filter to get only after today, and sort by date
  let events = theSchedule.value.filter(isInTheFuture).sort(compareEvents);
  
  // filter to get only the courses of the specific group.
  if (groupSelected) {
    events = events.filter(theSchedule => {
      return theSchedule.class === groupSelected.value;
    });
    // return events;
  }
    return events.map(makeReadable);
});


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
    start: formatTwoDigits(start.getUTCDate()) + '.' + formatTwoDigits(start.getUTCMonth() + 1),
    end: formatTwoDigits(end.getUTCDate()) + '.' + formatTwoDigits(end.getUTCMonth() + 1)
  };
}

const schedulesShowable = computed(() => {
  if (!schedulesFiltered.value) return [];
  let allWeeks = [... new Set(schedulesFiltered.value.map(value => value.weekNumber))];
  let myArray = {};

  allWeeks.forEach(week => {
    let weekCourses = schedulesFiltered.value.filter(value => value.weekNumber === week);
    let dateRange = getDateRange(weekCourses.map(value => value.date));

    let daysCourse = daysShort.map(day => {
      let courses = schedulesFiltered.value.filter(course => (course.weekNumber === week && course.day === day));
      return { day,courses }
    });

    myArray[week] = {
      dates: `${dateRange.start}-${dateRange.end} `, 
      daysCourse
    };
  });
  return myArray;
});

const {value: groupSelected} = useLocalstorage('groupSelected', 'IM49-2');

</script>

<template>
	<h1>Horaires</h1>
	<p>{{}}</p>
  <div id="listSchedule">
    <select v-model="groupSelected">
      <option v-for="groupName in groupNames" :value="groupName">{{groupName}}</option>
      <!-- <option v-for="group in groups" :value="group.id">{{group.promotion.name}}-{{group.name}}</option> -->
    </select>
    <ul>
      <li v-for="(schedule, weekNb) of schedulesShowable">
        <h2>{{weekNb}} - {{schedule.dates}}</h2>
          <li v-for="event of schedule.daysCourse">
            <p>{{event.day}}</p>
              <ul>
                <li v-for="course of event.courses">
                  <p>{{course.hours}}</p>
                  <p>{{course.course}}</p>
                  <p>{{course.room}}</p>
                </li>
              </ul>
          </li>
      </li>
    </ul>
  </div>
	<!-- <div id="listSchedule">
		<ul>
			<li v-for="event of schedulesFiltered">
				<span class="date" v-if="currentWeek != event.weekNumber">
          semaine: {{currentWeek = event.weekNumber}}
          </span>
        <span class="day" v-if="currentDay != event.day">
          Jour: {{currentDay = event.day}}
        </span>
				<span class="date">date: {{event.dateFr}}</span>
			{{event.hours}}
				{{event.course} 
				{{event.room}}
			</li>
		</ul>
	</div>
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

