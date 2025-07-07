<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold text-blue-600">Debug Page</h1>
    <p class="text-gray-600 mt-2">If you can see this, Vue is working!</p>
    
    <div class="mt-4 p-4 bg-gray-100 rounded">
      <h2 class="font-semibold">Current State:</h2>
      <p>Authenticated: {{ userStore.isAuthenticated }}</p>
      <p>User: {{ userStore.user ? userStore.user.username : 'None' }}</p>
      <p>Current Route: {{ $route.path }}</p>
    </div>
    
    <div class="mt-6 space-y-4">
      <div>
        <button @click="testSimple" class="btn-primary mr-2">
          Test Simple API
        </button>
        <button @click="testDb" class="btn-primary mr-2">
          Test Database
        </button>
        <button @click="testHealth" class="btn-primary mr-2">
          Test Health
        </button>
        <button @click="testLogin" class="btn-primary mr-2">
          Test Login
        </button>
        <button @click="testSurveys" class="btn-primary">
          Test Surveys
        </button>
      </div>
      
      <div v-if="results.length > 0" class="space-y-2">
        <h3 class="font-semibold">Test Results:</h3>
        <div v-for="(result, index) in results" :key="index" class="p-3 rounded border">
          <div class="font-medium">{{ result.test }}</div>
          <div class="text-sm text-gray-600">{{ result.status }}</div>
          <pre class="text-xs bg-gray-100 p-2 mt-1 rounded overflow-auto">{{ JSON.stringify(result.data, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'
import api from '@/services/api'

const userStore = useUserStore()
const results = ref([])

const addResult = (test, status, data) => {
  results.value.unshift({
    test,
    status,
    data,
    timestamp: new Date().toLocaleTimeString()
  })
}

const testSimple = async () => {
  try {
    const response = await api.get('/test/simple')
    addResult('Simple API', '✅ Success', response.data)
  } catch (error) {
    addResult('Simple API', '❌ Failed', {
      message: error.message,
      status: error.response?.status,
      data: error.response?.data
    })
  }
}

const testDb = async () => {
  try {
    const response = await api.get('/test/db')
    addResult('Database', '✅ Success', response.data)
  } catch (error) {
    addResult('Database', '❌ Failed', {
      message: error.message,
      status: error.response?.status,
      data: error.response?.data
    })
  }
}

const testHealth = async () => {
  try {
    const response = await api.get('/health')
    addResult('Health', '✅ Success', response.data)
  } catch (error) {
    addResult('Health', '❌ Failed', {
      message: error.message,
      status: error.response?.status,
      data: error.response?.data
    })
  }
}

const testLogin = async () => {
  try {
    const response = await api.post('/auth/login', {
      username: 'admin',
      password: 'admin123'
    })
    addResult('Login', '✅ Success', response.data)
  } catch (error) {
    addResult('Login', '❌ Failed', {
      message: error.message,
      status: error.response?.status,
      data: error.response?.data
    })
  }
}

const testSurveys = async () => {
  try {
    const response = await api.get('/surveys')
    addResult('Surveys', '✅ Success', response.data)
  } catch (error) {
    addResult('Surveys', '❌ Failed', {
      message: error.message,
      status: error.response?.status,
      data: error.response?.data
    })
  }
}
</script> 