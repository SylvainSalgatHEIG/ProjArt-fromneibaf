<script setup>
import { useFetch, usePost } from "../composables/fetch";
import { computed, ref, watch, watchEffect } from "vue";
import { deadlines } from "../stores/deadlines";
const { data: coursesArray } = useFetch("/api/courses/");

// let btnText = ref("Ajouter");
// let coefficient = ref("");
// let grade = ref("");
// let course = ref("");
// let disabledSelect = ref(false);
// let deleBtnPressed = ref(false);

// watchEffect(() => {
//   if (grades.value != null && coursesArray.value != null) {
//     let pass = false;
//     for (const courseData of coursesArray.value) {
//       for (const gradeData of grades.value[courseData.moduleName][
//         courseData.courseName
//       ].grades) {
//         if (gradeData.id === props.id) {
//           console.log(gradeData);
//           coefficient.value = gradeData.coefficient;
//           grade.value = gradeData.grade;
//           course.value = courseData.courseShortName;
//           btnText.value = "Modifier";
//           disabledSelect.value = true;
//           pass = true;
//         }
//       }
//     }
//     if (!pass) {
//       coefficient.value = "";
//       grade.value = "";
//       course.value = "";
//       disabledSelect.value = false;
//       btnText.value = "Ajouter";
//     }
//   }
// });

// function deleteBtnClicked() {
//   deleBtnPressed.value = true;
// }

// function addOrEditGrade(id = props.id) {
//   let moduleName = "";
//   let courseFullName = "";

//   for (const courseData of coursesArray.value) {
//     if (courseData.courseShortName === course.value) {
//       courseFullName = courseData.courseName;
//       moduleName = courseData.moduleName;
//     }
//   }
//   if (id && deleBtnPressed.value) {
//     console.log("delete");
//     const data = {
//       id: id,
//     };
//     deleteGrade(data, courseFullName, moduleName)
//   } else if (id) {
//     console.log("edit");
//     const data = {
//       grade: grade.value,
//       coefficient: coefficient.value,
//       course: course.value,
//       id: id,
//     };
//     editGrade(data, courseFullName, moduleName);
//   } else {
//     console.log("add");
//     const data = {
//       grade: grade.value,
//       coefficient: coefficient.value,
//       course: course.value,
//     };
//     addGrade(data, courseFullName, moduleName);
//   }
//   emit("close");
// }

// function editGrade(data, courseFullName, moduleName) {
//   usePost({ url: "/api/grades/edit", data: data });

//   for (const gradeData of grades.value[moduleName][courseFullName].grades) {
//     if (gradeData.id === data.id) {
//       gradeData.grade = grade.value;
//       gradeData.coefficient = coefficient.value;
//     }
//   }
// }

// function addGrade(data, courseFullName, moduleName) {
//   usePost({ url: "/api/grades/add", data: data });

//   // Faire un test si la note à été ajoutée à la base
//   grades.value[moduleName][courseFullName].grades.push({
//     grade: grade.value,
//     coefficient: coefficient.value,
//   });
// }

// function deleteGrade(data, courseFullName, moduleName) {
//   // Supprimer la note de la base
//   usePost({ url: "/api/grades/delete", data: data });
//   // Supprimer la note du tableau
//   for(let i = 0; i < grades.value[moduleName][courseFullName].grades.length; i++){
//     if(grades.value[moduleName][courseFullName].grades[i].id == data.id){
//       grades.value[moduleName][courseFullName].grades.splice(i, 1)
//     }
//   }

//   deleBtnPressed.value = false;
// }

const emit = defineEmits(["close"]);
const props = defineProps({
  deadlineId: {},
  groupId: {},
});

let name = ref("");
let description = ref("");
let type = ref("rendu");
let date = ref("");
let startTime = ref("");
let endTime = ref("");
let course = ref("");

let endTimeComputed = computed(() => {
  if (!displayEndTime.value) {
    return startTime.value;
  }
  return endTime.value;
});
let displayEndTime = computed(() => {
  if (type.value == "examen") {
    return true;
  }
  endTime.value = "";
  return false;
});
let startTimeText = computed(() => {
  if (displayEndTime.value) {
    return "Heure de début";
  }
  return "Heure de rendu";
});

function addOrEditDeadline() {
  // let moduleName = "";
  // let courseFullName = "";

  // for (const courseData of coursesArray.value) {
  //   if (courseData.courseShortName === course.value) {
  //     courseFullName = courseData.courseName;
  //     moduleName = courseData.moduleName;
  //   }
  // }
  // if (id && deleBtnPressed.value) {
  //   console.log("delete");
  //   const data = {
  //     id: id,
  //   };
  //   deleteGrade(data, courseFullName, moduleName);
  // } else if (id) {
  //   console.log("edit");
  //   const data = {
  //     grade: grade.value,
  //     coefficient: coefficient.value,
  //     course: course.value,
  //     id: id,
  //   };
  //   editGrade(data, courseFullName, moduleName);
  // } else {
  //   console.log("add");
  //   const data = {
  //     grade: grade.value,
  //     coefficient: coefficient.value,
  //     course: course.value,
  //   };
  //   addGrade(data, courseFullName, moduleName);
  // }
  // emit("close");
  // console.log(props.groupId);

  const data = {
    name: name.value,
    description: description.value,
    type: type.value,
    date: date.value,
    startTime: startTime.value,
    endTime: endTimeComputed.value,
    course: course.value,
    groupId: props.groupId,
  };
  addTask(data);
}

function addTask(data) {
  console.log(deadlines.value);
  const { results: newDeadLineId } = usePost({
    data: data,
    url: "/api/deadlines/add",
  });
  let added = false;
  watchEffect(() => {
    if (newDeadLineId.value != null && !added) {
      deadlines.value.push({
        type: type.value,
        description: description.value,
        name: name.value,
        start_date: date.value + " " + startTime.value,
        end_date: date.value + " " + endTimeComputed.value,
        check: [
          {
            isChecked: 0,
          },
        ],
        group_id: props.groupId,
        id: newDeadLineId.value,
      });
      added = true;
    }
  });
}
</script>

<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-block">
          <div class="modal-default-button closeButton" @click="$emit('close')"></div>

          <div class="modal-header">
            <slot name="header"> Ajouter une tâche </slot>
          </div>
          <div class="modal-body">
            <slot name="body">
              <form @submit.prevent="addOrEditDeadline()">
                <br />
                <input type="text" v-model="name" id="name" required placeholder="Nom de la tâche" />
              

                <select id="course" v-model="course">
                  <!-- <select id="course" v-model="course" :disabled="disabledSelect"> -->
                  <option
                    :value="course.courseShortName"
                    v-for="course in coursesArray"
                  >
                    {{ course.courseShortName }}
                  </option>
                </select>
                <br />

                <textarea
                  v-model="description"
                  id="description"
                  rows="6"
                  cols="25"
                  required
                  placeholder="Décris la tâche..."
                ></textarea>

                <br />
                <span class="checkmark"></span>
                <label for="deadline" class="labelRadio">Rendu</label>
                <input
                  type="radio"
                  id="deadline"
                  name="type"
                  value="rendu"
                  v-model="type"
                />
                <span class="checkmark"></span>
                <label for="exam" class="labelRadio">Examen</label>
                <input
                  type="radio"
                  id="exam"
                  name="type"
                  value="examen"
                  v-model="type"
                />
                
                <br />
                <label for="date">Date</label>
                <input
                  type="date"
                  name="date"
                  id="date"
                  v-model="date"
                  value=""
                  required
                />

                <br />
                <label for="startTime">{{ startTimeText }}</label>
                <input
                  type="time"
                  name="startTime"
                  id="startTime"
                  v-model="startTime"
                />
                <label for="endTime" v-if="displayEndTime">Heure de Fin</label>
                <input
                  type="time"
                  name="endTime"
                  id="endTime"
                  v-model="endTime"
                  v-if="displayEndTime"
                  required
                />
                <br />
                <button class="modal-default-button addButton">
                  <!-- {{ btnText }} -->
                  Ajouter
                </button>
                <!-- <button
                  class="modal-default-button"
                  v-show="disabledSelect"
                  @click="deleteBtnClicked"
                >
                  del
                </button> -->
              </form>
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

  font-family: 'Inter';
  font-style: normal;
  font-weight: 400;
  font-size: 16px;
  line-height: 22px;
}

div.modal-header {

  display: inline-block;

  width: 100%;

  border-bottom: none;

  padding-left: 0 auto;

  text-align: center;

  font-family: 'Outfit';
  font-style: normal;
  font-weight: 700;
  font-size: 32px;
  line-height: 36px;

  color: #0C223F;
}

.modal-body {
  margin: 0 !important;
  padding-top: 0;
}

.modal-body form {
  width: 250px;
  margin: auto;
}

input {
  border: none;
  border-bottom: 1px solid #0C223F;
  width: 100px;
  margin-bottom: 15px;
}

input#name {
  width: 100%;
}

input#description {
  width: 250px;
  border-radius: 50px;
}

input#course {
  width: 125px;
}

input#date {
  border: none;
  float: right;
  margin-bottom: 0;
}


/*
input#date {
  height: 36px;
  width: 36px;
  border-radius: 50%;
  background-color: rebeccapurple;

}
*/

/* Remove arrow input type number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number] {
  -moz-appearance: textfield;
}

input::placeholder {
  color: #0C223F;
  opacity: 0.6;

  font-family: 'Inter';
  font-style: normal;
  font-weight: 400;
  font-size: 14px;
  line-height: 22px;
}

select {
  -webkit-tap-highlight-color: transparent;
  
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;

  margin-bottom: 15px;


  background-image: url("data:image/svg+xml,%3Csvg width='24' height='26' viewBox='0 0 24 26' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 9.75L12 16.25L18 9.75' stroke='%230C223F' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
  background-size: 25px;
  background-position: calc(100% + .1rem);
  background-repeat: no-repeat;

  width: 50%;

  border: none;
  border-radius: 0%;
  border-bottom: 1px solid #0C223F;

  color: #0C223F;
  opacity: 0.6;

  font-family: 'Inter';
  font-style: normal;
  font-weight: 400;
  font-size: 14px;
  line-height: 22px;

}


textarea {
  margin-bottom: 15px;
  background: rgba(12, 34, 63, 0.1);
  border: 1px solid transparent;
  border-radius: 6px; 
  color: #0C223F;
  padding: 4px;
  max-width: 250px;
  max-height: 100px;
}

input#startTime {
  float: right;
  margin-top: 5px;
  width: 60px;

  padding: 2px;

  border: 2px #77B0C5 solid;
  border-radius: 34px;

  margin-bottom: 10px;
}

label[for=startTime] {
  margin-top: 15px;
  width: 120px;
}

input#endTime {
  float: right;
  margin-top: 5px;
  width: 60px;

  padding: 2px;

  border: 2px #77B0C5 solid;
  border-radius: 34px;
}

label[for=endTime] {
  margin-top: 15px;
  width: 120px;
}


input[type="radio"] {
  height: 25px;
  width: 25px;

  appearance: none;
  background-color: #fff;
  margin: 0;
  color: #0C223F;
  border: 0.15em solid #C8D7F4;
  border-radius: 50%;

  margin: 0 0px -6px 15px;
}

input[type="radio"]:first {
  margin-right: 35px;
}

input[type="radio"]:nth-of-type(2n) {
  margin-right: 35px;
}

input[type="radio"]:checked {
  border: 2px solid #77B0C5;
  background-color: #77B0C5;
  box-shadow:0px 0px 0px 4px #FFF inset;
}


.container input:checked .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark {
  width: 12px;
}


label {
  color: #0C223F;

  font-family: 'Inter';
  font-style: normal;
  font-weight: 400;
  font-size: 16px;
  line-height: 22px;

  margin-bottom: 15px;
}

label.labelRadio {
  padding-bottom: 2px;
  margin-bottom: 35px;
}




.addButton {

  margin: 50px auto 0 auto;

  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  gap: 10px;

  width: 290px;
  height: 45px;

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


  height: 35px;
  width: 35px;

  margin: 10px 10px auto auto;

  background-image: url("data:image/svg+xml,%3Csvg width='34' height='36' viewBox='0 0 34 36' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11.3205 25.6075C10.9392 25.6075 10.5579 25.4587 10.2568 25.14C9.67481 24.5237 9.67481 23.5037 10.2568 22.8875L21.6161 10.86C22.1981 10.2437 23.1615 10.2437 23.7435 10.86C24.3255 11.4762 24.3255 12.4962 23.7435 13.1125L12.3842 25.14C12.1032 25.4587 11.7018 25.6075 11.3205 25.6075Z' fill='%230C223F'/%3E%3Cpath d='M22.6798 25.6075C22.2985 25.6075 21.9172 25.4587 21.6161 25.14L10.2568 13.1125C9.67481 12.4962 9.67481 11.4762 10.2568 10.86C10.8388 10.2437 11.8022 10.2437 12.3842 10.86L23.7435 22.8875C24.3255 23.5037 24.3255 24.5237 23.7435 25.14C23.4424 25.4587 23.0611 25.6075 22.6798 25.6075Z' fill='%230C223F'/%3E%3C/svg%3E%0A");
  background-repeat: no-repeat;
  
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