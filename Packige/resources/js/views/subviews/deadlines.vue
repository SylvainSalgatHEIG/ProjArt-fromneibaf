<script setup>
import {computed, ref} from 'vue';
import { useFetch } from '../../composables/fetch';

const msg = ref('');

const { data: userGroups } = useFetch("/api/usergroups/");
const { data: deadlinesArray } = useFetch("/api/deadlines/");

const daysShort = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

let groupSelected = ref(1);

function getWeekNumber(date) {
    let currentDate = new Date(date.split(' ')[0]);
    let startDate = new Date(currentDate.getFullYear(), 0, 1);
    let days = Math.floor((currentDate - startDate) / (24 * 60 * 60 * 1000));
         
    let weekNumber = Math.ceil(days / 7);

    return weekNumber;
}

</script>

<template>
    <select name="groups" id="groups" v-model="groupSelected">
        <option v-for="(group, index) in userGroups" :value="group[0].id">{{group[0].promotion.name}}-{{group[0].id}}</option>
    </select>
    
    <h1>Deadline</h1>
    <h2 v-if="deadlinesArray">Semaine {{ getWeekNumber(deadlinesArray[0].start_date) }}</h2>
    <div v-for="deadline in deadlinesArray">
        <div v-if="deadline.group_id == groupSelected" class="deadline" :class="deadline['check'][0].isChecked ? 'checked' : ''">
            <div class="date">
                {{daysShort[new Date(deadline.end_date.split(' ')[0]).getDay()]}}
                {{String(new Date(deadline.end_date.split(' ')[0]).getDate()).padStart(2, '0')}}
            </div>
            <div class="info">
                {{deadline.name}}
            </div>
            <!-- Same date = 1 hour -->
            <div class="time" v-if="deadline.start_date == deadline.end_date">
                {{deadline.end_date.split(' ')[1].split(':')[0] + ':' + deadline.end_date.split(' ')[1].split(':')[1]}}
            </div>

            <!-- Different dates = Range of hours -->
            <div class="time" v-if="deadline.start_date != deadline.end_date">
                {{deadline.start_date.split(' ')[1].split(':')[0] + ':' + deadline.start_date.split(' ')[1].split(':')[1]}}
                {{'à ' + deadline.end_date.split(' ')[1].split(':')[0] + ':' + deadline.end_date.split(' ')[1].split(':')[1]}}
            </div>

            <div class="check">
                <input type="checkbox" id="scales" name="scales" :checked="deadline['check'][0].isChecked">
            </div>

        </div>
    </div>
</template>

<style scoped>
    .deadline {
        display: inline-block;
    }

    .date, .info, .time, .check {
        display: inline;
        margin: auto 5px;
    }

    .checked {
        text-decoration: line-through;
    }


</style>