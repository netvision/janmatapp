<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Change Password</h1>
        <p class="text-gray-600">Update your account password</p>
      </div>

      <!-- Change Password -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Update Password</h2>
        
        <form @submit.prevent="handlePasswordChange" class="space-y-4">
          <!-- Hidden field with user info -->
          <input type="hidden" :value="userStore.user?.id" />
          
          <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
              Current Password
            </label>
            <input
              id="current_password"
              v-model="passwordForm.current_password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter your current password"
              :disabled="loading"
            />
          </div>

          <div>
            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
              New Password
            </label>
            <input
              id="new_password"
              v-model="passwordForm.new_password"
              type="password"
              required
              minlength="6"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter your new password"
              :disabled="loading"
            />
            <div class="mt-2">
              <div class="flex items-center space-x-2">
                <div class="flex-1">
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="h-2 rounded-full transition-all duration-300"
                      :class="getPasswordStrengthClass()"
                      :style="{ width: getPasswordStrengthWidth() }"
                    ></div>
                  </div>
                </div>
                <span class="text-xs font-medium" :class="getPasswordStrengthTextClass()">
                  {{ getPasswordStrengthText() }}
                </span>
              </div>
              <p class="text-xs text-gray-500 mt-1">Password must be at least 6 characters long</p>
            </div>
          </div>

          <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
              Confirm New Password
            </label>
            <input
              id="confirm_password"
              v-model="passwordForm.confirm_password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Confirm your new password"
              :disabled="loading"
            />
          </div>

          <!-- Error Message -->
          <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-3">
            <p class="text-sm text-red-600">{{ error }}</p>
          </div>

          <!-- Success Message -->
          <div v-if="success" class="bg-green-50 border border-green-200 rounded-lg p-3">
            <p class="text-sm text-green-600">{{ success }}</p>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="clearAll"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              :disabled="loading"
            >
              Clear
            </button>
            <button
              type="submit"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              :disabled="loading || !isFormValid"
            >
              <span v-if="loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Changing Password...
              </span>
              <span v-else>Change Password</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import api from '@/services/api'

const router = useRouter()
const userStore = useUserStore()

const passwordForm = ref({
  current_password: '',
  new_password: '',
  confirm_password: ''
})

const loading = ref(false)
const error = ref('')
const success = ref('')

const isFormValid = computed(() => {
  return passwordForm.value.current_password &&
         passwordForm.value.new_password &&
         passwordForm.value.confirm_password &&
         passwordForm.value.new_password === passwordForm.value.confirm_password &&
         passwordForm.value.new_password.length >= 6
})

const getPasswordStrength = () => {
  const password = passwordForm.value.new_password
  if (!password) return 0
  
  let score = 0
  
  // Length check
  if (password.length >= 6) score += 1
  if (password.length >= 8) score += 1
  if (password.length >= 12) score += 1
  
  // Character variety checks
  if (/[a-z]/.test(password)) score += 1 // lowercase
  if (/[A-Z]/.test(password)) score += 1 // uppercase
  if (/[0-9]/.test(password)) score += 1 // numbers
  if (/[^A-Za-z0-9]/.test(password)) score += 1 // special characters
  
  return Math.min(score, 5)
}

const getPasswordStrengthWidth = () => {
  const strength = getPasswordStrength()
  return `${(strength / 5) * 100}%`
}

const getPasswordStrengthClass = () => {
  const strength = getPasswordStrength()
  if (strength <= 1) return 'bg-red-500'
  if (strength <= 2) return 'bg-orange-500'
  if (strength <= 3) return 'bg-yellow-500'
  if (strength <= 4) return 'bg-blue-500'
  return 'bg-green-500'
}

const getPasswordStrengthText = () => {
  const strength = getPasswordStrength()
  if (strength <= 1) return 'Weak'
  if (strength <= 2) return 'Fair'
  if (strength <= 3) return 'Good'
  if (strength <= 4) return 'Strong'
  return 'Very Strong'
}

const getPasswordStrengthTextClass = () => {
  const strength = getPasswordStrength()
  if (strength <= 1) return 'text-red-600'
  if (strength <= 2) return 'text-orange-600'
  if (strength <= 3) return 'text-yellow-600'
  if (strength <= 4) return 'text-blue-600'
  return 'text-green-600'
}

const clearForm = () => {
  passwordForm.value = {
    current_password: '',
    new_password: '',
    confirm_password: ''
  }
  error.value = ''
  // Don't clear success message when clearing form manually
  // success.value = ''
}

const clearAll = () => {
  passwordForm.value = {
    current_password: '',
    new_password: '',
    confirm_password: ''
  }
  error.value = ''
  success.value = ''
}

const handlePasswordChange = async () => {
  // Clear previous messages
  error.value = ''
  success.value = ''

  // Validate passwords match
  if (passwordForm.value.new_password !== passwordForm.value.confirm_password) {
    error.value = 'New passwords do not match'
    return
  }

  // Validate password length
  if (passwordForm.value.new_password.length < 6) {
    error.value = 'New password must be at least 6 characters long'
    return
  }

  loading.value = true

  try {
    console.log('Sending password change request...')
    const response = await api.post('/auth/change-password', {
      current_password: passwordForm.value.current_password,
      new_password: passwordForm.value.new_password
    })

    console.log('Password change response:', response.data)

    if (response.data.success) {
      success.value = 'Password changed successfully! Please log in again with your new password...'
      console.log('Success message set:', success.value)
      // Clear form fields but keep success message visible
      passwordForm.value = {
        current_password: '',
        new_password: '',
        confirm_password: ''
      }
      // Log out user and redirect to login after 5 seconds
      setTimeout(() => {
        userStore.logout()
        router.push('/login?message=Password changed successfully. Please log in again with your new password.')
      }, 5000)
    } else {
      error.value = response.data.message || 'Failed to change password'
      console.log('Error from server:', error.value)
    }
  } catch (err) {
    console.error('Password change error:', err)
    console.error('Error response:', err.response?.data)
    error.value = err.response?.data?.message || 'Failed to change password. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
