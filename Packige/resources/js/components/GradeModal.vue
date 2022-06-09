<script setup>
import { useFetch, usePost } from "../composables/fetch";
import { computed, ref, watch, watchEffect } from "vue";
import { grades } from "../stores/grades.js";

const { data: coursesArray } = useFetch("/api/courses/");

const emit = defineEmits(["close"]);
const props = defineProps({
  id: {}
});

let btnText = ref("Ajouter");
let coefficient = ref("");
let grade = ref("");
let course = ref("");
let disabledSelect = ref(false);
let deleBtnPressed = ref(false);

watchEffect(() => {
  if (grades.value != null && coursesArray.value != null) {
    let pass = false;
    for (const courseData of coursesArray.value) {
      for (const gradeData of grades.value[courseData.moduleName][
        courseData.courseName
      ].grades) {
        if (gradeData.id === props.id) {
          console.log(gradeData);
          coefficient.value = gradeData.coefficient;
          grade.value = gradeData.grade;
          course.value = courseData.courseShortName;
          btnText.value = "Modifier";
          disabledSelect.value = true;
          pass = true;
        }
      }
    }
    if (!pass) {
      coefficient.value = "";
      grade.value = "";
      course.value = "";
      disabledSelect.value = false;
      btnText.value = "Ajouter";
    }
  }
});

function deleteBtnClicked() {
  deleBtnPressed.value = true;
}

function addOrEditGrade(id = props.id) {
  let moduleName = "";
  let courseFullName = "";

  for (const courseData of coursesArray.value) {
    if (courseData.courseShortName === course.value) {
      courseFullName = courseData.courseName;
      moduleName = courseData.moduleName;
    }
  }
  if (id && deleBtnPressed.value) {
    console.log("delete");
    const data = {
      id: id,
    };
    deleteGrade(data, courseFullName, moduleName)
  } else if (id) {
    console.log("edit");
    const data = {
      grade: grade.value,
      coefficient: coefficient.value,
      course: course.value,
      id: id,
    };
    editGrade(data, courseFullName, moduleName);
  } else {
    console.log("add");
    const data = {
      grade: grade.value,
      coefficient: coefficient.value,
      course: course.value,
    };
    addGrade(data, courseFullName, moduleName);
  }
  emit("close");
}

function editGrade(data, courseFullName, moduleName) {
  usePost({ url: "/api/grades/edit", data: data });

  for (const gradeData of grades.value[moduleName][courseFullName].grades) {
    if (gradeData.id === data.id) {
      gradeData.grade = grade.value;
      gradeData.coefficient = coefficient.value;
    }
  }
}

function addGrade(data, courseFullName, moduleName) {
  usePost({ url: "/api/grades/add", data: data });

  // Faire un test si la note à été ajoutée à la base
  grades.value[moduleName][courseFullName].grades.push({
    grade: grade.value,
    coefficient: coefficient.value,
  });
}

function deleteGrade(data, courseFullName, moduleName) {
  // Supprimer la note de la base
  usePost({ url: "/api/grades/delete", data: data });
  // Supprimer la note du tableau
  for(let i = 0; i < grades.value[moduleName][courseFullName].grades.length; i++){
    if(grades.value[moduleName][courseFullName].grades[i].id == data.id){
      grades.value[moduleName][courseFullName].grades.splice(i, 1)
    }
  }

  deleBtnPressed.value = false;
}
</script>

<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <slot name="header"> {{btnText}} une note </slot>
          </div>
          <div class="modal-body">
            <slot name="body">
              <form @submit.prevent="addOrEditGrade()">
                <label for="grade">Note :</label><br />
                <input
                  type="number"
                  v-model="grade"
                  id="grade"
                  step="0.01"
                  required
                />
                <label for="coefficient">Coefficient :</label><br />
                <input
                  type="number"
                  v-model="coefficient"
                  id="coefficient"
                  step="0.01"
                  required
                />
                <label for="course">Cours :</label><br />
                <select id="course" v-model="course" :disabled="disabledSelect">
                  <option
                    :value="course.courseShortName"
                    v-for="course in coursesArray"
                  >
                    {{ course.courseShortName }}
                  </option>
                </select>
                <br />
                <button class="modal-default-button">
                  {{ btnText }}
                </button>
                <button
                  class="modal-default-button"
                  v-show="disabledSelect"
                  @click="deleteBtnClicked"
                >
                  del
                </button>
              </form>
            </slot>
          </div>

          <div class="modal-footer">
            <slot name="footer">
              default footer
              <button class="modal-default-button" @click="$emit('close')">
                Fermer
              </button>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<style>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 300px;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-body {
  margin: 20px 0;
}

.modal-default-button {
  float: right;
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>