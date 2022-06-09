import { ref } from 'vue';
import { useFetch } from '../composables/fetch.js';

export const { data: deadlines } = useFetch('/api/deadlines/get');