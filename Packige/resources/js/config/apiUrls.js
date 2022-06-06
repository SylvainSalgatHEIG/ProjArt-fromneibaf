let BASE_URL = "";
if (window.location.hostname == 'localhost') {
    BASE_URL = 'http://localhost:8000/';
} else {
    BASE_URL = "https://" + window.location.hostname + "/";
}
export const apiCourses = BASE_URL + 'api/courses';
export const apiGroups = BASE_URL + 'api/groups';

const CHABLOZ_URL = "https://chabloz.eu/";
export const apiSchedules = CHABLOZ_URL + 'files/horaires/all.json';
