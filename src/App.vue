<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Show login page if not authenticated -->
    <router-view v-if="!userStore.isAuthenticated" />
    
    <!-- Show main app layout if authenticated -->
    <div v-else>
      <!-- Navigation -->
      <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
              <h1 class="text-xl font-semibold text-gray-900">Janmat Survey Admin</h1>
            </div>
            <div class="flex items-center space-x-4">
              <span class="text-sm text-gray-500">{{ userStore.user?.username }}</span>
              <button @click="logout" class="btn-secondary text-sm">Logout</button>
            </div>
          </div>
        </div>
      </nav>

      <!-- Sidebar and Main Content -->
      <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm min-h-screen">
          <nav class="mt-8">
            <div class="px-4 space-y-2">
              <router-link
                to="/dashboard"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900"
                :class="{ 'bg-primary-50 text-primary-700': $route.path === '/dashboard' }"
              >
                <ChartBarIcon class="w-5 h-5 mr-3" />
                Dashboard
              </router-link>
              <router-link
                to="/surveys"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900"
                :class="{ 'bg-primary-50 text-primary-700': $route.path.startsWith('/surveys') }"
              >
                <ClipboardDocumentListIcon class="w-5 h-5 mr-3" />
                Surveys
              </router-link>
              <router-link
                to="/responses"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900"
                :class="{ 'bg-primary-50 text-primary-700': $route.path.startsWith('/responses') }"
              >
                <DocumentTextIcon class="w-5 h-5 mr-3" />
                Responses
              </router-link>
              <router-link
                to="/agents"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900"
                :class="{ 'bg-primary-50 text-primary-700': $route.path.startsWith('/agents') }"
              >
                <UsersIcon class="w-5 h-5 mr-3" />
                Agents
              </router-link>
            </div>
          </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useUserStore } from '@/stores/user'
import { useRouter } from 'vue-router'
import {
  ChartBarIcon,
  ClipboardDocumentListIcon,
  DocumentTextIcon,
  UsersIcon,
} from '@heroicons/vue/24/outline'

const userStore = useUserStore()
const router = useRouter()

const logout = () => {
  userStore.logout()
  router.push('/login')
}

onMounted(() => {
  userStore.initializeAuth()
})
</script> 