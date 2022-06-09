<script setup>
import { computed, ref, watchEffect } from "vue";
import { useFetch, usePost } from "../../composables/fetch";
import { deadlines } from "../../stores/deadlines.js";

import DeadlineModal from "../../components/DeadlineModal.vue";

const msg = ref("");

const { data: userGroups } = useFetch("/api/usergroups/");

const daysShort = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];

let groupSelected = ref(1);

let currentWeek = ref("");
let currentDay = ref("");

const todayDate = new Date(Date.now()).toISOString().split("T")[0];

const dealinesArray = computed(() => {

    if (!deadlines.value) return [];

    let array = deadlines.value;
    let currentWeek = getWeekNumber(array[0].start_date);
    let weekRange = getWeekStartEnd(array[0].start_date);

    let deadlineArray = [];
    

    let deadlineElement = {
        'week': currentWeek,
        'weekRange': weekRange,
        'deadlines': []
    }

    array.forEach(deadline => {

        if (currentWeek == getWeekNumber(deadline.start_date)) {
            deadlineElement.deadlines.push(deadline);
        } else {
            deadlineArray.push(deadlineElement);

            currentWeek = getWeekNumber(deadline.start_date);
            weekRange = getWeekStartEnd(deadline.start_date);

            deadlineElement = {
                'week': currentWeek,
                'weekRange': weekRange,
                'deadlines': []
            }

            deadlineElement.deadlines.push(deadline);
        }
        
    });

    deadlineArray.push(deadlineElement);


    return deadlineArray;
})

function getWeekNumber(date) {
  let currentDate = new Date(date.split(" ")[0]);
  let startDate = new Date(currentDate.getFullYear(), 0, 1);
  let days = Math.floor((currentDate - startDate) / (24 * 60 * 60 * 1000));

  let weekNumber = Math.ceil(days / 7);

  return weekNumber;
}

function checkEvent(event, deadlineId) {
  console.log();

  //let deadlineId = event.target.value;
  if (!event.target.classList.contains("checked")) {
    // Check
    useFetch("/api/deadline/check/" + deadlineId + "/check");
    event.target.classList.add("checked");
    event.target.parentNode.parentNode.classList.add("checked");
  } else {
    // Uncheck
    useFetch("/api/deadline/check/" + deadlineId + "/uncheck");
    event.target.classList.remove("checked");
    event.target.parentNode.parentNode.classList.remove("checked");
  }
}

function formatDateShort(date) {
  let day = date.toLocaleDateString("en-US").split("/")[1].padStart(2, "0");
  let month = date.toLocaleDateString("en-US").split("/")[0].padStart(2, "0");

  let dateFormated = day + "." + month;

  return dateFormated;
}

function getWeekStartEnd(day) {
  day = Date.parse(day.split(" ")[0]);

  day = new Date(day);

  let first = day.getDate() - day.getDay() + 1;
  let last = first + 6;

  let firstDay = new Date(day.setDate(first));
  let lastDay = new Date(day.setDate(last));

  firstDay = formatDateShort(firstDay);
  lastDay = formatDateShort(lastDay);

  let week = firstDay + " - " + lastDay;

  return week;
}

let deadlineId = ref(null);
let showModal = ref(false);

function editDeadline(note) {
  //   deadlineId.value = note.id;
  showModal.value = true;
}

function addDeadline() {
  deadlineId.value = null;
  showModal.value = true;
}
</script>

<template>
  <deadline-modal
    v-show="showModal"
    @close="showModal = false"
    :deadlineId="deadlineId"
    :groupId="groupSelected"
  />
  <button @click="addDeadline()">Ajouter une deadline</button>
  
  <div class="inputRow">
    <select name="groups" id="groups" v-model="groupSelected">
      <option v-for="(group, index) in userGroups" :value="group[0].id">
        {{ group[0].promotion.name }}-{{ group[0].id }}
      </option>
    </select>
  </div>

    <div v-for="(week) of dealinesArray">
        <h2 v-if="week.deadlines[0].group_id == groupSelected">{{week.weekRange}}</h2>

        <div v-for="(deadline, index) of week.deadlines">
            <div v-if="deadline.group_id == groupSelected" class="deadline">
                
                <div v-if="deadline.end_date.split(' ')[0] != week.deadlines[(index+week.deadlines.length-1)%week.deadlines.length].end_date.split(' ')[0] || week.deadlines.length == 1" class="date">
                    {{daysShort[new Date(deadline.end_date.split(' ')[0]).getDay()-1]}}
                     {{String(new Date(deadline.end_date.split(' ')[0]).getDate()).padStart(2, '0')}}
                </div>

                <div v-else class="date hidden"></div>

                <div class="info" v-bind:class = "(deadline.type == 'rendu')?'rendu':'examen'" :class="deadline['check'][0].isChecked ? 'checked' : ''">
                    <div class="name"> {{deadline.name}} </div>

                    <!-- Same date = 1 hour -->
                    <div class="time" v-if="deadline.start_date == deadline.end_date">
                        {{deadline.end_date.split(' ')[1].split(':')[0] + ':' + deadline.end_date.split(' ')[1].split(':')[1]}}
                    </div>

                    <!-- Different dates = Range of hours -->
                    <div class="time" v-if="deadline.start_date != deadline.end_date">
                        {{deadline.start_date.split(' ')[1].split(':')[0] + ':' + deadline.start_date.split(' ')[1].split(':')[1]}}
                        {{'à ' + deadline.end_date.split(' ')[1].split(':')[0] + ':' + deadline.end_date.split(' ')[1].split(':')[1]}}
                    </div>
                    
                    <div class="check" v-show="deadline.type == 'rendu'">
                        <div @click="checkEvent($event, deadline.id)" :value="deadline.id" class="checkbox" v-bind:class = "(deadline['check'][0].isChecked)?'checked':''"></div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</template>

<style scoped>
.inputRow {
  display: flex;
  align-items: flex-end;
  width: 312px;
  margin: auto;
}

.deadline .name {
  display: inline-block;
  width: 90px;
}

.inputRow {
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
  background-position: calc(100% + 0.1rem);
  background-repeat: no-repeat;

  background-color: transparent;
  border: none;
}

#groups option {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-color: #0b2240;
  color: #ffffff;
}

h2 {
  font-family: "Inter";
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

.deadline .date {
  width: 30px;
  height: 35px;

  text-align: center;
  font-family: "Inter";
  font-style: normal;
  font-weight: 400;
  font-size: 16px;
  line-height: 16px;

  line-height: 1rem;
  padding: px 4px;
  margin-left: 0 !important;
  margin-bottom: 5px;
  margin-top: 5px;
}

.deadline .date.currentDay {
  background-color: #f84e35;
  border-radius: 5px;
}

.deadline .date::first-line {
  color: rgba(255, 255, 255, 0.6);
  font-size: 11px;
}

.deadline .info {
  width: 272px !important;
  height: 48px;
  padding: 13px 0 13px 10px;

  background-color: #77b0c5;
  border-radius: 5px;
}

.deadline .info.rendu {
  border-left: 9px solid #6f83aa;
}

.deadline .info.examen {
  border-left: 9px solid #f84e35;
}

.deadline .check {
  visibility: visible;

  width: 36px;
  height: 36px;

  position: relative;

  float: right;
}

.checkbox {
  width: 36px;
  height: 36px;

  top: -18%;
  right: 20%;

  position: relative;

  float: right;

  border-radius: 7px;
  background: rgba(255, 255, 255, 0.5);
}

.checkbox.checked {
  background-image: url("data:image/svg+xml,%3Csvg width='21' height='22' viewBox='0 0 21 22' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M17.143 5.4231L7.71442 15.3654L3.42871 10.8462' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
  background-repeat: no-repeat;
  background-position: center;
  background-size: 21px;
}

.deadline {
  display: flex;
  align-items: center;
  height: 48px;
  width: 312px;
  margin: 8px 0;
}

.date,
.info,
.time {
  display: inline;
  margin: auto 0 0 15px;
}

.check {
  display: inline;
}

.deadline .info.checked {
  text-decoration: line-through;
}

.hidden {
  visibility: hidden;
}

@media (min-width: 480px) {
  .content {
    position: absolute;
    left: 0;
    right: 0;
    margin-left: auto;
    margin-right: auto;
    width: 312px;
  }
}

.checked {
  text-decoration: line-through;
}
</style>