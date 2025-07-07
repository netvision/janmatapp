<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Agent</h1>
        <router-link
          to="/agents"
          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors"
        >
          Back to Agents
        </router-link>
      </div>

      <div v-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading agent details...</p>
      </div>

      <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ error }}
      </div>

      <form v-else @submit.prevent="handleSubmit" class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-4">
          <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
            Username
          </label>
          <input
            id="username"
            v-model="form.username"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :disabled="loading"
          />
        </div>

        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :disabled="loading"
          />
        </div>

        <div class="mb-4">
          <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
            Status
          </label>
          <select
            id="status"
            v-model="form.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :disabled="loading"
          >
            <option value="10">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>

        <div class="mb-6">
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            New Password (leave blank to keep current)
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :disabled="loading"
          />
        </div>

        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="$router.push('/agents')"
            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors"
            :disabled="loading"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors"
            :disabled="loading"
          >
            {{ loading ? 'Updating...' : 'Update Agent' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

export default {
  name: 'EditAgent',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const loading = ref(false)
    const error = ref('')
    
    const form = ref({
      username: '',
      email: '',
      password: '',
      status: 10
    })

    const loadAgent = async () => {
      loading.value = true
      error.value = ''
      
      try {
        const response = await api.get(`/agents/${route.params.id}`)
        if (response.data.success) {
          const agent = response.data.data
          form.value.username = agent.username
          form.value.email = agent.email
          form.value.password = '' // Don't populate password field
          form.value.status = agent.status
        } else {
          error.value = response.data.message || 'Failed to load agent'
        }
      } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load agent'
      } finally {
        loading.value = false
      }
    }

    const handleSubmit = async () => {
      loading.value = true
      error.value = ''
      
      try {
        const updateData = {
          username: form.value.username,
          email: form.value.email,
          status: form.value.status
        }
        
        // Only include password if it's not empty
        if (form.value.password.trim()) {
          updateData.password = form.value.password
        }
        
        const response = await api.put(`/agents/${route.params.id}/update`, updateData)
        
        if (response.data.success) {
          router.push('/agents')
        } else {
          error.value = response.data.message || 'Failed to update agent'
        }
      } catch (err) {
        error.value = err.response?.data?.message || 'Failed to update agent'
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadAgent()
    })

    return {
      form,
      loading,
      error,
      handleSubmit
    }
  }
}
</script> 