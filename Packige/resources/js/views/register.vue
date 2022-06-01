<script setup>
    import { page } from '../state.js';
    import { ref, computed, watchEffect } from 'vue';
    import { useFetch } from '../composables/fetch.js';
    import BaseInput from '../components/BaseInput.vue';


    const {data: students} = useFetch('https://chabloz.eu/files/horaires/students.json');


    const email = ref('');
    const firstName = ref(null);
    const lastName = ref(null);
    const promotion = ref(null);

    const student = computed(() => {
            const res = students.value.filter(student => student.email.match(email.value))
            if (res.length == 1) {
                const sdt = res[0];
                firstName.value = sdt.name.split(' ')[1];
                lastName.value = sdt.name.split(' ')[0];
                return sdt;
            } else {
                firstName.value = '';
                lastName.value = '';
                return null;
            }
        });

    const regexEmail = /^[\w\-\.]+@heig-vd.ch$/i;
//   watchEffect(() => {
//       console.log(students);
//   });


</script>

<template>
    <h1 v-show="page === '#home'">
    </h1>
    <p>{{student}}</p>
    <base-input 
    v-model="email" 
    type="email"
    label="Entrez votre adresse mail de l'école" />

    <base-input 
    v-model="firstName" 
    type="text"
    label="Prénom" />

    <base-input 
    v-model="lastName" 
    type="text"
    label="nom" />
    <!-- <input type="email" name="email" id="email" v-model="email"> -->

</template>

<style scoped>

</style>