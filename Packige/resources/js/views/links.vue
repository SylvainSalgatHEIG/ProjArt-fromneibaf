<script setup>
import {ref} from 'vue';

const msg = ref('');

let currentCategory = "";

const links = [
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
]

</script>

<template>
	<h1>Liens utiles</h1>
	<div v-for="(link) in links">
		<h2 v-if="link.category != currentCategory">{{currentCategory = link.category}}</h2>
		<a :href=link.link>{{link.name}}</a>
	</div>
</template>