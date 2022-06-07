import { ref } from 'vue';
import { useFetch } from '../composables/fetch.js';

export const { data: grades } = useFetch('/api/grades/get');

// export const test = ref('2');

// console.log(test)
// console.log(test.value)