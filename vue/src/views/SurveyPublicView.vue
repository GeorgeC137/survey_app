<template>
  <div class="py-8 px-5">
    <div v-if="loading" class="flex font-semibold justify-center">Loading...</div>

    <form class="container mx-auto" v-else @submit="submitSurvey">
      <div class="grid grid-cols-6 items-center">
        <div class="mr-4">
          <img :src="survey.image_preview" alt="" />
        </div>
        <div class="col-span-5">
          <h1 class="text-3xl mb-3">{{ survey.title }}</h1>
          <p class="text-gray text-sm">{{ survey.description }}</p>
        </div>
      </div>

      <div
        v-if="surveyFinished"
        class="text-white mx-auto py-8 px-6 bg-emerald-400 w-[600px]"
      >
        <div class="text-xl mb-3 font-semibold">
          Thank you for participating in this survey
        </div>

        <button
          type="submit"
          @click="submitAnotherResponse"
          class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Submit another response
        </button>
      </div>

      <div v-else>
        <hr class="my-3" />
        <div v-for="(question, index) in survey.questions" :key="question.id">
          <QuestionViewer
            v-model="answers[question.id]"
            :question="question"
            :index="index"
          />
        </div>

        <button
          type="submit"
          class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-2 focus:outline-none hover:bg-indigo-700"
        >
          Submit
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import QuestionViewer from "../components/viewer/QuestionViewer.vue";
import { computed, ref } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";

const store = useStore();
const route = useRoute();

const loading = computed(() => store.state.currentSurvey.loading);
const survey = computed(() => store.state.currentSurvey.data);
const surveyFinished = ref(false);
const answers = ref({});

store.dispatch("getSurveyBySlug", route.params.slug);

function submitSurvey(e) {
  e.preventDefault();
  console.log(JSON.stringify(answers.value, undefined, 2));
  store
    .dispatch("saveSurveyAnswer", {
      surveyId: survey.value.id,
      answers: answers.value,
    })
    .then((response) => {
      if (response.status === 201) {
        // survey saved successfully on backend
        surveyFinished.value = true;
      }
    });
}

function submitAnotherResponse() {
  answers.value = {};
  surveyFinished.value = false;
}
</script>
