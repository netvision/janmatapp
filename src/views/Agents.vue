<template>
  <div>
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Field Agents</h1>
        <p class="text-gray-600">Manage your field survey agents</p>
      </div>
      <router-link
        to="/agents/create"
        class="btn-primary"
      >
        Add Agent
      </router-link>
    </div>

    <!-- Agents List -->
    <div class="card">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Phone
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Surveys Completed
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Last Active
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="agent in agents" :key="agent.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ agent.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ agent.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ agent.phone }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(agent.status)">
                  {{ getStatusText(agent.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ agent.surveys_completed || 0 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ agent.last_active ? formatDate(agent.last_active) : 'Never' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <router-link
                  :to="`/agents/${agent.id}`"
                  class="text-primary-600 hover:text-primary-900 mr-3"
                >
                  Edit
                </router-link>
                <button
                  @click="viewStats(agent.id)"
                  class="text-green-600 hover:text-green-900 mr-3"
                >
                  View Stats
                </button>
                <button
                  @click="deleteAgent(agent.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Stats Modal -->
    <div v-if="showStatsModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-bold">Agent Statistics</h3>
          <button @click="closeStatsModal" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div v-if="statsLoading" class="text-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Loading statistics...</p>
        </div>

        <div v-else-if="statsData" class="space-y-6">
          <!-- Agent Info -->
          <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="font-semibold text-gray-900">{{ statsData.agent.name }}</h4>
            <p class="text-sm text-gray-600">{{ statsData.agent.email }}</p>
          </div>

          <!-- Period Selector -->
          <div class="flex space-x-2">
            <button 
              @click="loadStats(selectedAgentId, 'day')"
              :class="statsData.period === 'day' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
              class="px-4 py-2 rounded-lg text-sm font-medium"
            >
              Today
            </button>
            <button 
              @click="loadStats(selectedAgentId, 'week')"
              :class="statsData.period === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
              class="px-4 py-2 rounded-lg text-sm font-medium"
            >
              Last 7 Days
            </button>
            <button 
              @click="loadStats(selectedAgentId, 'month')"
              :class="statsData.period === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
              class="px-4 py-2 rounded-lg text-sm font-medium"
            >
              Last 30 Days
            </button>
          </div>

          <!-- Summary Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
              <h5 class="text-sm font-medium text-blue-800">Total Responses</h5>
              <p class="text-2xl font-bold text-blue-900">{{ statsData.summary.total_responses }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
              <h5 class="text-sm font-medium text-green-800">With Voice</h5>
              <p class="text-2xl font-bold text-green-900">{{ statsData.summary.total_with_voice }}</p>
              <p class="text-sm text-green-600">{{ statsData.summary.voice_percentage }}%</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
              <h5 class="text-sm font-medium text-purple-800">With Location</h5>
              <p class="text-2xl font-bold text-purple-900">{{ statsData.summary.total_with_location }}</p>
              <p class="text-sm text-purple-600">{{ statsData.summary.location_percentage }}%</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-4">
              <h5 class="text-sm font-medium text-orange-800">Avg/Day</h5>
              <p class="text-2xl font-bold text-orange-900">
                {{ statsData.daily_stats.length > 0 ? Math.round(statsData.summary.total_responses / statsData.daily_stats.length) : 0 }}
              </p>
            </div>
          </div>

          <!-- Daily Chart -->
          <div v-if="statsData.daily_stats.length > 0" class="bg-white border rounded-lg p-4">
            <h5 class="font-semibold text-gray-900 mb-4">Daily Activity</h5>
            <div class="space-y-3">
                              <div v-for="day in statsData.daily_stats" :key="day.date" class="flex items-center">
                  <div class="w-24 text-sm text-gray-600">{{ formatStatsDate(day.date) }}</div>
                <div class="flex-1 ml-4">
                  <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700">{{ day.responses }} responses</span>
                    <span class="text-gray-500">{{ day.with_voice }} voice, {{ day.with_location }} location</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-blue-600 h-2 rounded-full" 
                      :style="{ width: `${Math.min((day.responses / Math.max(...statsData.daily_stats.map(d => d.responses))) * 100, 100)}%` }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8 text-gray-500">
            No activity data for this period
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { format } from 'date-fns'

const toDate = (ts) => {
  if (ts === undefined || ts === null) return null
  if (typeof ts === 'number') return ts > 1e12 ? new Date(ts) : new Date(ts * 1000)
  const n = Number(ts)
  if (!Number.isNaN(n)) return n > 1e12 ? new Date(n) : new Date(n * 1000)
  const d = new Date(ts)
  return isNaN(d.getTime()) ? null : d
}

const agents = ref([])
const showStatsModal = ref(false)
const statsLoading = ref(false)
const statsData = ref(null)
const selectedAgentId = ref(null)

const getStatusClass = (status) => {
  const classes = {
    0: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800',
    10: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'
  }
  return classes[status] || classes[0]
}

const getStatusText = (status) => {
  const texts = {
    0: 'Inactive',
    10: 'Active'
  }
  return texts[status] || 'Inactive'
}

const formatDate = (timestamp) => {
  const d = toDate(timestamp)
  return d ? format(d, 'MMM dd, yyyy HH:mm') : ''
}

const formatStatsDate = (dateString) => {
  return format(new Date(dateString), 'MMM dd')
}

const loadAgents = async () => {
  try {
    const response = await api.get('/agents')
    agents.value = response.data.data
  } catch (error) {
    console.error('Error loading agents:', error)
  }
}

const deleteAgent = async (id) => {
  if (!confirm('Are you sure you want to delete this agent?')) return
  
  try {
    await api.delete(`/agents/${id}/delete`)
    await loadAgents()
  } catch (error) {
    console.error('Error deleting agent:', error)
  }
}

const viewStats = (id) => {
  selectedAgentId.value = id
  showStatsModal.value = true
  loadStats(id, 'day') // Load default stats for the modal
}

const closeStatsModal = () => {
  showStatsModal.value = false
  statsData.value = null
  selectedAgentId.value = null
}

const loadStats = async (agentId, period) => {
  statsLoading.value = true
  try {
    const response = await api.get(`/agents/${agentId}/stats?period=${period}`)
    statsData.value = response.data.data
  } catch (error) {
    console.error('Error loading stats:', error)
    statsData.value = null
  } finally {
    statsLoading.value = false
  }
}

onMounted(() => {
  loadAgents()
})
</script> 