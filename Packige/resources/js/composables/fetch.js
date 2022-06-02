import { ref, unref } from "vue";

export function useFetch(url) {
  const data = ref(null);

  async function fetchJson() {
    const res = await fetch(url);
    const json = await res.json();
    data.value = json;
  }

  fetchJson();

  return { data };

}

export function usePost({url, method = 'POST', data}) {
  const results = ref(null);

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
    results.value = json;
  }

  fetchJsonPost(data);
  return { results, fetchJsonPost };
}