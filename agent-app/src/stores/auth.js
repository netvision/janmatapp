import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('agent_token') || null)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)

  const login = async (username, password) => {
    loading.value = true
    try {
      const response = await api.post('/auth/login', {
        username,
        password
      })
      
      if (response.data.success) {
        token.value = response.data.token
        user.value = response.data.user
        
        // Store in localStorage
        localStorage.setItem('agent_token', token.value)
        localStorage.setItem('agent_user', JSON.stringify(user.value))
        
        return { success: true }
      } else {
        return { success: false, message: response.data.message }
      }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Login failed' 
      }
    } finally {
      loading.value = false
    }
  }

  const logout = () => {
    token.value = null
    user.value = null
    localStorage.removeItem('agent_token')
    localStorage.removeItem('agent_user')
  }

  const initializeAuth = () => {
    const storedToken = localStorage.getItem('agent_token')
    const storedUser = localStorage.getItem('agent_user')
    
    if (storedToken && storedUser) {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  return {
    user,
    token,
    loading,
    isAuthenticated,
    login,
    logout,
    initializeAuth
  }
}) 