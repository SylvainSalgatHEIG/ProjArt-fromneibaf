<script setup>
import { computed, ref } from "vue";
import { useFetch } from "../composables/fetch";
import LinkModal from "../components/LinkModal.vue";
import { useLocalstorage } from "../composables/localstorage";
import { userLinks } from "../stores/links.js";

let currentCategory = "";
// const menusCafet = ref(null);
const currentDate = new Date();
const startDate = new Date(currentDate.getFullYear(), 0, 1);
var numberOfDays = Math.floor((currentDate - startDate) /
  (24 * 60 * 60 * 1000));

var weekNumber = Math.ceil(numberOfDays / 7);

function getDayFromNb(dayNb, date) {
  if (dayNb < 1 || dayNb > 7) {
    throw new Error('dayNb should be between 1 and 7');
  }

  const currentDay = date.getDay(); // Sunday - 0, Monday - 1, etc.
  const difference = dayNb - currentDay + (currentDay === 0 ? 1 : 0); // Adjusting if today is Sunday
  const targetDate = new Date(date);
  targetDate.setDate(date.getDate() + difference);

  return targetDate;
}

// Example usage:
// console.log("getDayFunction", getDayFromNb(1, new Date()));


const menusCafet = ref({
  week: weekNumber,
  // date of monday and friday the current week
  monday: getDayFromNb(1, currentDate),
  friday: getDayFromNb(5, currentDate),
  days: [
    {
      day: getDayFromNb(1, currentDate),
      menus: [
        {
          index: 0,
          starter: "Potage Garbure",
          mainCourse: ["Grillade de saucisses (CH)*", "Sauce chimichurri", "Epinards à la crème", "Boulgour aux poivrons"],
          dessert: "Trilogie de pommes (CH) ",
          containsPork: true,
          isVeggie: false,
        },
        {
          index: 0,
          starter: "Potage Garbure",
          mainCourse: ["Moussaka de lentilles", "Aubergines et tofu", "Bouquet de salade"],
          dessert: "Trilogie de pommes (CH) ",
          containsPork: false,
          isVeggie: true,
        },
      ],
    },
    {
      day: getDayFromNb(2, currentDate),
      menus: [
        {
          index: 0,
          starter: "Samoussas de Chèvre frais aux fines Herbes",
          mainCourse: ["Poulet 'Label rouge' rôti", "Gratin dauphinois", "Salade de Tomates 'Coeur de Boeuf'"],
          dessert: "Cheesecake aux Oranges",
          containsPork: false,
          isVeggie: false,
        },
      ],
    },
    {
      day: getDayFromNb(3, currentDate),
      menus: [
        {
          index: 0,
          starter: "Carottes râpées",
          mainCourse: ["Filet mignon*", "Haricots verts", "Patates vapeur"],
          dessert: "Coeur coulant au Chocolat",
          containsPork: true,
          isVeggie: false,
        },
      ],
    },
    {
      day: getDayFromNb(4, currentDate),
      menus: [
        {
          index: 0,
          starter: "Soupe aux légumes",
          mainCourse: ["Poisson en papillote", "Epinards", "Pâtes", "Riz"],
          dessert: "Crème anglaise et chantilly",
          containsPork: false,
          isVeggie: false,
        },
        {
          index: 0,
          starter: "Soupe aux légumes",
          mainCourse: ["Semoule au pois chiches", "Epinards", "Pâtes", "Riz"],
          dessert: "Crème anglaise et chantilly",
          containsPork: false,
          isVeggie: true,
        },
      ],
    },
    {
      day: getDayFromNb(5, currentDate),
      menus: [
        {
          index: 0,
          starter: "Bouillon",
          mainCourse: ["Betteraves", "Escalopes de dinde", "Pâtes", "Champignons"],
          dessert: "Tartare d'Ananas",
          containsPork: false,
          isVeggie: false,
        },
        {
          index: 0,
          starter: "Soupe au butternut",
          mainCourse: ["Chou fleur vinaigrette", "Riz aux petits légumes"],
          dessert: "Yaourt aux fruits",
          containsPork: false,
          isVeggie: true,
        },
      ],
    },
    // {
    //   day: getDayFromNb(6, currentDate),
    //   date: currentDate,
    //   menus: [
    //     {
    //       index: 0,
    //       starter: "Soupe au butternut",
    //       mainCourse: ["Rôti de porc*", "Riz aux petits légumes"],
    //       dessert: "Yaourt aux fruits",
    //       containsPork: true,
    //       isVeggie: false,
    //     },
    //     {
    //       index: 0,
    //       starter: "Soupe au butternut",
    //       mainCourse: ["Chou fleur vinaigrette", "Riz aux petits légumes"],
    //       dessert: "Yaourt aux fruits",
    //       containsPork: false,
    //       isVeggie: true,
    //     },
    //   ],
    // },
  ],
});
// console.log(menusCafet.value);

const { data: connexionStatus } = useFetch("/api/connexion/status");

/* Fetch data for cafeteria menu */
// const menuCafetUrl = "https://top-chef-intra-api.blacktree.io/weeks/current";
// fetch(menuCafetUrl, {
//   method: "GET",
//   withCredentials: true,
//   headers: {
//     "x-api-key":
//       "xY79sN6FDiN6PXucrsBiQBeWkasTLCEn9hcD@dGBcH2Q22f*zs9LHzsfdshT_JBV.Td_ZRdCqQdm4RNFY8JTE!tLK@.GA!2YLNoo",
//     "Content-Type": "application/json",
//   },
// })
//   .then((resp) => resp.json())
//   .then(function (data) {
//     menusCafet.value = data;
//   })
//   .catch(function (error) {
//     console.log(error);
//   });

/* Array with main links (common to all users) */
const mainLinks = ref([
  {
    name: "Attestation d'études",
    category: "Semestre",
    link: "#",
  },
  {
    name: "Calendrier Académique",
    category: "Semestre",
    link: "#",
  },
  {
    name: "Justificatif d'absences",
    category: "Absences",
    link: "#",
  },
  {
    name: "Webmail",
    category: "Absences",
    link: "#",
  },
  {
    name: "Bulletin",
    category: "Notes",
    link: "#",
  },
  {
    name: "Contrôles continus",
    category: "Notes",
    link: "#",
  },
  {
    name: "Enseignement à choix",
    category: "Enseignement",
    link: "#",
  },
  {
    name: "Eval. des enseignants",
    category: "Enseignement",
    link: "#",
  },
]);

// Get users personal links
const links = computed(() => {
  if (!userLinks.value) return mainLinks.value;
  // let links = mainLinks.value;
  userLinks.value.forEach((link, index, array) => {
    const aLink = {
      name: link.name,
      category: "Mes liens",
      link: link.link,
    };
    if (mainLinks.value.findIndex((l) => l.name == link.name) === -1) {
      mainLinks.value.unshift(aLink);
    }
  });
  return mainLinks.value;
});

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

/**
 * Format date with FR format
 * @param {*} date 
 */
function formatDateFr(date) {
  // console.log("date", date)
  let day = formatTwoDigits(new Date(date).getUTCDate());
  let month = formatTwoDigits(new Date(date).getUTCMonth() + 1);
  let year = new Date(date).getFullYear();
  return day + "." + month + "." + year;
}

/**
 * Get day of the week in french
 * @param {*} date 
 */
function getDayFr(date) {
  const dayFr = new Date(date).toLocaleDateString("fr-FR", { weekday: "long" });
  // first letter uppercase
  return dayFr.charAt(0).toUpperCase() + dayFr.slice(1);
}

/**
 * Get index of current day (Exemple : monday = 1)
 */
function getCurrentDayIndex() {
  const timeElapsed = Date.now();
  const today = new Date(timeElapsed);

  return today.getDay() - 1;
}

const menusFormatted = computed(() => {
  if (!menusCafet.value) return [];

  const cafeteria = {
    week: menusCafet.value.week,
    monday: menusCafet.value.monday,
    friday: menusCafet.value.friday,
    days: [],
  };
  let days = {};
  menusCafet.value.days.forEach((menus) => {
    days = {
      day: menus.day,
      date: formatDateFr(menus.day),
      menus: [],
    };
    menus.date = formatDateFr(menus.day);
    menus.dayFr = getDayFr(menus.day);
    let hasMeals = false;
    let index = 1;
    menus.menus.forEach((meal) => {
      if (meal.mainCourse != "") {
        hasMeals = true;
        meal.index = index;
        days.menus.push(meal);
        index++;
      }
    });
    days.hasMeals = hasMeals;
    days.dayFr = getDayFr(days.day);
    if (!hasMeals) {
      days.menus = "Pas de menu aujourd'hui";
    }
    cafeteria.days.push(days);
  });
  // console.log("cafet", cafeteria)
  return cafeteria;
});

let linkId = ref(null);
let showModal = ref(false);

/**
 * Open modal for adding link
 */
function addLink() {
  linkId.value = null;
  showModal.value = true;
}


</script>

<template>
  <h1>Liens utiles</h1>
  <LinkModal v-show="showModal" @close="showModal = false" />
  <div @click="addLink()" id="btnAddLink" v-show="connexionStatus"></div>

  <div class="content">

    <div v-for="link in links" class="link">
      <h2 v-if="link.category != currentCategory">
        {{ (currentCategory = link.category) }}
      </h2>
      <a :href="link.link" target="_blank">{{ link.name }}</a>
    </div>
  </div>

  <div class="menu-cafeteria">
    <h1>Cafétéria - Semaine {{ menusFormatted.week }}</h1>
    <div class="menu-day" v-for="(menu, index) of menusFormatted.days">
      <div v-if="index >= getCurrentDayIndex()">
        <div class="big-line"></div>
        <h2>{{ menu.dayFr }} : {{ menu.date }}</h2>
        <p v-show="!menu.hasMeals">Pas de menu aujourd'hui</p>
        <div v-for="meal of menu.menus" v-show="menu.hasMeals">
          <h3>| Menu {{ meal.index }} <span v-if="meal.isVeggie">- Végétarien</span> |</h3>
          <h4>Entrée</h4>
          <p>{{ meal.starter }}</p>
          <div class="smallline"></div>
          <h4>Plat</h4>
          <!-- <ul> -->
          <div v-for="mainCourse of meal.mainCourse">{{ mainCourse }}</div>
          <!-- <li v-for="mainCourse of meal.mainCourse">{{mainCourse}}</li>
            </ul> -->
          <div class="porc" v-show="meal.containsPork">
            *Ce menu contient du porc
          </div>
          <div class="smallline"></div>
          <h4>Dessert</h4>
          <p>{{ meal.dessert }}</p>
          <br />
          <!-- <p>------------- SEPARATION MENUS ----------------------</p> -->
        </div>
        <!-- <p>----------- SEPARATION JOURS----------------</p> -->
      </div>
    </div>
  </div>
</template>
<style>
#btnAddLink {
  width: 58px;
  height: 58px;

  border-radius: 50%;
  background-color: #FF3820;

  position: fixed;

  bottom: 100px;

  right: 5%;

  background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M8 1V15' stroke='%230C223F' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpath d='M1 8H15' stroke='%230C223F' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
  background-repeat: no-repeat;
  background-position: center;
  background-size: 24px;

  cursor: pointer;
}

.content {
  margin-left: auto;
  margin-right: auto;
  width: 312px;
}

.porc {
  font-style: italic;
  color: #f84e35;
}

.smallline {
  opacity: 50%;
  width: 60px;
  height: 10px;
  border-bottom: 1px solid rgb(255, 255, 255);
  display: inline-block;
}

.big-line {
  width: 224px;
  height: 47px;
  border-bottom: 1px solid rgb(255, 255, 255);
  display: inline-block;
}

h2 {
  margin-top: 20px;
}

.link h2 {
  font-family: "Inter";
  font-style: normal;
  font-weight: 700;
  font-size: 20px;
  line-height: 30px;

  letter-spacing: -0.02em;

  margin: 14px auto 5px auto;
}

h3 {
  font-family: "Inter";
  font-style: normal;
  font-weight: 700;
  font-size: 17px;
  line-height: 30px;

  letter-spacing: -0.02em;

  margin-top: 35px;
}

h4 {
  font-family: "Inter";
  font-style: normal;
  font-weight: 700;
  font-size: 14px;
  line-height: 30px;

  letter-spacing: -0.02em;

  margin: 10px auto 5px auto;
}

.menu-day {
  text-align: center;
}

a {
  width: 100%;

  text-decoration: none;
  color: #c8d7f4;

  font-family: "Inter";
  font-style: normal;
  font-weight: 600;
  font-size: 16px;
  line-height: 22px;

  letter-spacing: -0.02em;
}

hr {
  margin: 30px auto;
  width: 80%;
  border-top: 1px solid #ffffff;
  opacity: 0.6;
}

p {
  margin: 0;
  padding: 0;
}
</style>