<template>
  <PageComponent title="Dashboard">
    <div v-if="loading" class="flex justify-center font-bold">Loading...</div>
    <div
      v-else
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 text-gray-700"
    >
      <div
        class="p-3 shadow-md bg-white text-center animate-fade-in-down flex flex-col order-1 lg:order-2"
        style="animation-delay: 0.1s"
      >
        <h3 class="text-2xl font-semibold">Total Surveys</h3>
        <div class="text-8xl font-semibold flex-1 flex items-center justify-center">
          {{ data.totalSurveys }}
        </div>
      </div>
      <div
        class="p-3 shadow-md bg-white text-center animate-fade-in-down flex flex-col order-2 lg:order-4"
        style="animation-delay: 0.2s"
      >
        <h3 class="text-2xl font-semibold">Total Answers</h3>
        <div class="text-8xl font-semibold flex-1 flex items-center justify-center">
          {{ data.totalAnswers }}
        </div>
      </div>
      <div
        class="p-4 shadow-md bg-white row-span-2 animate-fade-in-down order-3 lg:order-1"
      >
        <h3 class="text-2xl font-semibold mb-2">Latest Survey</h3>
        <img :src="data.latestSurvey.image_preview" class="mx-auto w-[240px]" alt="" />
        <h3 class="font-bold text-xl mb-3">{{ data.latestSurvey.title }}</h3>
        <div class="flex justify-between text-sm mb-1">
          <div>Create Date:</div>
          <div>{{ data.latestSurvey.created_at }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <div>Expire Date:</div>
          <div>{{ data.latestSurvey.expire_date }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <div>Status:</div>
          <div>{{ data.latestSurvey.status ? "Active" : "Draft" }}</div>
        </div>
        <div class="flex justify-between text-sm mb-1">
          <div>Questions:</div>
          <div>{{ data.latestSurvey.questions }}</div>
        </div>
        <div class="flex justify-between text-sm mb-3">
          <div>Answers:</div>
          <div>{{ data.latestSurvey.answers }}</div>
        </div>
        <div class="flex justify-between">
          <router-link
            :to="{ name: 'SurveyView', params: { id: data.latestSurvey.id } }"
            class="py-2 px-4 flex rounded-md text-sm border-transparent text-indigo-500 hover:bg-indigo-700 hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-4 h-4 mr-1 inline-block"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
              />
            </svg>
            Edit Survey</router-link
          >

          <button
            class="flex py-2 px-4 rounded-md text-sm border-transparent text-indigo-500 hover:bg-indigo-700 hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-4 h-4 mr-1 inline-block"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
            </svg>
            View Answers
          </button>
        </div>
      </div>
      <div
        class="p-3 shadow-md bg-white row-span-2 animate-fade-in-down order-4 lg:order-3"
        style="animation-delay: 0.3s"
      >
        <div class="flex justify-between items-center mb-3 px-2">
          <h3 class="text-2xl font-semibold">Latest Answers</h3>
          <a
            href="javascript:void(0)"
            class="text-sm text-blue-500 hover:decoration-blue-500"
            >View all</a
          >
        </div>
        <a
          href="#"
          class="block p-2 hover:bg-gray-100/90"
          v-for="answer in data.latestAnswers"
          :key="answer.id"
        >
          <div class="font-semibold">{{ answer.survey.title }}</div>
          <small>
            Answer Made At:
            <i class="font-semibold">{{ answer.end_date }}</i>
          </small>
        </a>
      </div>
    </div>
  </PageComponent>
</template>

<script setup>
import PageComponent from "../components/PageComponent.vue";
import { useStore } from "vuex";
import { computed } from "vue";

const store = useStore();
const data = computed(() => store.state.dashboard.data);
const loading = computed(() => store.state.dashboard.loading);

store.dispatch("getDashboardData");
</script>
