<script setup>
import { useFetch, usePost } from "../composables/fetch";
import { computed, ref, watch, watchEffect } from "vue";
import { grades } from "../stores/grades.js";

const { data: coursesArray } = useFetch("/api/courses/");

const emit = defineEmits(["close"]);
const props = defineProps({
  id: {},
});

let name = ref("");
let btnText = ref("Ajouter");
let coefficient = ref("");
let grade = ref("");
let courseShortName = ref("");
let courseId = ref("");
let disabledSelect = ref(false);
let deleBtnPressed = ref(false);
// let actionDone = ref(false);
let error = ref(false);
let errorMsg = ref("");

watchEffect(() => {
  if (grades.value != null && coursesArray.value != null) {
    // console.log(coursesArray);
    let pass = false;
    for (const courseData of coursesArray.value) {
      // console.log(courseData);
      // console.log(grades.value[courseData.moduleName][courseData.courseShortName].grades);
      for (const gradeData of grades.value[courseData.moduleName][
        courseData.courseShortName
      ].grades) {
        // console.log(props.id + "props")

        // WHEN EDITING. props.id is the gradeId. Not null when editing a grade.
        if (gradeData.id === props.id) {
          // console.log(gradeData);
          name.value = gradeData.name;
          coefficient.value = gradeData.coefficient;
          grade.value = gradeData.grade;
          courseShortName.value = courseData.courseShortName;
          courseId.value = courseData.courseId;
          btnText.value = "Modifier";
          disabledSelect.value = true;
          pass = true;
        }
      }
    }
    // WHEN ADDING A GRADE. pass is set to true if it's a grade editing.
    if (!pass) {
      name.value = "";
      coefficient.value = "";
      grade.value = "";
      courseId.value = "";
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
    // console.log(courseId.value + "vvv");
    if (courseData.courseId === courseId.value) {
      moduleName = courseData.moduleName;
      courseShortname = courseData.courseShortName;
    }
  }

  // WHEN DELETING A GRADE.
  if (id && deleBtnPressed.value) {
    console.log("delete");
    const data = {
      id: id,
    };
    deleteGrade(data, courseShortname, moduleName);
    // WHEN EDITING A GRADE
  } else if (id) {
    console.log("edit");
    const data = {
      grade: grade.value,
      coefficient: coefficient.value,
      name: name.value,
      id: id,
    };
    editGrade(data, courseShortname, moduleName);
    // WHEN ADDING A GRADE
  } else {
    console.log("add");
    const data = {
      grade: grade.value,
      coefficient: coefficient.value,
      name: name.value,
      course: courseId.value,
    };
    addGrade(data, courseShortname, moduleName);
  }
  if (!error.value) {
    emit("close");
  }
}

function editGrade(data, courseShortname, moduleName) {
  usePost({ url: "/api/grades/edit", data: data });

  for (const gradeData of grades.value[moduleName][courseShortname].grades) {
    if (gradeData.id === data.id) {
      gradeData.grade = grade.value;
      gradeData.coefficient = coefficient.value;
    }
  }
  // TODO: check if no error on editing the grade.
  error.value = false;
}

function addGrade(data, courseShortname, moduleName) {
  const { results: newGradeId } = usePost({
    url: "/api/grades/add",
    data: data,
  });
  let added = false;
  watchEffect(() => {
    if (typeof newGradeId.value === "number" && !added) {
      grades.value[moduleName][courseShortname].grades.push({
        id: newGradeId.value,
        name: name.value,
        grade: grade.value,
        coefficient: coefficient.value,
      });
      added = true;
    }
  });
}

function deleteGrade(data, courseShortname, moduleName) {
  // Supprimer la note de la base
  usePost({ url: "/api/grades/delete", data: data });
  // Supprimer la note du tableau
  for (
    let i = 0;
    i < grades.value[moduleName][courseShortname].grades.length;
    i++
  ) {
    if (grades.value[moduleName][courseShortname].grades[i].id == data.id) {
      grades.value[moduleName][courseShortname].grades.splice(i, 1);
    }
  }
  // TODO: check if no error on deleting the grade.
  error.value = false;
  deleBtnPressed.value = false;
}
</script>

<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-block">
          <div
            class="modal-default-button closeButton"
            @click="$emit('close')"
          ></div>

          <div class="modal-header">
            <slot name="header"> {{ btnText }} une note </slot>
          </div>
          <div class="modal-error">
            <p class="error" v-if="error">{{ errorMsg }}</p>
          </div>
          <div class="modal-body">
            <slot name="body">
              <form @submit.prevent="addOrEditGrade()">
                <input
                  type="text"
                  v-model="name"
                  id="name"
                  maxlength="30"
                  required
                  placeholder="Nom de la note"
                />
                <br />
                <input
                  type="number"
                  v-model="grade"
                  id="grade"
                  step="0.01"
                  min="1"
                  max="6"
                  required
                  placeholder="Note obtenue"
                />
                <br />
                <input
                  type="number"
                  v-model="coefficient"
                  id="coefficient"
                  step="0.01"
                  required
                  placeholder="Pondération %"
                />
                <br />
                <select
                  id="course"
                  v-model="courseId"
                  :disabled="disabledSelect"
                  placeholder="Branche"
                  v-bind:class="btnText == 'Modifier' ? 'hideArrowSelect' : ''"
                >
                  <option value="" disabled selected>Branche</option>
                  <option
                    :value="course.courseId"
                    v-for="course in coursesArray"
                  >
                    {{ course.courseShortName }}
                  </option>
                </select>
                <br />
                <button
                  class="modal-default-button deleteButton"
                  v-show="disabledSelect"
                  @click="deleteBtnClicked"
                >
                  Supprimer
                </button>

                <button
                  class=""
                  v-bind:class="
                    btnText == 'Ajouter' ? 'addButton' : 'updateButton'
                  "
                >
                  {{ btnText }}
                </button>
              </form>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
div.modal-mask {
  position: fixed;
  z-index: 9999;
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

  max-width: 500px;
}

div.modal-block {
  max-width: 500px !important;

  margin: auto;
  padding: 0px 0px 10px 0px;

  background-color: #fff;

  border-radius: 25px 25px 0px 0px;

  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
}

div.modal-header {
  display: inline-block;

  width: 100%;

  border-bottom: none;

  padding-left: auto;

  text-align: center;

  font-family: "Outfit";
  font-style: normal;
  font-weight: 700;
  font-size: 32px;
  line-height: 36px;

  color: #0c223f;
}

.modal-body {
  margin: 0 !important;
}

.modal-body form {
  width: 250px;
  margin: auto;
}

input {
  border: none;
  border-bottom: 1px solid #0c223f;
  width: 100px;
  margin-bottom: 40px;
}

/* Remove arrow input type number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}

input::placeholder {
  color: #0c223f;
  opacity: 0.6;

  font-family: "Inter";
  font-style: normal;
  font-weight: 400;
  font-size: 16px;
  line-height: 22px;
}

select {
  -webkit-tap-highlight-color: transparent;

  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;

  background-image: url("data:image/svg+xml,%3Csvg width='24' height='26' viewBox='0 0 24 26' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 9.75L12 16.25L18 9.75' stroke='%230C223F' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
  background-size: 25px;
  background-position: calc(100% + 0.1rem);
  background-repeat: no-repeat;

  width: 250px;

  border: none;
  border-radius: 0%;
  border-bottom: 1px solid #0c223f;

  color: #0c223f;
  opacity: 0.6;

  font-family: "Inter";
  font-style: normal;
  font-weight: 400;
  font-size: 16px;
  line-height: 22px;
}

.hideArrowSelect {
  background-image: none;
}

.addButton {
  margin: 50px auto 0 auto;

  align-items: center;

  width: 250px;
  height: 45px;

  background: #f84e35;
  border-radius: 40px;

  font-family: "Outfit";
  font-style: normal;
  font-weight: 600;
  font-size: 20px;
  line-height: 25px;

  color: #ffffff;

  border: none;
}

.updateButton {
  margin: auto auto 0 0;
  display: inline;
  justify-content: center;
  align-items: center;
  gap: 10px;
  width: 160px;
  height: 45px;
  background: #f84e35;
  border-radius: 40px;
  font-family: "Outfit";
  font-style: normal;
  font-weight: 600;
  font-size: 20px;
  line-height: 25px;
  color: #ffffff;
  border: none;

  position: absolute;
  right: 10%;

  margin-top: 20px;
}

.deleteButton {
  display: inline;
  border: none;
  padding: 0;
  background-color: inherit;
  font-size: 16px;
  color: #f84e35;
  cursor: pointer;

  height: 45px;

  margin-top: 20px;
}

.closeButton {
  height: 45px;
  width: 45px;

  margin: 10px 10px auto auto;

  background-image: url("data:image/svg+xml,%3Csvg width='34' height='36' viewBox='0 0 34 36' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11.3205 25.6075C10.9392 25.6075 10.5579 25.4587 10.2568 25.14C9.67481 24.5237 9.67481 23.5037 10.2568 22.8875L21.6161 10.86C22.1981 10.2437 23.1615 10.2437 23.7435 10.86C24.3255 11.4762 24.3255 12.4962 23.7435 13.1125L12.3842 25.14C12.1032 25.4587 11.7018 25.6075 11.3205 25.6075Z' fill='%230C223F'/%3E%3Cpath d='M22.6798 25.6075C22.2985 25.6075 21.9172 25.4587 21.6161 25.14L10.2568 13.1125C9.67481 12.4962 9.67481 11.4762 10.2568 10.86C10.8388 10.2437 11.8022 10.2437 12.3842 10.86L23.7435 22.8875C24.3255 23.5037 24.3255 24.5237 23.7435 25.14C23.4424 25.4587 23.0611 25.6075 22.6798 25.6075Z' fill='%230C223F'/%3E%3C/svg%3E%0A");
  background-repeat: no-repeat;
  background-position: bottom;
  background-size: 40px;
}

div.modal-footer {
  border-top: none;
  width: 100%;
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