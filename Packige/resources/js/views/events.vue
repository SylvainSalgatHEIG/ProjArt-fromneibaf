<script setup>
import { ref, computed } from "vue";
import { useFetch } from "../composables/fetch";
import { apiEvents } from "../config/apiUrls.js";
import { STORAGE_URL } from "../config/apiUrls.js";

const year = ref(new Date().getFullYear());

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

// const { data: events } = useFetch(apiEvents);
const threeDaysLater = new Date();
threeDaysLater.setDate(threeDaysLater.getDate() + 3);

const oneMonthAndHalfLater = new Date();
oneMonthAndHalfLater.setDate(oneMonthAndHalfLater.getDate() + 45);
const events = [
  {
    // get day of the month 3 days later than today 
    day : threeDaysLater.getDate(),
    // get month number 
    month: months[threeDaysLater.getMonth() + 1],
    // get year 
    year: threeDaysLater.getFullYear(),
    name: "Assemblée générale de l'association",
    imageLink: STORAGE_URL + "event-1.png",
  },
  // an event 1 month later
  {
    day : new Date().getDate(),
    month: months[new Date().getMonth() + 2],
    year: new Date().getFullYear(),
    name: "Soirée au club - DJ Yuzu en live",
    imageLink: STORAGE_URL + "event-2.jpg",
  },
  // an event 1 month and a half later
  {
    day : oneMonthAndHalfLater.getDate(),
    month: months[oneMonthAndHalfLater.getMonth() + 1],
    year: oneMonthAndHalfLater.getFullYear(),
    name: "Olympiades au campus de l'école !",
    imageLink: STORAGE_URL + "event-3.jpg",
  },
]

function getFormattedDate(day, month, year) {
  console.log (day)
  console.log (month)
  console.log (year)
  if (months.indexOf(month) === -1) return formatTwoDigits(day) + ".mm." + year;
  return formatTwoDigits(day) + "." + formatTwoDigits(months.indexOf(month)) + "." + year;
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
  console.log(events)
  // if (!events.value) return [];
  if (!events) return [];

  events.forEach((value, index, array) => {
    value.date = getFormattedDate(
      value.day,
      value.month,
      new Date().getFullYear()
    );
  });
  return events;
});
</script>

<template>
  <h1>Actus</h1>
  <div id="events-cards" v-if="allEvents.length > 0">
    <div v-for="event in allEvents" class="event-card">
      <a :href="event.link" target="_blank">
        <p><img class="img" :src="event.imageLink" :alt="event.name" /></p>
        <div class="description">
          <h2 class="title">{{ event.name }}</h2>
          <span class="date">{{ event.date }}</span>
        </div>
      </a>
    </div>
  </div>
  <div v-else class="no-event-msg">Aucun événement à venir</div>
</template>

<style scoped>

.no-event-msg {
    text-align: center;
    margin-top: 60px;
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
}

#events-cards .event-card .date {
  padding: 5px;
  position: relative;
  width: 90px;
  height: 30px;
  left: 95px;
  top: -90px;

  font-family: "Inter";
  font-weight: bold;
  text-align: center;

  background: #f84e35;
  border-radius: 15px;
}

#events-cards .event-card .img {
  border-radius: 15px;
  width: 300px;
  margin-top: 15px;
  object-fit: cover;
}

#events-cards .event-card div.description {
  margin: 0 auto -10px auto;
  width: 300px;
  text-align: center;
  color: white;
}

#events-cards .event-card p {
  text-align: center;
  margin-bottom: 0;
}

#events-cards .event-card p.event-link {
  text-align: right;
}
#events-cards .event-card .link {
  text-decoration: underline;
  font-size: 12px;
}

a:hover {
  color: white;
}
</style>