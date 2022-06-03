<script setup>
import { useFetch, usePost } from "../composables/fetch";
import { ref } from "vue";

const { data: coursesArray } = useFetch("/api/courses/");

const coefficient = ref("");
const grade = ref("");
const course = ref("");

const emits = defineEmits(["newGrade"]);

function addGrade() {
  //   console.log(grade.value);
  //   console.log(coefficient.value);
  //   console.log(course.value);
  const data = {
    grade: grade.value,
    coefficient: coefficient.value,
    course: course.value,
  };
  console.log(data);
  usePost({ url: "/api/grades/add", data: data });
  emits("newGrade", data);
}
</script>

<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <slot name="header"> Ajouter une note </slot>
          </div>

          <div class="modal-body">
            <slot name="body">
              <form @submit.prevent="addGrade()">
                <label for="grade">Note :</label><br />
                <input type="number" v-model="grade" id="grade" step="0.01" />
                <label for="coefficient">Coefficient :</label><br />
                <input
                  type="number"
                  v-model="coefficient"
                  id="coefficient"
                  step="0.01"
                />
                <label for="course">Cours :</label><br />
                <select id="course" v-model="course">
                  <option :value="course.name" v-for="course in coursesArray">
                    {{ course.shortname }}
                  </option>
                </select>
                <br />
                <button class="modal-default-button">Ajouter</button>
                <!-- <button class="modal-default-button" @click="$emit('newGrade', data)">Ajouter</button> -->
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