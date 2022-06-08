<script setup>
import { ref } from "vue";
import { useFetch } from "../composables/fetch.js";
import GradeModal from "../components/GradeModal.vue";
import { grades } from "../stores/grades.js";

let id = ref(null);

function editGrade(note) {
  id.value = note.id;
  showModal.value = true;
}

function addGrade(){
  id.value = null;
  showModal.value = true;
}

let showModal = ref(false);

</script>

<template>
  <GradeModal v-show="showModal" @close="showModal = false" :id="id" />
  <button @click="addGrade()">Ajouter une note</button>

  <div v-for="(moduleData, moduleName) in grades">
    <h1>{{ moduleName }}</h1>
    Moyenne de module : {{ moduleData.average }}
    <div v-for="(courseData, courseName) in moduleData">
      <div v-if="courseName != 'average'">
        <h2>{{ courseName }}</h2>
        Pondération : {{ courseData.weighting }}
        <br />
        <div v-for="gradeData in courseData.grades" @click="editGrade(gradeData);">
          Note : {{ gradeData.grade }} | Pondération :
          {{ gradeData.coefficient }}
        </div>
        Moyenne : {{ courseData.average }}
      </div>
    </div>
  </div>
</template>