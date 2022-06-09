<script setup>
import {computed, ref, watchEffect} from 'vue';
import { useFetch } from '../../composables/fetch';
import { useLocalstorage } from '../../composables/localstorage';
import { apiSchedules} from '../../config/apiUrls.js';


const {data: schedules} = useFetch(apiSchedules);
const {value: theSchedule} = useLocalstorage('schedules', schedules.value);
const todayDate = ref(new Date(Date.now()));

const showPast = ref(false);
const daysShort = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
const weekDaysShort = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven'];


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
 * Format the date to dd.mm.yyyy
 * @param {Date} date
 * @returns {string} formatted date
 */
function formatDate(date) {
    let day = date.getDate();
    let month = date.getMonth() + 1;
    let year = date.getFullYear();

    return `${day}.${month}.${year}`;
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
   let room = event.room.split(",").slice(0, 1).join("");
   return {
     date: dateEnd,
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
  let events = {};
  // filter to get only after today, and sort by date
  if (!showPast.value) {
    events = theSchedule.value.filter(isInTheFuture);
  }else {
    events = theSchedule.value;
  }

  events = events.sort(compareEvents);
  
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
  start.setDate(start.getDate() - 4);
  // start = start.getUTCDate === 0 ? start : new Date(start.getTime() + 24 * 60 * 60 * 1000);
  // end = end.getUTCDate === 6 ? end : new Date(end.getTime() + 24 * 60 * 60 * 1000);
  return {
    start: formatTwoDigits(start.getUTCDate()) + '.' + formatTwoDigits(start.getUTCMonth() + 1),
    end: formatTwoDigits(end.getUTCDate()) + '.' + formatTwoDigits(end.getUTCMonth() + 1)
  };
}

const schedulesShowable = computed(() => {
  if (!schedulesFiltered.value) return [];
  console.log(schedulesFiltered.value);
  let allWeeks = [... new Set(schedulesFiltered.value.map(value => value.weekNumber))];
  let myArray = {};

  allWeeks.forEach(week => {
    let weekCourses = schedulesFiltered.value.filter(value => value.weekNumber === week);
    let dateRange = getDateRange(weekCourses.map(value => value.date));
    let daysCourse = weekDaysShort.map(day => {
      console.log(day);
      let hasCourses = true;
      // let dayNb = daysShort.indexOf(day) -1;
      // if (dayNb == -1) dayNb = 6;
      let dayTwoDigits = '';
      let courses = schedulesFiltered.value.filter(course => {
        
        // if (typeof(weekCourses[dayNb]) !== 'undefined') {
        //   dayTwoDigits = formatTwoDigits(new Date(weekCourses[dayNb].date).getUTCDate());
        // }
        // course.dateTwoDigits = formatTwoDigits(course.),
        return course.weekNumber === week && course.day === day
        });
        if (courses.length == 0){
          courses = "Il n'y a pas de cours aujourd'hui";
          hasCourses = false;
        }else {
          // console.log(formatTwoDigits(new Date(courses[0].date).getUTCDate()))
          console.log(courses[0].date);
          dayTwoDigits = formatTwoDigits(new Date(courses[0].date).getUTCDate())
        }
      return { day,courses, hasCourses, dayTwoDigits: dayTwoDigits}
    });

    myArray[week] = {
      dates: `${dateRange.start}-${dateRange.end} `,
      daysCourse
    };
  });

// const schedulesShowable = computed(() => {
//   if (!schedulesFiltered.value) return [];
//   console.log(schedulesFiltered.value);
//   let allWeeks = [... new Set(schedulesFiltered.value.map(value => value.weekNumber))];
//   let myArray = {};

//   allWeeks.forEach(week => {
//     let weekCourses = schedulesFiltered.value.filter(value => value.weekNumber === week);
//     let dateRange = getDateRange(weekCourses.map(value => value.date));
//     let daysCourse = daysShort.map(day => {
//       let dayNb = daysShort.indexOf(day) -1;
//       if (dayNb == -1) dayNb = 6;
//       let dayTwoDigits = 'test';
//       let courses = schedulesFiltered.value.filter(course => {
        
//         if (typeof(weekCourses[dayNb]) !== 'undefined') {
//           dayTwoDigits = formatTwoDigits(new Date(weekCourses[dayNb].date).getUTCDate());
//         }
//         // course.dateTwoDigits = formatTwoDigits(course.),
//         return course.weekNumber === week && course.day === day
//         });
//       return { day,courses, dayTwoDigits: dayTwoDigits}
//     });

//     myArray[week] = {
//       dates: `${dateRange.start}-${dateRange.end} `,
//       daysCourse
//     };
//   });
  return myArray;
});

const {value: groupSelected} = useLocalstorage('groupSelected', 'IM49-2');

</script>

<template>
  <div  class="content">
    <h1>Horaires</h1>
    <!-- <pre>{{schedulesShowable}}</pre> -->
      <div class="inputRow">
      <!-- <div id="listSchedule"> -->
        <select v-model="groupSelected" id="groups">
          <option v-for="groupName in groupNames" :value="groupName">{{groupName}}</option>
          <!-- <option v-for="group in groups" :value="group.id">{{group.promotion.name}}-{{group.name}}</option> -->
        </select>
      </div>

      <label for="showPast">
        <input type="checkbox" v-model="showPast" id="showPast">
        Afficher l'historique
        <!-- {{schedulesShowable}} -->
      </label>

        <div v-for="(schedule, weekNb) of schedulesShowable">
          <h2>{{schedule.dates}}</h2>
            <div v-for="event of schedule.daysCourse" class="planning"  v-show="event.hasCourses">
              <div class="date" v-bind:class="formatDate(new Date(event.courses[0].date)) == formatDate(new Date(todayDate)) ? 'currentDay':''">{{event.day}} {{event.dayTwoDigits}}</div>
              
              <div class="day-info">
                  <div v-for="course of event.courses" class="info">
                    <p class="hours">{{course.hours}}</p>
                    <p class="course">{{course.course}} </p>
                    <p class="room">{{course.room}}</p>
                  </div>
                  <div class="info" v-show="!event.hasCourses">
                    Il n'y a pas de cours aujourd'hui
                  </div>
                </div>
            </div>
        </div>
      <!-- </div> -->
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
        </div>
</template>

<style scoped>
.currentDay {
  background-color: #F84E35 !important;
}
.content {
    margin-left: auto;
    margin-right: auto;
    width: 312px;
  }

  .day-info .info {
    display: flex;
    padding: 13px 0 0 0;
    justify-content: space-between;
    /* column-gap: 40px; */
  }

  .day-info .info p {
    display: block;
  }

  .day-info .info p.hours {
    min-width: 110px;
  }

  .day-info .info p.course {
    font-weight: bold;
    min-width: 110px;

  }

  .day-info .info p.room {
    text-align: right;
  }
/* #schedule {
  border-collapse: collapse;
  width: 60%;
  margin: auto;
  border: 1px solid black;
} */ .inputRow {
        display: flex;
        align-items: flex-end;
        width: 312px;
        margin: auto;
    }

    #groups:focus {
        box-shadow: none;
        outline: none;
    }

    #groups {

        -webkit-tap-highlight-color: transparent;
        
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;

        width: 78px;
        height: 26px;

        background-image: url("data:image/svg+xml,%3Csvg width='24' height='26' viewBox='0 0 24 26' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 9.75L12 16.25L18 9.75' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
        background-size: 25px;
        background-position: calc(100% + .1rem);
        background-repeat: no-repeat;

        background-color: transparent;
        border: none;

        color: #FFFFFF;
        font-family: 'Inter';
        font-style: normal;
        font-weight: 600;
        font-size: 16px;
        line-height: 22px;

        margin-top: 20px;
        margin-bottom: 20px;
        
        margin-left: auto;

    }

    #groups option {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-color: #0B2240;
        color: #FFFFFF;
    }

    h2 {
        font-family: 'Inter';
        font-style: normal;
        font-weight: 600;
        font-size: 16px;
        line-height: 22px;
    }

    h2:after {
        content: "";
        display: inline-block;
        height: 0.7em;
        vertical-align: bottom;
        width: 196px;
        margin-right: -100%;
        margin-left: 10px;
        border-top: 1px solid white;
    }  
    
    h2:not(:first-of-type) {
        margin-top: 30px !important;
    }

    .planning .date {
        width: 30px;
        height: 35px;

        text-align: center;
        font-family: 'Inter';
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 16px;

        /* background-color: #F84E35; */
        border-radius: 5px;

        line-height: 1rem;
        /* padding: 4px;
        margin-left: 0 !important;
        margin-bottom: 5px;
        margin-top: 5px; */
        margin-top: 12px;
        margin-bottom: auto;
    }

    .planning .date::first-line {
        color: rgba(255, 255, 255, 0.6);
        font-size: 11px;
    }

    /* .planning .info { */
        /* width: 272px !important; */
        /* height: 48px; */
/* 
        background-color: #77b0c5;
        border-radius: 5px; */

    /* } */

    .planning {
        display: flex;
        align-items: center;
        /* height: 48px; */
        width: 312px;
        margin: 8px 0;
    }

    .date, .info, .time {
        display: inline;
        margin: auto 0 0 15px;
    }
</style>

