<script setup>
import { computed, ref } from "vue";
import { useFetch } from "../composables/fetch";
import { useLocalstorage } from "../composables/localstorage";
import { apiUserLinks } from "../config/apiUrls.js";

let currentCategory = "";
const menusCafet = ref(null);
// const { value: theLinks } = useLocalstorage("links", null);

const menuCafetUrl = "https://top-chef-intra-api.blacktree.io/weeks/current";
fetch(menuCafetUrl, {
  method: "GET",
  withCredentials: true,
  headers: {
    "x-api-key":
      "xY79sN6FDiN6PXucrsBiQBeWkasTLCEn9hcD@dGBcH2Q22f*zs9LHzsfdshT_JBV.Td_ZRdCqQdm4RNFY8JTE!tLK@.GA!2YLNoo",
    "Content-Type": "application/json",
  },
})
  .then((resp) => resp.json())
  .then(function (data) {
    menusCafet.value = data;
  })
  .catch(function (error) {
    console.log(error);
  });

const { data: userLinks } = useFetch(apiUserLinks);

const mainLinks = ref([
  {
    name: "Attestation d'études",
    category: "Semestre",
    link: "https://intra.heig-vd.ch/academique/attestation-etude/Pages/default.aspx",
  },
  {
    name: "Calendrier Académique",
    category: "Semestre",
    link: "https://intra.heig-vd.ch/academique/calendriers-academiques/Pages/calendriers-academiques.aspx",
  },
  {
    name: "Justificatif d'absences",
    category: "Absences",
    link: "https://intra.heig-vd.ch/academique/formulaire-absence/Pages/default.aspx",
  },
  {
    name: "Webmail",
    category: "Absences",
    link: "https://webmail.heig-vd.ch/owa/auth/logon.aspx?replaceCurrent=1&reason=3&url=https%3a%2f%2fwebmail.heig-vd.ch%2fowa%2f",
  },
  {
    name: "Bulletin",
    category: "Notes",
    link: "https://gaps.heig-vd.ch/consultation/notes/bulletin.php?id=17484&format=pdf&timestamp=1655105738532",
  },
  {
    name: "Contrôles continus",
    category: "Notes",
    link: "https://gaps.heig-vd.ch/consultation/controlescontinus/consultation.php?idst=17484",
  },
  {
    name: "Enseignement à choix",
    category: "Enseignement",
    link: "https://gaps.heig-vd.ch/consultation/choixOption/",
  },
  {
    name: "Eval. des enseignants",
    category: "Enseignement",
    link: "https://gaps.heig-vd.ch/consultation/evaluationenseignements/index.php",
  },
]);

const links = computed(() => {
  // if (!theLinks.value) {
  //   theLinks.value = mainLinks.value;
  // }
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

function formatDateFr(date) {
  let day = formatTwoDigits(new Date(date).getUTCDate());
  let month = formatTwoDigits(new Date(date).getUTCMonth() + 1);
  let year = new Date(date).getFullYear();
  return day + "." + month + "." + year;
}

function getDayFr(date) {
  const dayFr = new Date(date).toLocaleDateString("fr-FR", { weekday: "long" });
  // first letter uppercase
  return dayFr.charAt(0).toUpperCase() + dayFr.slice(1);
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
  return cafeteria;
});
</script>

<template>
  <h1>Liens utiles</h1>
  <div class="content">
    <div v-for="link in links" class="link">
      <h2 v-if="link.category != currentCategory">
        {{ (currentCategory = link.category) }}
      </h2>
      <a :href="link.link">{{ link.name }}</a>
    </div>
  </div>

  <div class="menu-cafeteria">
    <h1>Cafétéria - Semaine {{ menusFormatted.week }}</h1>
    <div class="menu-day" v-for="menu of menusFormatted.days">
      <div class="big-line"></div>
      <h2>{{ menu.dayFr }} : {{ menu.date }}</h2>
      <p v-show="!menu.hasMeals">Pas de menu aujourd'hui</p>
      <div v-for="meal of menu.menus" v-show="menu.hasMeals">
        <h3>| Menu {{ meal.index }} |</h3>
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
</template>
<style>
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