<script setup>
import {computed, ref} from 'vue';
import { useFetch } from '../composables/fetch';
import { useLocalstorage } from '../composables/localstorage';
import { apiUserLinks} from '../config/apiUrls.js';


let currentCategory = "";
const menusCafet = ref(null);
const {value: theLinks} = useLocalstorage('links', null);


const menuCafetUrl = "https://top-chef-intra-api.blacktree.io/weeks/current";
    fetch(menuCafetUrl, {
		method: "GET",
		withCredentials: true,
		headers: {
			"x-api-key": "xY79sN6FDiN6PXucrsBiQBeWkasTLCEn9hcD@dGBcH2Q22f*zs9LHzsfdshT_JBV.Td_ZRdCqQdm4RNFY8JTE!tLK@.GA!2YLNoo",
			"Content-Type": "application/json"
		}
    })
    .then(resp => resp.json())
    .then(function(data) {
		menusCafet.value = data;
    })
    .catch(function(error) {
		console.log(error);
    });

	const {data: userLinks} = useFetch(apiUserLinks);

	
const mainLinks = ref([
	{
		"name":"Attestation d'études",
		"category":"Semestre",
		"link":"https://www.heig-vd.ch"
	},
	{
		"name":"Calendrier Académique",
		"category":"Semestre",
		"link":"https://www.heig-vd.ch"
	},
	{
		"name":"Justificatif d'absences",
		"category":"Absences",
		"link":"https://www.heig-vd.ch"
	},
	{
		"name":"Webmail",
		"category":"Absences",
		"link":"https://www.heig-vd.ch"
	},
	{
		"name":"Bulletin",
		"category":"Notes",
		"link":"https://www.heig-vd.ch"
	},
	{
		"name":"Contrôles continus",
		"category":"Notes",
		"link":"https://www.heig-vd.ch"
	},
	{
		"name":"Enseignement à choix",
		"category":"Enseignement",
		"link":"https://www.heig-vd.ch"
	},
	{
		"name":"Eval. des enseignants",
		"category":"Enseignement",
		"link":"https://www.heig-vd.ch"
	}

]);

const links = computed(() => {
	if (!theLinks.value) {
		theLinks.value = mainLinks.value;
	}
	if (!userLinks.value) return mainLinks.value;
	// let links = mainLinks.value;
	userLinks.value.forEach((link, index, array) => {
		const aLink = {
			name: link.name,
			category: "Mes liens",
			link: link.link
		}
		if (theLinks.value.findIndex(l => l.name == link.name) === -1) {
			theLinks.value.unshift(aLink);
		}
		
	})
	return theLinks.value;
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

function formatDateFr(date) {
	let day = formatTwoDigits(new Date(date).getUTCDate());
	let month = formatTwoDigits(new Date(date).getUTCMonth() + 1);
	let year = new Date(date).getFullYear();
	return day + "." + month + "." + year;
}

function getDayFr(date) {
	const dayFr = new Date(date).toLocaleDateString("fr-FR", {weekday: 'long'});
	// first letter uppercase
	return dayFr.charAt(0).toUpperCase() + dayFr.slice(1);
}

const menusFormatted = computed(() => {
	if (!menusCafet.value) return []
	
	const cafeteria = {
		week: menusCafet.value.week,
		monday: menusCafet.value.monday,
		friday: menusCafet.value.friday,
		days: []
	}
	let days = {};
	menusCafet.value.days.forEach(menus => {
		days = {
			"day": menus.day,
			"date": formatDateFr(menus.day),
			"menus": []
		}
		menus.date = formatDateFr(menus.day);
		menus.dayFr = getDayFr(menus.day);
		let hasMeals = false;
		let index = 1;
		menus.menus.forEach(meal => {
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

	})
	return cafeteria
});

</script>

<template>
	<h1>Liens utiles</h1>
	<div class="content">		
		<div v-for="(link) in links" class="link">
			<h2 v-if="link.category != currentCategory">{{currentCategory = link.category}}</h2>
			<a :href=link.link >{{link.name}}</a>
		</div>
	</div>

	<div class="menu-cafeteria">
		<h1>Cafétéria - Semaine {{menusFormatted.week}}</h1>
		<div class="menu-day" v-for="menu of menusFormatted.days">
			<h2>{{menu.dayFr}} : {{menu.date}}</h2>
			<p v-show="!menu.hasMeals">Pas de menu aujourd'hui</p>
			<div v-for="meal of menu.menus" v-show="menu.hasMeals">
				<h3> Menu {{meal.index}}</h3>
				<h4>Entrée</h4>
				<p>{{meal.starter}}</p>
				<h4>Plat</h4>
					<!-- <ul> -->
						<p v-for="mainCourse of meal.mainCourse">{{mainCourse}}</p>
						<!-- <li v-for="mainCourse of meal.mainCourse">{{mainCourse}}</li>
					</ul> -->
					<span v-show="meal.containsPork">Ce menu contient du porc</span>
				<h4>Dessert</h4>
				<p>{{meal.dessert}}</p>
				<br>
				<p>------------- SEPARATION MENUS ----------------------</p>
			</div>
			<p>----------- SEPARATION JOURS----------------</p>
		</div>
	</div>
</template>
<style>
	.link h2 {
	font-family: 'Inter';
	font-style: normal;
	font-weight: 700;
	font-size: 20px;
	line-height: 30px;

	letter-spacing: -0.02em;

	margin: 14px auto 5px auto;
	}

	h3 {
	font-family: 'Inter';
	font-style: normal;
	font-weight: 700;
	font-size: 17px;
	line-height: 30px;

	letter-spacing: -0.02em;

	margin: 14px auto 5px auto;
	}

	h4 {
	font-family: 'Inter';
	font-style: normal;
	font-weight: 700;
	font-size: 14px;
	line-height: 30px;

	letter-spacing: -0.02em;

	margin: 14px auto 5px auto;
	}

	.menu-day {
		text-align: center;
	}
	a {
		width: 100%;

		text-decoration: none;
		color: #C8D7F4;

		font-family: 'Inter';
		font-style: normal;
		font-weight: 600;
		font-size: 16px;
		line-height: 22px;

		letter-spacing: -0.02em;
	}

	hr {
		margin: 30px auto;
		width: 80%;
		border-top: 1px solid #FFFFFF;
		opacity: 0.6;
	}
</style>