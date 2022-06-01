<script setup>
  import { computed, ref } from 'vue';

  const props = defineProps({
    modelValue: {
      type: Number, String,
      required: true
    },
    typeInput: {
      type: String,
      required: false,
      default: 'text'
    },
    label: {
      type: String,
      required: true,
      default: ''
    }
  })

  const emits = defineEmits([
    'update:modelValue'
  ]);

  const value = computed({
    get: () => props.modelValue,
    set: val => emits('update:modelValue', val)
  });

 /*
    :value="modelValue"
    @input="modelValue = $event.target.value"
  */
</script>

<template>

<label> {{ props.label }} </label>
  <input
    :type="typeInput"
    v-model="value"
  >
  <span> {{ value }}</span>
</template>

<style scoped>

</style>