<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="px-4 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-600">Welcome back, {{ authStore.user?.username }}</p>
          </div>
          <button @click="logout" class="btn btn-secondary text-sm">
            Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="px-4 py-6 space-y-6">
      <!-- Quick Stats -->
      <div class="grid grid-cols-2 gap-4">
        <div class="card p-4">
          <div class="flex items-center">
            <div class="p-2 bg-primary-100 rounded-lg">
              <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-600">Active Surveys</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.activeSurveys }}</p>
            </div>
          </div>
        </div>

        <div class="card p-4">
          <div class="flex items-center">
            <div class="p-2 bg-success-100 rounded-lg">
              <svg class="h-6 w-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-600">Responses Today</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.responsesToday }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="space-y-3">
          <button 
            @click="router.push('/surveys')"
            class="w-full flex items-center justify-between p-3 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors"
          >
            <div class="flex items-center">
              <svg class="h-5 w-5 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <span class="font-medium text-gray-900">View Surveys</span>
            </div>
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          <button 
            @click="router.push('/profile')"
            class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <div class="flex items-center">
              <svg class="h-5 w-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span class="font-medium text-gray-900">Profile</span>
            </div>
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Recent Surveys -->
      <div class="card p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Surveys</h2>
        <div v-if="loading" class="text-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
          <p class="mt-2 text-sm text-gray-600">Loading surveys...</p>
        </div>
        <div v-else-if="recentSurveys.length === 0" class="text-center py-8">
          <p class="text-gray-600">No surveys available</p>
        </div>
        <div v-else class="space-y-3">
          <div 
            v-for="survey in recentSurveys" 
            :key="survey.id"
            @click="router.push(`/survey/${survey.id}`)"
            class="p-3 bg-gray-50 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors"
          >
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-medium text-gray-900">{{ survey.title }}</h3>
                <p class="text-sm text-gray-600">{{ survey.question_count }} questions</p>
              </div>
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const recentSurveys = ref([])
const stats = ref({
  activeSurveys: 0,
  responsesToday: 0
})

const logout = () => {
  authStore.logout()
  router.push('/login')
}

const fetchDashboardData = async () => {
  loading.value = true
  try {
    // Fetch surveys
    const surveysResponse = await api.get('/surveys')
    if (surveysResponse.data.success) {
      recentSurveys.value = surveysResponse.data.data.slice(0, 5)
      stats.value.activeSurveys = surveysResponse.data.data.length
    }

    // Fetch today's responses count
    const today = new Date().toISOString().split('T')[0]
    const responsesResponse = await api.get(`/responses?date_from=${today}`)
    if (responsesResponse.data.success) {
      stats.value.responsesToday = responsesResponse.data.data.length
    }
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script> 