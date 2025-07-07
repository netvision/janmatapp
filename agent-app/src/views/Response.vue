<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4">
    <div class="max-w-md w-full card p-6 text-center">
      <svg class="mx-auto h-12 w-12 text-success-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h2 class="text-2xl font-bold text-gray-900 mb-2">Thank you!</h2>
      <p class="text-gray-600 mb-6">Your response has been recorded.</p>
      <button @click="goToSurveys" class="btn btn-primary w-full">Back to Surveys</button>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
const goToSurveys = () => {
  router.push('/surveys')
}
</script> 