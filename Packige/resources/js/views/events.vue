<script setup>
import { ref, computed } from "vue";
import { useFetch } from "../composables/fetch";
import { useLocalstorage } from "../composables/localstorage";
import { apiEvents } from "../config/apiUrls.js";

const { data: events } = useFetch(apiEvents);
const year = ref(new Date().getFullYear());

const { value: theEvents } = useLocalstorage("events", events.value);
const months = [
  "janvier",
  "février",
  "mars",
  "avril",
  "mai",
  "juin",
  "juillet",
  "août",
  "septembre",
  "octobre",
  "novembre",
  "décembre",
];

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
  if (date < 10) return `0${date}`;
  else return date;
}

const allEvents = computed(() => {
  if (!theEvents.value) {
    theEvents.value = events.value;
    return [];
  }
  theEvents.value.forEach((value, index, array) => {
    value.date = getFormattedDate(
      value.day,
      value.month,
      new Date().getFullYear()
    );
  });
  return theEvents.value;
});
</script>

<template>
  <h1>Actus</h1>
  <div id="events-cards">
    <div v-for="event in allEvents" class="event-card">
      <img class="img" :src="event.imageLink" :alt="event.name" />
      <div class="date">
        <span>{{ event.date }}</span>
      </div>
      <h2 class="title">{{ event.name }}</h2>

      <a class="link" :href="event.link">Lien vers l'événement</a>
    </div>
  </div>
</template>

<style scoped>

html, body {
  max-width: 100%;
  overflow-x: hidden;
  margin: auto;
}

#events-cards {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
}

#events-cards .event-card {
  background: linear-gradient(
    177.03deg,
    rgba(30, 17, 41, 0.6) -119.33%,
    rgba(115, 195, 207, 0.6) 379.63%
  );
  box-shadow: -1px 1px 8px 4px rgba(0, 0, 0, 0.25);

  border-radius: 15px;
  margin-bottom: 12px;
  margin-top: 12px;
  width: 330px;
  height: 240px;

}

#events-cards .event-card .date {
  padding: 3px;
  position: absolute;

  width: 90px;
  height: 30px;
  left: 50px;
	margin-top:20px;

  font-family: "Inter";
  font-weight: bold;
  text-align: center;

  background: #f84e35;
  border-radius: 15px;
}
#events-cards .event-card .img {
  border-radius: 15px;
  width: 300px;
  height: 145px;
  position: absolute;
  left: 0;
  right: 0;
  margin-left: auto;
  margin-right: auto;
  margin-top:15px;
}

#events-cards .event-card a {
  position: relative;
  left: 195px;
  top: 175px;
  width: 130px;
  /* margin-top: 20px;
margin-left: 20px; */
}
#events-cards .event-card .link {
  text-decoration: underline;
  font-size: 12px;
}
#events-cards .event-card .title {
  position: relative;
  top: 175px;
  left: 15px;
}
</style>