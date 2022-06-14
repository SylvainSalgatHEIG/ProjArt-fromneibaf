<script setup>
import { computed, ref } from "vue";
import { useFetch } from "../composables/fetch.js";
import GradeModal from "../components/GradeModal.vue";
import { grades } from "../stores/grades.js";
import NotConnected from "../components/NotConnected.vue";
const { data: connexionStatus } = useFetch("/api/connexion/status");

let gradeId = ref(null);

function editGrade(gradeToEdit) {
  // console.log(gradeToEdit.id);
  gradeId.value = gradeToEdit.id;
  showModal.value = true;
}

function addGrade() {
  gradeId.value = null;
  showModal.value = true;
}

let showModal = ref(false);

const averages = computed(() => {
  if (!grades.value) return [];
  let allAverages = [];
  for (const moduleName in grades.value) {
    // console.log(grades.value[moduleName]);
    // format: { "moduleName": "moduleAverage": 3.4, 0: ["gradeName1": 2.3, "gradeName2": 3.4] }
    let moduleAverageGradesTotal = 0;
    let moduleAverageDivision = 0;
    let result = 0;
    let gradeAverage = [];
    for (const grade in grades.value[moduleName]) {
      if (grade !== "average" && grade !== "id") {
        let resultGrade = 0;
        allAverages[moduleName] = [];
        let gradesAverageTotal = 0;
        let gradesAverageDivision = 0;
        if (grades.value[moduleName][grade].grades.length > 0) {
          for (const theGradeData of grades.value[moduleName][grade].grades) {
            gradesAverageTotal += theGradeData.grade * theGradeData.coefficient;
            gradesAverageDivision += theGradeData.coefficient;
          }
          resultGrade =
            Math.round((gradesAverageTotal / gradesAverageDivision) * 10) / 10;
          moduleAverageGradesTotal +=
            resultGrade * grades.value[moduleName][grade].weighting;
          moduleAverageDivision += grades.value[moduleName][grade].weighting;
        } else {
          moduleAverageGradesTotal = 0;
          moduleAverageDivision = 0;
        }
        gradeAverage[grade] = resultGrade;
      }
    }
    allAverages[moduleName].push(gradeAverage);
    if (moduleAverageDivision > 0) {
      allAverages[moduleName]["moduleAverage"] =
        Math.round((moduleAverageGradesTotal / moduleAverageDivision) * 10) /
        10;
    } else {
      allAverages[moduleName]["moduleAverage"] = 0;
    }
  }
  // console.log(allAverages);
  return allAverages;
});
</script>

<template>
  <h1>Notes</h1>
  <div id="notConnected" v-if="!connexionStatus">
    <not-connected></not-connected>
  </div>
  <div class="content">
    <div id="connected" v-if="connexionStatus">
      <grade-modal
        v-show="showModal"
        @close="showModal = false"
        :id="gradeId"
      />
      <div @click="addGrade()" id="btnAddGrade"></div>

      <div v-for="(moduleData, moduleName) in grades" class="module">
        <h2>{{ moduleName }}</h2>
        <div v-for="(courseData, courseName) in moduleData">
          <div v-if="courseName != 'average' && courseName != 'id'">
            <h3>{{ courseName }}</h3>
            <span class="courseWeighting">{{ courseData.weighting }}</span>
            <br />
            <!-- <div
            v-for="gradeData in courseData.grades"
            @click="editGrade(gradeData)"
            id="btnEditGrade"
          >
            Note : {{ gradeData.grade }} | Pondération :
            {{ gradeData.coefficient }}
          </div> -->
            <div v-if="courseData.grades.length != 0">
              <table class="tb-grades">
                <tr>
                  <th>Note</th>
                  <th>Pondération</th>
                </tr>
                <tr
                  v-for="gradeData in courseData.grades"
                  @click="editGrade(gradeData)"
                  class="btnEditGrade"
                >
                  <td class="bold">{{ gradeData.grade }}</td>
                  <td>{{ gradeData.coefficient }}%</td>
                </tr>
              </table>
              <div class="courseAverage">
                <span>Moyenne : {{ averages[moduleName][0][courseName] }}</span>
              </div>
            </div>
            <div v-else>
              <span>Aucune note</span>
            </div>
          </div>
        </div>
        <div class="moduleAverage">
          Moyenne de module : {{ averages[moduleName]["moduleAverage"] }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
#connected {
  margin-top: 27px;
}

.courseAverage {
  width: 50%;
  min-width: 280px;
  margin: auto;
  text-align: right;
}

.bold {
  font-weight: bold;
}

.tb-grades {
  width: 50%;
  min-width: 280px;
  border-collapse: separate;
  border-spacing: 0 5px;
  text-align: center;
  margin: 0 auto;
}

.tb-grades th {
  font-family: "Inter";
  font-style: normal;
  font-weight: 400;
  font-size: 16px;
  line-height: 30px;
  /* or 188% */

  letter-spacing: -0.02em;
  font-feature-settings: "calt" off;

  /* Second elements */

  color: rgba(255, 255, 255, 0.6);
}

.tb-grades .btnEditGrade td:first-child {
  border-radius: 4px 0 0 4px;
  width: 60%;
}

.tb-grades .btnEditGrade td:last-child {
  border-radius: 0 4px 4px 0;
}

.tb-grades .btnEditGrade {
  background-color: #77b0c5;
  color: white;
  padding: 10px;
  margin: 5px;
}

.tb-grades .btnEditGrade:hover {
  background-color: #5c9fb0;
  cursor: pointer;
}

#btnAddGrade {
  width: 58px;
  height: 58px;

  border-radius: 50%;
  background-color: #ff3820;

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
  border-image: linear-gradient(
      28.82deg,
      #c8d7f4 -39.19%,
      #f84e35 3.81%,
      #77b0c5 52.2%,
      #0c223f 116.48%
    )
    1;
}

.module h2 {
  font-family: "Inter";
  font-style: normal;
  font-weight: 700;
  font-size: 24px;
  line-height: 30px;
}

.module h3 {
  font-family: "Inter";
  font-style: normal;
  font-weight: 400;
  font-size: 20px;
  line-height: 30px;

  width: auto;

  display: inline-block;

  margin-left: 5px;
}

.module .courseWeighting {
  font-family: "Inter";
  font-style: normal;
  font-weight: 400;
  font-size: 14px;
  line-height: 30px;

  opacity: 0.6;

  margin-left: 10px;
}

.moduleAverage {
  font-family: "Inter";
  font-style: normal;
  font-weight: 700;
  font-size: 16px;
  line-height: 30px;
}
</style>