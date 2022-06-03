<script setup>
import { ref } from "vue";
import { useFetch } from "../composables/fetch.js";
import GradeModal from "../components/GradeModal.vue";

let showModal = ref(false);

const update = ref(0);

const { data: gradesArray } = useFetch("/api/grades/get");

function newGrade(data){
  console.log(data);
  // const {data:gradesArray} = useFetch("/api/grades/get");
}
</script>

<template>

  <GradeModal v-show="showModal" @close="showModal = false" @newGrade="newGrade"/>
  <button @click="showModal = true">Ajouter une note</button>

  <div v-for="(moduleData, moduleName) in gradesArray">
    <h1>{{ moduleName }}</h1>
    Moyenne de module : {{ moduleData.average }}
    <div v-for="(courseData, courseName) in moduleData">
      <div v-if="courseName != 'average'">
        <h2>{{ courseName }}</h2>
        Pondération : {{ courseData.weighting }}
        <br />
        <div v-for="gradeData in courseData.grades">
          Note : {{ gradeData.grade }} | Pondération :
          {{ gradeData.coefficient }}
        </div>
        Moyenne : {{ courseData.average }}
      </div>
    </div>
  </div>
</template>