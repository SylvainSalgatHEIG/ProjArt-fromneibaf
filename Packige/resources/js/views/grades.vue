<script setup>
import { ref } from "vue";
import { useFetch } from "../composables/fetch.js";
import GradeModal from "../components/GradeModal.vue";
import { grades } from "../stores/grades.js";

let id = ref(null);

function editGrade(note) {
  console.log(note.id);
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
  <div @click="addGrade()" id="btnAddGrade"></div>

  <div v-for="(moduleData, moduleName) in grades" class="module">
    <h2>{{ moduleName }}</h2>
    <div v-for="(courseData, courseName) in moduleData">
      <div v-if="courseName != 'average'">
        <h3>{{ courseName }}</h3>
        <span class="courseWeighting">{{ courseData.weighting }}</span>
        <br />
        <div v-for="gradeData in courseData.grades" @click="editGrade(gradeData);">
          Note : {{ gradeData.grade }} | Pondération :
          {{ gradeData.coefficient }}
        </div>
        <span class="course">Moyenne : {{ courseData.average }}</span>
      </div>
    </div>
    <div class="moduleAverage">
      Moyenne de module : {{ moduleData.average }}
    </div>
    
  </div>
</template>

<style scoped>

  #btnAddGrade {
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
  }
  .module {
    padding: 25px;

    margin-left: auto;
    margin-right: auto;
    margin-bottom: 40px;

    width: 90%;

    border-radius: 25px;
    border: 3px solid transparent;
    border-image: linear-gradient(28.82deg, #C8D7F4 -39.19%, #F84E35 3.81%, #77B0C5 52.2%, #0C223F 116.48%) 1;

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

    width: auto;

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

    margin-left: 10px;
  }

  .moduleAverage {
    font-family: 'Inter';
    font-style: normal;
    font-weight: 700;
    font-size: 16px;
    line-height: 30px;
  }
  
</style>