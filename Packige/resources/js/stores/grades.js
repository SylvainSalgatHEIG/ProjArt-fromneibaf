import { ref } from 'vue';
import { useFetch } from '../composables/fetch.js';

export const { data: grades } = useFetch('/api/grades/get');