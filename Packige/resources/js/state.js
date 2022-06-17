import { ref } from "vue";
import {useLocalstorage} from './composables/localstorage.js';

export const { value: tasks } = useLocalstorage('tasks', []);

export const page = ref('#home');

// export function change(newPage) {
//   page.value = newPage;
// }

// export function get() {
//   return page.value
// }