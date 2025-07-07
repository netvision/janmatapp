<template>
  <div>
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Surveys</h1>
        <p class="text-gray-600">Manage your survey questionnaires</p>
      </div>
      <router-link
        to="/surveys/create"
        class="btn-primary"
      >
        Create Survey
      </router-link>
    </div>

    <!-- Surveys List -->
    <div class="card">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Title
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Description
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Questions
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Responses
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="survey in surveys" :key="survey.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ survey.title }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                {{ survey.description }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(survey.status)">
                  {{ getStatusText(survey.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button 
                  @click="showQuestionStats(survey)"
                  class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer"
                  :disabled="survey.questions_count === 0"
                >
                  {{ survey.questions_count || 0 }}
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button 
                  @click="showResponseStats(survey)"
                  class="text-green-600 hover:text-green-900 font-medium cursor-pointer"
                  :disabled="survey.responses_count === 0"
                >
                  {{ survey.responses_count || 0 }}
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(survey.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <router-link
                  :to="`/surveys/${survey.id}`"
                  class="text-primary-600 hover:text-primary-900 mr-3"
                >
                  Edit
                </router-link>
                <router-link
                  :to="`/surveys/${survey.id}/questions`"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  Manage Questions
                </router-link>
                <button
                  @click="deleteSurvey(survey.id)"
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

    <!-- Question Stats Modal -->
    <div v-if="showQuestionModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-900">Question Statistics: {{ selectedSurvey?.title }}</h3>
          <button @click="closeQuestionModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div v-if="questionStatsLoading" class="text-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Loading question statistics...</p>
        </div>
        
        <div v-else-if="questionStats" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="question in questionStats.questions" :key="question.question_id" class="border rounded-lg p-4">
              <h4 class="font-medium text-gray-900 mb-3 text-sm">{{ question.question_text }}</h4>
              
              <!-- Choice Questions -->
              <div v-if="question.question_type === 'single_choice' || question.question_type === 'multiple_choice' || question.question_type === 'rating'">
                <div v-if="Object.keys(question.answers).length > 0" class="space-y-2">
                  <div v-for="(count, option) in question.answers" :key="option" class="flex items-center">
                    <div class="flex-1">
                      <div class="flex justify-between text-xs">
                        <span class="text-gray-700 truncate">{{ option }}</span>
                        <span class="text-gray-500">{{ count }}</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                        <div 
                          class="bg-blue-600 h-1.5 rounded-full" 
                          :style="{ width: `${(count / question.total_answers) * 100}%` }"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="text-gray-500 text-xs">No responses yet</div>
              </div>

              <!-- Text Questions -->
              <div v-else-if="Object.keys(question.text_answers).length > 0" class="space-y-2">
                <div v-for="(count, answer) in question.text_answers" :key="answer" class="flex items-center">
                  <div class="flex-1">
                    <div class="flex justify-between text-xs">
                      <span class="text-gray-700 truncate">{{ answer }}</span>
                      <span class="text-gray-500">{{ count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                      <div 
                        class="bg-green-600 h-1.5 rounded-full" 
                        :style="{ width: `${(count / question.total_answers) * 100}%` }"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- No answers -->
              <div v-else class="text-gray-500 text-xs">No responses yet</div>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-8 text-gray-500">
          No question statistics available
        </div>
      </div>
    </div>

    <!-- Response Stats Modal -->
    <div v-if="showResponseModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-900">Response Statistics: {{ selectedSurvey?.title }}</h3>
          <button @click="closeResponseModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div v-if="responseStatsLoading" class="text-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Loading response statistics...</p>
        </div>
        
        <div v-else-if="responseStats" class="space-y-6">
          <!-- Summary Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-blue-900">Total Responses</h4>
              <p class="text-2xl font-bold text-blue-600">{{ responseStats.total_responses }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-green-900">Questions Answered</h4>
              <p class="text-2xl font-bold text-green-600">{{ responseStats.questions?.length || 0 }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-purple-900">Completion Rate</h4>
              <p class="text-2xl font-bold text-purple-600">{{ responseStats.total_responses > 0 ? '100%' : '0%' }}</p>
            </div>
          </div>

          <!-- Question Analytics -->
          <div v-if="responseStats.questions && responseStats.questions.length > 0">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Question Analytics</h4>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div v-for="question in responseStats.questions" :key="question.question_id" class="border rounded-lg p-4">
                <h5 class="font-medium text-gray-900 mb-3">{{ question.question_text }}</h5>
                
                <!-- Choice Questions -->
                <div v-if="question.question_type === 'single_choice' || question.question_type === 'multiple_choice' || question.question_type === 'rating'">
                  <div v-if="Object.keys(question.answers).length > 0" class="space-y-2">
                    <div v-for="(count, option) in question.answers" :key="option" class="flex items-center">
                      <div class="flex-1">
                        <div class="flex justify-between text-sm">
                          <span class="text-gray-700">{{ option }}</span>
                          <span class="text-gray-500">{{ count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                          <div 
                            class="bg-blue-600 h-2 rounded-full" 
                            :style="{ width: `${(count / question.total_answers) * 100}%` }"
                          ></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-gray-500 text-sm">No responses yet</div>
                </div>

                <!-- Text Questions -->
                <div v-else-if="Object.keys(question.text_answers).length > 0" class="space-y-2">
                  <div v-for="(count, answer) in question.text_answers" :key="answer" class="flex items-center">
                    <div class="flex-1">
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-700 truncate">{{ answer }}</span>
                        <span class="text-gray-500">{{ count }}</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div 
                          class="bg-green-600 h-2 rounded-full" 
                          :style="{ width: `${(count / question.total_answers) * 100}%` }"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- No answers -->
                <div v-else class="text-gray-500 text-sm">No responses yet</div>
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-8 text-gray-500">
          No response statistics available
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { format } from 'date-fns'

const surveys = ref([])
const showQuestionModal = ref(false)
const showResponseModal = ref(false)
const selectedSurvey = ref(null)
const questionStats = ref(null)
const responseStats = ref(null)
const questionStatsLoading = ref(false)
const responseStatsLoading = ref(false)

const getStatusClass = (status) => {
  const classes = {
    0: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800',
    1: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800',
    2: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800'
  }
  return classes[status] || classes[0]
}

const getStatusText = (status) => {
  const texts = {
    0: 'Draft',
    1: 'Active',
    2: 'Inactive'
  }
  return texts[status] || 'Draft'
}

const formatDate = (timestamp) => {
  return format(new Date(timestamp * 1000), 'MMM dd, yyyy')
}

const loadSurveys = async () => {
  try {
    const response = await api.get('/surveys')
    surveys.value = response.data.data
  } catch (error) {
    console.error('Error loading surveys:', error)
  }
}

const showQuestionStats = async (survey) => {
  if (survey.questions_count === 0) return
  
  selectedSurvey.value = survey
  showQuestionModal.value = true
  questionStatsLoading.value = true
  questionStats.value = null
  
  try {
    const response = await api.get(`/responses/analytics?survey_id=${survey.id}`)
    questionStats.value = response.data.data
  } catch (error) {
    console.error('Error loading question stats:', error)
  } finally {
    questionStatsLoading.value = false
  }
}

const showResponseStats = async (survey) => {
  if (survey.responses_count === 0) return
  
  selectedSurvey.value = survey
  showResponseModal.value = true
  responseStatsLoading.value = true
  responseStats.value = null
  
  try {
    const response = await api.get(`/responses/analytics?survey_id=${survey.id}`)
    responseStats.value = response.data.data
  } catch (error) {
    console.error('Error loading response stats:', error)
  } finally {
    responseStatsLoading.value = false
  }
}

const closeQuestionModal = () => {
  showQuestionModal.value = false
  selectedSurvey.value = null
  questionStats.value = null
}

const closeResponseModal = () => {
  showResponseModal.value = false
  selectedSurvey.value = null
  responseStats.value = null
}

const deleteSurvey = async (id) => {
  if (!confirm('Are you sure you want to delete this survey?')) return
  
  try {
    await api.post(`/surveys/${id}/delete`)
    await loadSurveys()
  } catch (error) {
    console.error('Error deleting survey:', error)
  }
}

onMounted(() => {
  loadSurveys()
})
</script> 