<script setup>
    import { page } from '../state.js';
    import { ref, computed, watchEffect } from 'vue';
    import { useFetch } from '../composables/fetch.js';
    import BaseInput from '../components/BaseInput.vue';


    const {data: students} = useFetch('https://chabloz.eu/files/horaires/students.json');

    const {data: groups} = useFetch('/api/groups/');
    // const props = defineProps({
    //     showRegister: {
    //         type: Boolean,
    //         required: true,
    //         default: true
    //     }
    // });

    const email = ref('');
    const firstName = ref(null);
    const lastName = ref(null);
    const groupId = ref(null);
    const promotion = ref(null);
    const groupPin = ref(null);
    const suggestion = computed(() => student.email);

    const student = computed(() => {
            const res = students.value.filter(student => student.email.match(email.value.toLowerCase()));
            if (res.length == 1) {
                const sdt = res[0];
                firstName.value = sdt.name.split(' ')[1];
                lastName.value = sdt.name.split(' ')[0];
                return sdt;
            } else {
                firstName.value = '';
                lastName.value = '';
                return {email: ''};
            }
        });
	// .then(res => res.json())
	// .then(students => data.value = students );

    const regexEmail = /^[\w\-\.]+@heig-vd.ch$/i;
//   watchEffect(() => {
//       console.log(students);
//   });
// const showRegister = ref(true);
// console.log(showRegister);
const firstnameError = ref(false);

function register(event) {
    const data = {
        email: email.value,
        firstName: firstName.value,
        lastName: lastName.value,
        groupId: groupId.value,
        groupPin: groupPin.value
    };
    console.log(data)
    // const {data: testApi} = useFetch('/api/register/validator');

    fetch('/api/register/validator', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }).then (res => res.json())
    .then (errors => {
        if (errors?.firstname){
            firstnameError.value = true;
        }
        console.log(errors.firstname)
    });
}



</script>

<template>
    <!-- <div v-show="showRegister"> -->
        <h1 v-show="page === '#register'">
        </h1>
        <!-- Button that close the modal -->
        <button class="close" @click="showRegister = false">&times;</button>
        <p>Problème: si je ne met pas la variable student ici, les données ne se mettent pas à jour:</p>
        <p>{{testApi }}</p>
        <form @submit.prevent="register">
            <base-input 
            v-model="email" 
            type="email"
            label="Entrez votre adresse mail de l'école" /><br>
            <button v-show="student.email" @click="email = student.email">
                {{student.email}}
            </button>
            <br>
            <base-input 
            v-model="firstName" 
            type="text"
            label="Prénom" />
            <p v-show="firstnameError">Erreur dans votre nom</p>
            <br>
            <base-input 
            v-model="lastName" 
            type="text"
            label="nom" />
            <br>
            <select v-model="groupId">
                <option v-for="group in groups" :value="group.id">{{group.promotion.name}}-{{group.name}} </option>
            </select>
            <br>
            <base-input 
            v-model="groupPin" 
            type="text"
            label="Mot de passe de classe" />
            <input type="submit" value="S'inscrire"  >
        </form>
        <!-- <input type="email" name="email" id="email" v-model="email"> -->
    <!-- </div> -->
</template>

<style scoped>

</style>