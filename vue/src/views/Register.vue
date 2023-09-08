<template>
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <img
      class="mx-auto h-10 w-auto"
      src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
      alt="Your Company"
    />
    <h2
      class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
    >
      Sign up for free
    </h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <Alert
      v-if="Object.keys(errors).length"
      :key="errors"
      class="flex-col items-stretch text-sm mb-2"
    >
      <div v-for="(field, ind) in Object.keys(errors)" :key="ind">
        <div v-for="(error, ind) in errors[field] || []" :key="ind">* {{ error }}</div>
      </div>
    </Alert>

    <form class="space-y-6" @submit="register">
      <div>
        <label for="full_name" class="block text-sm font-medium leading-6 text-gray-900"
          >Full Name</label
        >
        <div class="mt-2">
          <input
            id="full_name"
            name="full_name"
            v-model="user.name"
            type="text"
            autocomplete="full_name"
            required=""
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
          />
        </div>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900"
          >Email address</label
        >
        <div class="mt-2">
          <input
            id="email"
            name="email"
            v-model="user.email"
            type="email"
            autocomplete="email"
            required=""
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
          />
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-medium leading-6 text-gray-900"
            >Password</label
          >
        </div>
        <div class="mt-2">
          <input
            id="password"
            name="password"
            v-model="user.password"
            type="password"
            autocomplete="current-password"
            required=""
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
          />
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label
            for="password_confirmation"
            class="block text-sm font-medium leading-6 text-gray-900"
            >Confirm Password</label
          >
        </div>
        <div class="mt-2">
          <input
            id="password_confirmation"
            name="password_confirmation"
            v-model="user.password_confirmation"
            type="password"
            autocomplete="password_confirmation"
            required=""
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
          />
        </div>
      </div>

      <div>
        <button
          :disabled="loading"
          type="submit"
          class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          :class="{ 'cursor-not-allowed': loading, 'hover:bg-indigo-500': loading }"
        >
          <svg
            v-if="loading"
            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          Sign up
        </button>
      </div>
    </form>

    <p class="mt-10 text-center text-sm text-gray-500">
      Already have an account?
      {{ " " }}
      <router-link
        :to="{ name: 'Login' }"
        class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500"
        >Login</router-link
      >
    </p>
  </div>
</template>

<script setup>
import store from "../store";
import { useRouter } from "vue-router";
import { ref } from "vue";
import Alert from "../components/Alert.vue";

const user = {
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
};

const errors = ref({});
const loading = ref(false);

const router = useRouter();

function register(ev) {
  ev.preventDefault();
  loading.value = true;
  store
    .dispatch("register", user)
    .then((data) => {
      loading.value = false;
      router.push({
        name: "Dashboard",
      });
    })
    .catch((error) => {
      loading.value = false;
      if (error.response.status === 422) {
        errors.value = error.response.data.errors;
      }
    });
}
</script>

<style scoped>
.read-the-docs {
  color: #888;
}
</style>
