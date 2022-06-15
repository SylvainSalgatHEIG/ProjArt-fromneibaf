import { apiUserLinks } from "../config/apiUrls.js";
import { useFetch } from '../composables/fetch.js';
export const { data: userLinks } = useFetch(apiUserLinks);