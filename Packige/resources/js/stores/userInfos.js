import { useFetch } from '../composables/fetch.js';

export const { data: userInfos } = useFetch('/api/user/get');