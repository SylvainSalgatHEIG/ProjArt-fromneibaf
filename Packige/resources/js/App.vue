<script setup>
import {ref} from 'vue';

const msg = ref('');
let data = ref();

fetch('/api/students/')
	.then(res => res.json())
	.then(students => data.value = students );
</script>

<template>
	Hello vue 3
</template>