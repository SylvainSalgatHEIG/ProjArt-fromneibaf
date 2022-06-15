<script setup>
import { useFetch, usePost } from "../composables/fetch";
import { computed, ref, watch, watchEffect } from "vue";
import { apiUserLinks, apiUserLinkAdd } from "../config/apiUrls.js";

const { data: links } = useFetch(apiUserLinks);

console.log(links);

const emit = defineEmits(["close"]);
const props = defineProps({
  id: {},
});

let btnText = ref("Ajouter");
let url = ref("");
let name = ref("");

let disabledSelect = ref(false);
let deleBtnPressed = ref(false);

let error = ref(false);
let errorMsg = ref('');

function deleteBtnClicked() {
  deleBtnPressed.value = true;
}

function addOrEditLink(id = props.id) {
  

    console.log("add");
    const data = {
      name: name.value,
      link: url.value,
    };
    addLink(data);

  if (!error.value) {
    emit("close");
  }
  
}


function addLink(data) {
  const { results: newLinkId } = usePost({
    url: apiUserLinkAdd,
    data: data,
  });

  let added = false;
  watchEffect(() => {
    if (typeof newLinkId.value == 'object' && !added) {
      links.value.push({
        name: name.value,
        link: url.value,
      });
      added = true;
      // actionDone.value = true;
      error.value = false;
      errorMsg.value = "";
    } else if (!added) {
      // actionDone.value = true;
      errorMsg.value = "Une erreur est survenue lors de l'ajout du lien.";
      error.value = true;
      console.error('Erreur dans l\'ajout du lien');
    }
  });
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
            <slot name="header"> {{ btnText }} un lien </slot>
          </div>
          <div class="modal-error">
            <p class="error" v-if="error">{{ errorMsg }}</p>
          </div>
          <div class="modal-body">
            <slot name="body">
              <form @submit.prevent="addOrEditLink()">
                <input
                  type="text"
                  v-model="name"
                  id="name"
                  required
                  placeholder="Nom du lien"
                />
                <br />
                <input
                  type="text"
                  v-model="url"
                  id="url"
                  required
                  placeholder="URL du lien"
                />
                
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
  width: 100%;
  margin-bottom: 40px;
}

/* Remove arrow input type number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
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

</style>