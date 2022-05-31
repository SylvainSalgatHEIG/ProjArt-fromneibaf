import { ref, unref } from "vue";

export function useFetch(url) {
  const data = ref(null);

  async function fetchJson() {
    const res = await fetch(unref(url), {
      credentials: 'include'
    });
    const json = await res.json();
    data.value = json;
  }


  return { data, fetchJson };

}