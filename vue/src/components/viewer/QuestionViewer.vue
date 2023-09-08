<template>
  <fieldset class="mb-4">
    <div>
      <legend class="text-gray-900 text-base font-medium">
        {{ index + 1 }}. {{ question.question }}
      </legend>
      <p class="text-sm text-gray-500">
        {{ question.description }}
      </p>
    </div>

    <div class="mt-3">
      <div v-if="question.type === 'select'">
        <select
          class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white sm:text-sm rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
          :value="modelValue"
          @change="emits('update:modelValue', $event.target.value)"
        >
          <option value="">Please Select</option>
          <option
            v-for="option in question.data.options"
            :key="option.uuid"
            :value="option.text"
          >
            {{ option.text }}
          </option>
        </select>
      </div>
      <div v-else-if="question.type === 'checkbox'">
        <div
          v-for="(option, ind) in question.data.options"
          :key="option.uuid"
          class="flex items-center"
        >
          <input
            type="checkbox"
            class="h-4 w-4 focus:ring-indigo-500 rounded border-gray-300 text-indigo-600"
            v-model="model[option.text]"
            :id="option.uuid"
            @change="onCheckboxChange"
          />
          <label :for="option.uuid" class="ml-3 block text-sm font-medium text-gray-700">
            {{ option.text }}
          </label>
        </div>
      </div>
      <div v-else-if="question.type === 'radio'">
        <div
          v-for="(option, ind) in question.data.options"
          :key="option.uuid"
          class="flex items-center"
        >
          <input
            type="radio"
            class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
            :id="option.uuid"
            :value="option.text"
            @change="emits('update:modelValue', $event.target.value)"
            :name="'question' + question.id"
          />
          <label :for="option.uuid" class="ml-3 block text-sm font-medium text-gray-700">
            {{ option.text }}
          </label>
        </div>
      </div>
      <div v-else-if="question.type === 'text'">
        <input
          type="text"
          :value="modelValue"
          @input="emits('update:modelValue', $event.target.value)"
          class="rounded-md w-full sm:text-sm shadow-sm block focus:border-indigo-500 mt-1 focus:ring-indigo-500 border-gray-300"
        />
      </div>
      <div v-else-if="question.type === 'textarea'">
        <textarea
          :value="modelValue"
          @input="emits('update:modelValue', $event.target.value)"
          class="rounded-md w-full sm:text-sm shadow-sm block focus:border-indigo-500 mt-1 focus:ring-indigo-500 border-gray-300"
        ></textarea>
      </div>
    </div>
  </fieldset>

  <hr class="mb-4" />
</template>

<script setup>
import { ref } from "vue";

const { question, index, modelValue } = defineProps({
  question: Object,
  index: Number,
  modelValue: [String, Array],
});

const emits = defineEmits(["update:modelValue"]);

let model;
if (question.type === "checkbox") {
  model = ref({});
}

function onCheckboxChange($event) {
  const selectedOptions = [];
  for (let text in model.value) {
    if (model.value[text]) {
      selectedOptions.push(text);
    }
    emits("update:modelValue", selectedOptions);
  }
}
</script>
