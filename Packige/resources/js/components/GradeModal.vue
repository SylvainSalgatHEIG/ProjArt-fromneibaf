<script setup>
import { useFetch, usePost } from "../composables/fetch";
import { computed, ref, watch, watchEffect } from "vue";
import { grades } from "../stores/grades.js";

const { data: coursesArray } = useFetch("/api/courses/");

const emit = defineEmits(["close"]);
const props = defineProps({
  id: {},
});

let btnText = ref("Ajouter");
let coefficient = ref("");
let grade = ref("");
let course = ref("");
let disabledSelect = ref(false);
let deleBtnPressed = ref(false);

watchEffect(() => {
  if (grades.value != null && coursesArray.value != null) {
    console.log(coursesArray);
    let pass = false;
    for (const courseData of coursesArray.value) {
      for (const gradeData of grades.value[courseData.moduleName][
        courseData.courseShortName
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
  let courseShortname = "";

  for (const courseData of coursesArray.value) {
    if (courseData.courseShortName === course.value) {
      moduleName = courseData.moduleName;
    }
  }
  if (id && deleBtnPressed.value) {
    console.log("delete");
    const data = {
      id: id,
    };
    deleteGrade(data, courseShortname, moduleName);
  } else if (id) {
    console.log("edit");
    const data = {
      grade: grade.value,
      coefficient: coefficient.value,
      course: course.value,
      id: id,
    };
    editGrade(data, courseShortname, moduleName);
  } else {
    console.log("add");
    const data = {
      grade: grade.value,
      coefficient: coefficient.value,
      course: course.value,
    };
    addGrade(data, courseShortname, moduleName);
  }
  emit("close");
}

function editGrade(data, courseShortname, moduleName) {
  usePost({ url: "/api/grades/edit", data: data });

  for (const gradeData of grades.value[moduleName][course.value].grades) {
    if (gradeData.id === data.id) {
      gradeData.grade = grade.value;
      gradeData.coefficient = coefficient.value;
    }
  }
}

function addGrade(data, courseShortname, moduleName) {
  const { results: newGradeId } = usePost({
    url: "/api/grades/add",
    data: data,
  });
  let added = false;
  watchEffect(() => {
    if (newGradeId.value != null && !added) {
      console.log(newGradeId.value);
      grades.value[moduleName][course.value].grades.push({
        id: newGradeId.value,
        grade: grade.value,
        coefficient: coefficient.value,
      });
      added = true;
    }
    console.log(grades.value);
  });
  // Faire un test si la note à été ajoutée à la base
}

function deleteGrade(data, courseShortname, moduleName) {
  // Supprimer la note de la base
  usePost({ url: "/api/grades/delete", data: data });
  // Supprimer la note du tableau
  for (
    let i = 0;
    i < grades.value[moduleName][course.value].grades.length;
    i++
  ) {
    if (grades.value[moduleName][course.value].grades[i].id == data.id) {
      grades.value[moduleName][course.value].grades.splice(i, 1);
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
            <slot name="header"> {{ btnText }} une note </slot>
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
                  placeholder="Note obtenue"
                />
                <label for="coefficient">Coefficient :</label><br />
                <input
                  type="number"
                  v-model="coefficient"
                  id="coefficient"
                  step="0.01"
                  required
                  placeholder="Pondération"
                />
                <label for="course">Cours :</label><br />
                <select id="course" v-model="course" :disabled="disabledSelect" placeholder="Branche">
                <option value="" disabled selected>Branche</option>
                  <option
                    :value="course.courseShortName"
                    v-for="course in coursesArray"
                  >
                    {{ course.courseShortName }}
                  </option>
                </select>
                <br />
                <button class="modal-default-button" v-bind:class="btnText == 'Ajouter' ? 'addButton': 'updateButton'">
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
              <div class="modal-default-button closeButton" @click="$emit('close')">
              </div>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<style>
div.modal-mask {
  position: fixed;
  z-index: 9998;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

div.modal-wrapper {
  display: table-cell;
  vertical-align: bottom;

  width: 100%;
}

div.modal-container {
  width: 100%;

  margin: 0px;
  padding: 0px 30px 44px 30px;

  background-color: #fff;

  border-radius: 25px 25px 0px 0px;

  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

div.modal-header {

  display: inline-block;

  width: 100%;

  border-bottom: none;

  margin-top: 44px;
  padding-left: auto;

  text-align: center;

  font-family: 'Outfit';
  font-style: normal;
  font-weight: 700;
  font-size: 32px;
  line-height: 36px;
}

.modal-body {
  margin: 20px 0;
}

.addButton {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  padding: 16px 50px;
  gap: 10px;

  width: 290px;
  height: 45px;

  margin-top: 88px;

  background: #F84E35;
  border-radius: 40px;

  font-family: 'Outfit';
  font-style: normal;
  font-weight: 600;
  font-size: 20px;
  line-height: 25px;

  color: #FFFFFF;

  border: none;
}

.closeButton {

  background-color: #F84E35;
  height: 10px;
  width: 10px;
}

.modal-footer {
  border-top: none;
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