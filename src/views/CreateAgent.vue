<template>
  <div>
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Add Field Agent</h1>
      <p class="text-gray-600">Create a new field survey agent</p>
    </div>

    <div class="card">
      <form @submit.prevent="handleSubmit">
        <div class="space-y-6">
          <!-- Basic Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                  Full Name
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  class="input-field"
                  placeholder="Enter agent's full name"
                />
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                  Email Address
                </label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="input-field"
                  placeholder="Enter email address"
                />
              </div>

              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">
                  Phone Number
                </label>
                <input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  required
                  class="input-field"
                  placeholder="Enter phone number"
                />
              </div>

              <div>
                <label for="username" class="block text-sm font-medium text-gray-700">
                  Username
                </label>
                <input
                  id="username"
                  v-model="form.username"
                  type="text"
                  required
                  class="input-field"
                  placeholder="Enter username for login"
                />
              </div>

              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                  Password
                </label>
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  class="input-field"
                  placeholder="Enter password"
                />
              </div>

              <div>
                <label for="status" class="block text-sm font-medium text-gray-700">
                  Status
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  class="input-field"
                >
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Additional Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label for="address" class="block text-sm font-medium text-gray-700">
                  Address
                </label>
                <textarea
                  id="address"
                  v-model="form.address"
                  rows="3"
                  class="input-field"
                  placeholder="Enter agent's address"
                ></textarea>
              </div>

              <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">
                  Notes
                </label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="3"
                  class="input-field"
                  placeholder="Any additional notes about the agent"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Submit -->
          <div class="flex justify-end space-x-3">
            <router-link
              to="/agents"
              class="btn-secondary"
            >
              Cancel
            </router-link>
            <button
              type="submit"
              :disabled="loading"
              class="btn-primary"
            >
              {{ loading ? 'Creating...' : 'Create Agent' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

const form = ref({
  name: '',
  email: '',
  phone: '',
  username: '',
  password: '',
  status: '1',
  address: '',
  notes: ''
})

const loading = ref(false)

const handleSubmit = async () => {
  loading.value = true

  try {
    const agentData = {
      name: form.value.name,
      email: form.value.email,
      phone: form.value.phone,
      username: form.value.username,
      password: form.value.password,
      status: parseInt(form.value.status),
      address: form.value.address,
      notes: form.value.notes
    }

    await api.post('/agents/create', agentData)
    router.push('/agents')
  } catch (error) {
    console.error('Error creating agent:', error)
  } finally {
    loading.value = false
  }
}
</script> 