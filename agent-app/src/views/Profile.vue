<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="px-4 py-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900">Profile</h1>
        <button @click="router.back()" class="btn btn-secondary text-sm">Back</button>
      </div>
    </header>
    <main class="px-4 py-6">
      <div class="card p-6 max-w-md mx-auto">
        <div class="flex items-center space-x-4 mb-4">
          <div class="h-14 w-14 rounded-full bg-primary-100 flex items-center justify-center">
            <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div>
            <h2 class="text-xl font-bold text-gray-900">{{ authStore.user?.username }}</h2>
            <p class="text-sm text-gray-600">Agent</p>
          </div>
        </div>
        <div class="mb-6">
          <p class="text-sm text-gray-700"><span class="font-medium">Email:</span> {{ authStore.user?.email || 'N/A' }}</p>
        </div>
        <button @click="logout" class="w-full btn btn-secondary">Logout</button>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
const router = useRouter()
const authStore = useAuthStore()
const logout = () => {
  authStore.logout()
  router.push('/login')
}
</script> 