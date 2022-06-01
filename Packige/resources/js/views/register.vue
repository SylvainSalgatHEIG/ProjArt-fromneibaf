<script setup>
    import { page } from '../state.js';
    import { ref, computed, watchEffect } from 'vue';
    import { useFetch } from '../composables/fetch.js';
    import BaseInput from '../components/BaseInput.vue';


    const {data: students} = useFetch('https://chabloz.eu/files/horaires/students.json');

    const {data: groups} = useFetch('/api/groups/');
    

    const email = ref('');
    const firstName = ref(null);
    const lastName = ref(null);
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


</script>

<template>
    <h1 v-show="page === '#home'">
    </h1>
    <p>Problème: si je ne met pas la variable student ici, les données ne se mettent pas à jour:</p>
    <p>{{student.email}}</p>
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
    <br>
    <base-input 
    v-model="lastName" 
    type="text"
    label="nom" />
    <br>
    <select>
        <option v-for="group in groups" :value="group.id">{{group.promotion.name}}-{{group.name}} </option>
    </select>
    <br>
    <base-input 
    v-model="groupPin" 
    type="text"
    label="Mot de passe de classe" />
    <!-- <input type="email" name="email" id="email" v-model="email"> -->

</template>

<style scoped>

</style>