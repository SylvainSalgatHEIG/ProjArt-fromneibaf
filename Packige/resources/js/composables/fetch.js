import { ref, unref } from "vue";

export function useFetch(url) {
  const data = ref(null);

  async function fetchJson() {
    // const res = await fetch(unref(url), {
    //   credentials: 'include'
    // });
    const res = await fetch(url);
    const json = await res.json();
    data.value = json;
  }

  fetchJson();

  return { data, fetchJson };

}