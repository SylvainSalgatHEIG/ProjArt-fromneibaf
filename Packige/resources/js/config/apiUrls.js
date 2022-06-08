let BASE_URL = "";
if (window.location.hostname == 'localhost' || window.location.hostname == '127.0.0.1') {
    BASE_URL = "http://" + window.location.host + "/";
} else {
    BASE_URL = "https://" + window.location.hostname + "/";
}
export const apiCourses = BASE_URL + 'api/courses';
export const apiGroups = BASE_URL + 'api/groups';
export const apiEvents = BASE_URL + 'api/events';
export const apiUserLinks = BASE_URL + 'api/user/links';
export const apiUserLinkAdd = BASE_URL + 'api/user/link/add';
export const apiUserLinkEdit = BASE_URL + 'api/user/link/edit';
export const apiUserLinkDelete = BASE_URL + 'api/user/link/delete';

const CHABLOZ_URL = "https://chabloz.eu/";
export const apiSchedules = CHABLOZ_URL + 'files/horaires/all.json';
