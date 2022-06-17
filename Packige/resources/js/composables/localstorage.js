import { ref, watch } from "vue";

export function useLocalstorage(key, defaultVal = null) {

  let data = localStorage.getItem(key);

  // if allready in localstorage
  if (data !== null) {
    // Deserialize JSON
    data = JSON.parse(data);
  } else {
    // Serialize in JSON
    localStorage.setItem(key, JSON.stringify(defaultVal));
    data = defaultVal;
  }

  const value = ref(data);

  watch(value, () => {
    localStorage.setItem(key, JSON.stringify(value.value));
  }, {deep: true});

  return { value };
}
