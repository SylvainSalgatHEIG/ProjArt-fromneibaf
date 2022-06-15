<script setup>
import { useFetch, usePost } from "../composables/fetch";
import { computed, ref, watch, watchEffect } from "vue";
import { deadlines } from "../stores/deadlines";
const { data: coursesArray } = useFetch("/api/courses/");

let btnText = ref("Ajouter");
let disabledSelect = ref(false);
let deleBtnPressed = ref(false);
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

watchEffect(() => {
  if (deadlines.value != null) {
    let pass = false;
    for (const deadlineData of deadlines.value) {
      if (deadlineData.id === props.deadlineId) {
        name.value = deadlineData.name;
        description.value = deadlineData.description;
        type.value = deadlineData.type;
        course.value = deadlineData.course[0].shortname;
        date.value = deadlineData.start_date.split(" ")[0];
        startTime.value = deadlineData.start_date.split(" ")[1];
        endTime.value = deadlineData.end_date.split(" ")[1];
        btnText.value = "Modifier";
        disabledSelect.value = true;
        pass = true;
      }
    }
    if (!pass) {
      name.value = "";
      description.value = "";
	  course.value = "";
      type.value = "rendu";
      date.value = "";
      startTime.value = "";
      endTime.value = "";
      disabledSelect.value = false;
      btnText.value = "Ajouter";
    }
  }
});

function deleteBtnClicked() {
  deleBtnPressed.value = true;
}

function addOrEditDeadline(id = props.deadlineId) {
  if (id && deleBtnPressed.value) {
    console.log("delete");
    const data = {
      id: id,
    };
    deleteDeadline(data);
  } else if (id) {
    console.log("edit");
    const data = {
      name: name.value,
      description: description.value,
      date: date.value,
      startTime: startTime.value,
      endTime: endTimeComputed.value,
      groupId: props.groupId,
      deadlineId: id,
    };
    editDeadline(data);
  } else {
    console.log("add");
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
    addDeadline(data);
  }
  emit("close");
}

function addDeadline(data) {
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
		course: [
			{shortname : course.value}
		]
      });
      added = true;
    }
  });
}

function editDeadline(data) {
  const { results: editedDeadLineId } = usePost({
    data: data,
    url: "/api/deadlines/edit",
  });

  let edited = false;
  watchEffect(() => {
    if (editedDeadLineId.value != null && !edited) {
      for (const deadline of deadlines.value) {
        if (deadline.id == editedDeadLineId.value) {
          deadline.name = data.name;
          deadline.description = data.description;
          deadline.start_date = data.date + " " + data.startTime;
          deadline.end_date = data.date + " " + data.endTime;
        }
      }

      edited = true;
    }
  });
}

function deleteDeadline(data, courseFullName, moduleName) {
  // Supprimer la note de la base
  const { results: deletedDeadLineId } = usePost({
    data: data,
    url: "/api/deadlines/delete",
  });
  // Supprimer la note du tableau
  let deleted = false;
  watchEffect(() => {
    if (deletedDeadLineId.value != null && !deleted) {
		console.log(deadlines.value.length)
		for(let i = 0; i < deadlines.value.length; i ++){
			if(deadlines.value[i].id == deletedDeadLineId.value){
				deadlines.value.splice(i, 1);
			}
		}
      deleted = true;
    }
  })

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
            <slot name="header"> {{ btnText }} une tâche </slot>
          </div>
          <div class="modal-body">
            <slot name="body">
              <form @submit.prevent="addOrEditDeadline()">
                <input
                  type="text"
                  v-model="name"
                  id="name"
                  maxlength="12"
                  required
                  placeholder="Nom de la tâche"
                />
                <br />
                <!-- <select id="course" v-model="course"> -->
                <select id="course" v-model="course" :disabled="disabledSelect" required v-bind:class="btnText == 'Modifier' ? 'hideArrowSelect' : ''">
                  <option value="" disabled selected>Branche</option>
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

                <label :class="btnText == 'Ajouter' ? '' : 'selectInactive'" for="deadline">Rendu</label>
                <input
                  type="radio"
                  id="deadline"
                  name="type"
                  value="rendu"
                  v-model="type"
                  :disabled="disabledSelect"
                />
                <label :class="btnText == 'Ajouter' ? '' : 'selectInactive'" for="exam">Examen</label>
                <input
                  type="radio"
                  id="exam"
                  name="type"
                  value="examen"
                  v-model="type"
                  :disabled="disabledSelect"
                />

                <br />
                <label for="date">Date</label>
                <input
                  type="date"
                  name="date"
                  id="date"
                  v-model="date"
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
                <br />
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

                <button
                  class="modal-default-button deleteButton"
                  v-show="disabledSelect"
                  @click="deleteBtnClicked"
                >
                  Supprimer
                </button>

                <button class="modal-default-button" :class="btnText == 'Ajouter' ? 'addButton' : 'updateButton'">
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
  animation: enterSlide 0.5s ease 0s 1 normal forwards;
}

@keyframes enterSlide {
	0% {
		opacity: 0;
		transform: translateY(250px);
	}

	100% {
		opacity: 1;
		transform: translateY(0);
	}
}

@keyframes exitSlide {
	0% {
		opacity: 1;
		transform: translateY(0);
	}

	100% {
		opacity: 0;
		transform: translateY(250px);
	}
}

div.modal-block {
  max-width: 500px !important;
  margin: auto;
  padding: 0px 0px 10px 0px;
  background-color: #fff;
  border-radius: 25px 25px 0px 0px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
  font-family: "Inter";
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
  font-family: "Outfit";
  font-style: normal;
  font-weight: 700;
  font-size: 32px;
  line-height: 36px;
  color: #0c223f;
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
  border-bottom: 1px solid #0c223f;
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
  width: 120px;
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

input {
  -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

select {
  -webkit-tap-highlight-color: transparent;

  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  margin-bottom: 15px;
  background-image: url("data:image/svg+xml,%3Csvg width='24' height='26' viewBox='0 0 24 26' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 9.75L12 16.25L18 9.75' stroke='%230C223F' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
  background-size: 25px;
  background-position: calc(100% + 0.1rem);
  background-repeat: no-repeat;
  width: 50%;
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

textarea {
  margin-bottom: 15px;
  background: rgba(12, 34, 63, 0.1);
  border: 1px solid transparent;
  border-radius: 6px;
  color: #0c223f;
  padding: 4px;
  max-width: 250px;
  max-height: 100px;
}
input#startTime {
  float: right;
  margin-top: 5px;
  width: 70px;
  padding: 2px;
  border: 2px #77b0c5 solid;
  border-radius: 34px;
  margin-bottom: 10px;
}
label[for="startTime"] {
  margin-top: 15px;
  width: 120px;
}
input#endTime {
  float: right;
  margin-top: 5px;
  width: 70px;
  padding: 2px;
  border: 2px #77b0c5 solid;
  border-radius: 34px;
}
label[for="endTime"] {
  margin-top: 15px;
  width: 120px;
}
input[type="radio"] {
  height: 25px;
  width: 25px;
  appearance: none;
  background-color: #fff;
  margin: 0;
  color: #0c223f;
  border: 0.15em solid #c8d7f4;
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
  border: 2px solid #77b0c5;
  background-color: #77b0c5;
  box-shadow: 0px 0px 0px 4px #fff inset;
}
.container input:checked .checkmark {
  background-color: #2196f3;
}
/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark {
  width: 12px;
}
label {
  color: #0c223f;
  font-family: "Inter";
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
  right: 16px;

  margin-top: 20px;
}

.deleteButton {
  display: inline;
  border: none;
  padding: 0;
  background-color: inherit;
  font-size: 16px;
  color: #F84E35;
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
.modal-leave-active .modal-wrapper {
  animation: exitSlide 0.5s ease 0s 1 normal forwards;
  opacity: 0;
}

.selectInactive {
  color: rgba(12, 34, 63, 0.6);
}
</style>