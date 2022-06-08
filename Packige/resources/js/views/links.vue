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
	<div class="content">
		<div v-for="(link) in links" class="link">
			<h2 v-if="link.category != currentCategory">{{currentCategory = link.category}}</h2>
			<a :href=link.link >{{link.name}}</a>
		</div>
	</div>
	<hr>
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

	.link a {
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