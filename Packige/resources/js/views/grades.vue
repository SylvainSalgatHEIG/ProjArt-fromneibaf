<script setup>
import { ref } from "vue";
import { useFetch } from "../composables/fetch.js";

const msg = ref("");

const { data: gradesArray } = useFetch("/api/grades/");
</script>

<template>
  <div v-for="(moduleData, moduleName) in gradesArray">
    <h1>{{ moduleName }}</h1>
    <div v-for="(coursesData, coursesName) in moduleData">
		<h2 v-if="coursesName != 'average'">{{coursesName}}</h2>
		{{coursesData}}
		<div v-for="(courseData, courseName) in coursesData">
		{{courseName}}
		</div>
	</div>
  </div>
</template>