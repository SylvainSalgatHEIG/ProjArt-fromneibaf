<script setup>
import {computed, ref, watchEffect} from 'vue';
import { useFetch } from '../../composables/fetch';
import { useLocalstorage } from '../../composables/localstorage';
import { apiSchedules} from '../../config/apiUrls.js';


const {data: schedules} = useFetch(apiSchedules);
// const {value: theSchedule} = useLocalstorage('schedules', schedules.value);
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
  if (!schedules.value) return [];
  // if (!theSchedule.value) {
  //   theSchedule.value = schedules.value;
  // }
  let events = {};
  // filter to get only after today, and sort by date
  if (!showPast.value) {
    events = schedules.value.filter(isInTheFuture);
  }else {
    events = schedules.value;
  }

  events = events.sort(compareEvents);
  
  // filter to get only the courses of the specific group.
  if (groupSelected) {
    events = events.filter(schedules => {
      return schedules.class === groupSelected.value;
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
  // console.log(schedulesFiltered.value);
  let allWeeks = [... new Set(schedulesFiltered.value.map(value => value.weekNumber))];
  let myArray = {};

  allWeeks.forEach(week => {
    let weekCourses = schedulesFiltered.value.filter(value => value.weekNumber === week);
    let dateRange = getDateRange(weekCourses.map(value => value.date));
    let daysCourse = weekDaysShort.map(day => {
      // console.log(day);
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
          // console.log(courses[0].date);
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

// PAUL
console.log(schedulesShowable);

const spacingMarge = 6;

function margeEvent(time) 
 {

  if (typeof time == 'undefined') {
    return 0;
  }

  time = time.split('-')[0];

  const startDay = new Date("November 22 1963 08:30");

  const paramTime = new Date("November 22 1963 " + time);

  let diff = (paramTime.getTime() - startDay.getTime()) / 1000;
  diff /= 60;

  return Math.abs(Math.round(diff)) * 0.88 + 2 * spacingMarge;
  
 }

function courseDurationMarge(startTime, endTime) {

  if (typeof startTime == 'undefined' || typeof endTime == 'undefined') {
    return 0;
  }

  startTime = startTime.split('-')[0];
  endTime = endTime.split('-')[1]

  startTime = new Date("November 22 1963 " + startTime);
  endTime = new Date("November 22 1963 " + endTime);

  let diff = (endTime.getTime() - startTime.getTime()) / 1000;
  diff /= 60;

  return Math.abs(Math.round(diff)) * 0.88 - spacingMarge;
}

function formatDateShort(date) {

  if (date == 'Invalid Date') {
    return 0;
  }

  let day = date.toLocaleDateString("en-US").split("/")[1].padStart(2, "0");
  let month = date.toLocaleDateString("en-US").split("/")[0].padStart(2, "0");
  let year = date.toLocaleDateString("en-US").split("/")[2].substr(-2);

  let dateFormated = day + "." + month + "." + year;

  return dateFormated;
}

function getWeekStartEnd(day) {

  day = new Date(day);

  let first = day.getDate() - day.getDay() + 1;
  let last = first + 4;

  let firstDay = new Date(day.setDate(first));
  let lastDay = new Date(day.setDate(last));

  firstDay = formatDateShort(firstDay);
  lastDay = formatDateShort(lastDay);

  let week = firstDay + " - " + lastDay;

  return week;
}

// PAUL

const {value: groupSelected} = useLocalstorage('groupSelected', 'IM49-2');

const {value: scheduleType} = useLocalstorage('scheduleType', 'calendar');

let selectedWeek = ref(24);

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

      <label v-if="scheduleType == 'list'" for="showPast">
        <input v-if="scheduleType == 'list'" type="checkbox" v-model="showPast" id="showPast">
        Afficher l'historique
        <!-- {{schedulesShowable}} -->
      </label>

      <div v-if="scheduleType == 'list'" v-for="(schedule, weekNb) of schedulesShowable">
        <h2><span>{{schedule.dates}}</span></h2>
          <div v-for="event of schedule.daysCourse" class="planning"  v-show="event.hasCourses">
            <div class="date" v-bind:class="formatDate(new Date(event.courses[0].date)) == formatDate(new Date(todayDate)) ? 'currentDay':''">{{event.day}} {{event.dayTwoDigits}}</div>
            
            <div class="day-info">
                <div v-for="course of event.courses" class="info">
                  <p class="hours">{{course.hours}}</p>
                  <p class="course">{{course.course}} </p>
                  <p class="room">{{course.room}}</p>
                </div>
                <div class="info" v-if="!event.hasCourses">
                  Il n'y a pas de cours aujourd'hui
                </div>
              </div>
          </div>
      </div>
  </div>

  


  <div v-if="scheduleType == 'calendar'" id="weekSelector">
      
      <div id="weekIndication">Semaine {{selectedWeek}}</div>
      <div id="previousButton" v-on:click="selectedWeek -= 1; showPast=true"></div>

      <div v-if="schedulesShowable[selectedWeek]" id="weekRange">{{ getWeekStartEnd(schedulesShowable[selectedWeek].daysCourse[0].courses[0].date) }}</div>
      <div v-else id="weekRange"></div>

      <div id="nextButton" v-on:click="selectedWeek += 1; showPast=true"></div>
    </div>


  <div id="calendar" v-if="scheduleType == 'calendar'">
    


    <div v-if="schedulesShowable[selectedWeek]" id="scheduleView">

      <div id="timeArea">
        <div id="columnTime">
          <div class="rowTime"></div>
          <div class="rowTime">08:30<br/>09:15</div>
          <div class="rowTime">09:15<br/>10:00</div>
          <div class="rowTime">10:00<br/>10:45</div>
          <div class="rowTime">10:45<br/>11:30</div>
          <div class="rowTime">11:30<br/>12:15</div>
          <div class="rowTime">12:15<br/>13:00</div>
          <div class="rowTime">13:00<br/>13:45</div>
          <div class="rowTime">13:45<br/>14:30</div>
          <div class="rowTime">14:30<br/>15:15</div>
          <div class="rowTime">15:15<br/>16:00</div>
          <div class="rowTime">16:00<br/>16:45</div>
        </div>

      </div>

      <div v-for="(course) in schedulesShowable[selectedWeek].daysCourse" class="column row columnDate rowDate" id="rowDates" v-bind:class="formatDate(new Date(course.courses[0].date)) == formatDate(new Date(todayDate)) ? 'currentDay':''">{{ course.day }}<br />{{ course.dayTwoDigits }}</div>

      <div id="planningView">
        <div v-for="(course) in schedulesShowable[selectedWeek].daysCourse" class="column row" id="rowDates">
          <div :style="{ 'margin-top': margeEvent(courseOfDay.hours) + 'px', 'height': courseDurationMarge(courseOfDay.hours, courseOfDay.hours) + 'px'}" v-for="(courseOfDay, index) in course.courses" class="courseCalendar">
            <p class="courseNameVertical">{{courseOfDay.course}} <b>{{courseOfDay.room}}</b></p>
          </div>
        </div>
      </div>

    </div>

    <div v-else class="noCourseMessage">Aucun cours</div>
  </div>

</template>

<style scoped>

.noCourseMessage {
  margin-top: 20px;

  width: 100%;
  height: 100%;

  text-align: center;
}

#weekRange {
  display: inline-block;

  height: 30px;
  min-width: 127px;

  vertical-align: middle;

  padding-top: 5px;
}

#weekSelector {
  display: block;

  margin-left: 14px;

}

#weekIndication {
  width: 190px;
  text-align: center;
  font-weight: 600;
font-size: 16px;
}

#previousButton {
  height: 44px;
  width: 44px;

  background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M18.75 22.5L11.25 15L18.75 7.5' stroke='%2377B0C5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
  background-repeat: no-repeat;
  background-position: center;

  display: inline-block;

  margin-right: 2px;

  vertical-align: middle;
}

#nextButton {
  height: 44px;
  width: 44px;

  background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11.25 22.5L18.75 15L11.25 7.5' stroke='%2377B0C5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
  background-repeat: no-repeat;
  background-position: center;

  display: inline-block;

  margin-left: 2px;

  vertical-align: middle;
}

.rowTime {

  display: inline-block;

  position: relative;

  width: 330px;
  height: 40px;


  margin: 0;

  font-size: 12px;
  text-align: left;
  padding-left: 6px;

  border-color: rgb(256, 256, 256, 0.6);
  border-style: solid;
  border-width: 0 0 1px 0;

  color: rgb(256, 256, 256, 0.6);

}

.rowTime:before {
   content: '';
   display: block;
   position: absolute;
   background-color: #ffffff;
   border-radius: 666px;
   width: 3px;
   height: 3px;
   box-shadow: 0 0px 0 0 #fff
}
.rowTime:before {
   left: -1px;
   top: -2.5px;
}


.rowTime:last-of-type {
  border: none;
}

.rowTime:first-of-type {
  margin-bottom: -7px;
}

.rowTime:first-of-type:before, .orange:after {
   content: '';
   display: none;
   position: absolute;
   background-color: #ffffff;
   border-radius: 666px;
   width: 3px;
   height: 3px;
   box-shadow: 0 0px 0 0 #fff
}

#timeArea {

  position: absolute;

  width: 330px;
  height: 480px;

  right: 10px;

}

#columnTime {

  position: absolute;

  width: 50px;
  height: 480px;

  right: 280px;
}

#calendar {
  margin: 0 16% 0 16%;

  width: 300px;
  padding: 10px;
  position: relative;
}

@media (min-width: 415px) {
  #calendar {
    margin: 0 auto;
  }
}

#scheduleView {

  width: 280px;
  height: 480px;
}


.courseNameVertical {

  position: absolute;
    top: 50%;
    left: 50%;
  
  
  transform: translateX(-50%) translateY(-50%) rotate(-90deg);

  white-space: nowrap;
  word-spacing: 10px;

  color: #0C223F;
}

.courseCalendar {
  position: absolute;

  width: 40px;
  height: 120px;

  margin: auto 8px;
  
  background-color: #F2F7FF;
  

  border-radius: 3px;
}

.column {
  display:inline-flex;
  width: 56px;
  margin: 0;
  text-align: center;
}

.row {
  height: 40px;
}

.columnDate {
  text-align: center;
  line-height: 16px;
  padding: 2px;


  margin: auto 13px;
  width: 30px;
  height: 33px;

  /* background-color: #F84E35; */

  border-radius: 5px;
}

.columnDate:nth-of-type(5) {
  padding-left: 5px;
}

.rowDate {
  text-align: center;
}




.currentDay {
  background-color: #F84E35 !important;
}
.content {
    margin: auto;
  }

  .day-info .info {
    display: flex;
    padding: 13px 0 0 0;
    justify-content: space-between;
    /* column-gap: 40px; */
  }

  .planning:not(:last-child) .info:last-child {
    padding-bottom: 20px;
    border-bottom: 1px solid #345771;
  }

  .day-info .info p {
    display: block;
  }

  .day-info .info p.hours {
    min-width: 115px;
  }

  .day-info .info p.course {
    font-weight: bold;
    min-width: 115px;

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

        width: 90px;
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

        position: absolute;
top: 250px;
right: 25px;

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

    h2 span {
        display: inline-block;
        width: 105px;
    }

    h2:after {
        content: "";
        display: inline-block;
        height: 0.7em;
        vertical-align: bottom;
        width: 194px;
        margin-right: -100%;
        margin-left: 13px;
        border-top: 2px solid white;
        opacity: 0.6;
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

    .info, .time {
        display: inline;
        margin: auto 0 0 15px;
    }
</style>

