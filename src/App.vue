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
              <div class="relative group">
                <button class="flex items-center space-x-2 text-sm text-gray-500 hover:text-gray-700 focus:outline-none">
                  <span>{{ userStore.user?.username }}</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                  <div class="py-1">
                    <router-link
                      to="/profile"
                      class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      Profile Settings
                    </router-link>
                    <div class="border-t border-gray-100"></div>
                    <button
                      @click="logout"
                      class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                      </svg>
                      Logout
                    </button>
                  </div>
                </div>
              </div>
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
              
              <div class="border-t border-gray-200 my-4"></div>
              
              <router-link
                to="/profile"
                class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900"
                :class="{ 'bg-primary-50 text-primary-700': $route.path === '/profile' }"
              >
                <UserIcon class="w-5 h-5 mr-3" />
                Profile Settings
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
  UserIcon,
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