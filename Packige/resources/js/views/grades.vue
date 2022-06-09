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
  <h1>Notes</h1>
  <GradeModal v-show="showModal" @close="showModal = false" :id="id" />
  <button @click="addGrade()">Ajouter une note</button>

  <div v-for="(moduleData, moduleName) in grades" class="module">
    <h2>{{ moduleName }}</h2>
    <div v-for="(courseData, courseName) in moduleData">
      <div v-if="courseName != 'average'">
        <h3>{{ courseName }}</h3>
        <span class="courseWeighting">{{ courseData.weighting }}</span>
        <br />
        <div v-for="gradeData in courseData.grades" @click="editGrade();">
          Note : {{ gradeData.grade }} | Pondération :
          {{ gradeData.coefficient }}
        </div>
        <span class="course">Moyenne : {{ courseData.average }}</span>
      </div>
    </div>
    Moyenne de module : {{ moduleData.average }}
  </div>
</template>

<style scoped>
  .module {
    padding: 25px;

    margin-left: auto;
    margin-right: auto;
    margin-bottom: 40px;

    width: 90%;

    border: 3px solid;
    border-image: linear-gradient(28.82deg, #C8D7F4 -39.19%, #F84E35 3.81%, #77B0C5 52.2%, #0C223F 116.48%) 1;
    border-radius: 25px;

  }

  .module h2 {
    font-family: 'Inter';
    font-style: normal;
    font-weight: 700;
    font-size: 24px;
    line-height: 30px;
  }

  .module h3 {
    font-family: 'Inter';
    font-style: normal;
    font-weight: 400;
    font-size: 20px;
    line-height: 30px;

    width: 80%;

    display: inline-block;

    margin-left: 5px;
  }

  .module .courseWeighting {
    font-family: 'Inter';
    font-style: normal;
    font-weight: 400;
    font-size: 14px;
    line-height: 30px;

    opacity: 0.6;
  }
</style>