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

export function usePost({url, method = 'POST', data}) {
  const data = ref(null);

  async function fetchJsonPost(body) {
    const res = await fetch(url, {
      method: method,
      body: JSON.stringify(body),
      // credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      }
    });
    const json = await res.json();
    data.value = json;
  }

  fetchJsonPost(data);
  return { data, fetchJsonPost };
}