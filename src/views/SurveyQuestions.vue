<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manage Questions</h1>
        <router-link
          to="/surveys"
          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors"
        >
          Back to Surveys
        </router-link>
      </div>

      <div v-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading questions...</p>
      </div>

      <div v-else>
        <div class="mb-6 flex justify-between items-center">
          <h2 class="text-lg font-semibold">Questions for: <span class="text-primary-600">{{ surveyTitle }}</span></h2>
          <button @click="showAdd = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Add Question</button>
        </div>

        <!-- Analytics Section -->
        <div v-if="analytics && analytics.questions && analytics.questions.length > 0" class="mb-8">
          <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Survey Analytics</h3>
              <p class="text-sm text-gray-600 mt-1">{{ analytics.total_responses }} total responses</p>
            </div>
            
            <div class="p-6">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div v-for="question in analytics.questions" :key="question.question_id" class="border rounded-lg p-4">
                  <h4 class="font-medium text-gray-900 mb-3">{{ question.question_text }}</h4>
                  
                  <!-- Choice Questions (Bar Chart) -->
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

                  <!-- Text/Number Questions (Top Answers) -->
                  <div v-else-if="Object.keys(question.text_answers).length > 0" class="space-y-2">
                    <div v-for="(count, answer) in question.text_answers" :key="answer" class="flex items-center">
                      <div class="flex-1">
                        <div class="flex justify-between text-sm">
                          <span class="text-gray-700 truncate">{{ answer }}</span>
                          <span class="text-gray-500 ml-2">{{ count }}</span>
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

                  <!-- No answers yet -->
                  <div v-else class="text-gray-500 text-sm">No responses yet</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="questions.length === 0" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
          <p class="font-medium text-yellow-800">No questions found for this survey.</p>
          <p class="text-yellow-700 text-sm mt-1">Questions loaded: {{ questions.length }}</p>
        </div>

        <!-- Debug info (remove in production) -->
        <div v-if="questions.length > 0" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
          <p class="text-sm text-gray-600">Debug: {{ questions.length }} questions loaded</p>
        </div>

        <div class="space-y-4 mb-8">
          <div 
            v-for="(q, idx) in questions" 
            :key="q.id"
            class="bg-white shadow rounded-lg p-6 flex justify-between items-start hover:shadow-md transition-shadow"
          >
            <div class="flex-1 mr-4">
              <div class="font-medium text-gray-900 text-lg mb-2">{{ idx + 1 }}. {{ q.text }}</div>
              <div class="text-sm text-gray-600 mb-1">Type: {{ getTypeText(q.type) }} | Required: {{ q.required ? 'Yes' : 'No' }}</div>
              <div v-if="q.options && q.options.length" class="text-xs text-gray-500">Options: {{ q.options.join(', ') }}</div>
            </div>
            <div class="flex space-x-2 flex-shrink-0">
              <button @click="editQuestion(idx)" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm hover:shadow-md">
                ✏️ Edit
              </button>
              <button @click="deleteQuestion(q.id)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm hover:shadow-md">
                🗑️ Delete
              </button>
              <div class="flex flex-col space-y-1">
                <button 
                  @click="moveQuestion(idx, 'up')" 
                  :disabled="idx === 0"
                  class="bg-gray-500 hover:bg-gray-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-2 py-1 rounded text-xs transition-colors"
                >
                  ↑
                </button>
                <button 
                  @click="moveQuestion(idx, 'down')" 
                  :disabled="idx === questions.length - 1"
                  class="bg-gray-500 hover:bg-gray-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-2 py-1 rounded text-xs transition-colors"
                >
                  ↓
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Add/Edit Question Modal -->
        <div v-if="showAdd || editIdx !== null" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg relative">
            <h3 class="text-lg font-bold mb-4">{{ editIdx !== null ? 'Edit' : 'Add' }} Question</h3>
            <form @submit.prevent="handleSave">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                <input v-model="form.text" type="text" class="w-full px-3 py-2 border rounded" />
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select v-model="form.type" class="w-full px-3 py-2 border rounded">
                  <option value="text">Text</option>
                  <option value="number">Number</option>
                  <option value="select">Select</option>
                  <option value="radio">Radio</option>
                  <option value="checkbox">Checkbox</option>
                </select>
              </div>
              <div class="mb-4" v-if="['select','radio','checkbox'].includes(form.type)">
                <label class="block text-sm font-medium text-gray-700 mb-1">Options (comma separated)</label>
                <input v-model="form.options" type="text" class="w-full px-3 py-2 border rounded" placeholder="Option 1, Option 2, Option 3" />
              </div>
              <div class="mb-4">
                <label class="inline-flex items-center">
                  <input v-model="form.required" type="checkbox" class="form-checkbox" />
                  <span class="ml-2">Required</span>
                </label>
              </div>
              <div class="flex justify-end space-x-2">
                <button type="button" @click="closeModal" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const surveyId = route.params.id
const loading = ref(true)
const questions = ref([])
const surveyTitle = ref('')
const showAdd = ref(false)
const editIdx = ref(null)
const form = ref({ text: '', type: 'text', required: false, options: '' })
const analytics = ref(null)

const fetchQuestions = async () => {
  loading.value = true
  try {
    const [questionsRes, analyticsRes] = await Promise.all([
      api.get(`/surveys/${surveyId}`),
      api.get(`/responses/analytics?survey_id=${surveyId}`)
    ])
    
    console.log('Questions response:', questionsRes.data)
    
    surveyTitle.value = questionsRes.data.data.title
    questions.value = (questionsRes.data.data.questions || []).map(q => ({
      ...q,
      options: Array.isArray(q.options) ? q.options : [],
    }))
    
    console.log('Processed questions:', questions.value)
    
    analytics.value = analyticsRes.data.data
  } catch (e) {
    console.error('Error fetching questions:', e.response?.data || e.message)
    alert('Error loading questions: ' + (e.response?.data?.message || e.message))
  } finally {
    loading.value = false
  }
}

const closeModal = () => {
  showAdd.value = false
  editIdx.value = null
  form.value = { text: '', type: 'text', required: false, options: '' }
}

const handleSave = async () => {
  const payload = {
    text: form.value.text,
    type: form.value.type,
    required: form.value.required ? 1 : 0,
    options: ['select','radio','checkbox'].includes(form.value.type)
      ? form.value.options.split(',').map(o => o.trim()).filter(Boolean)
      : [],
  }

  try {
    if (editIdx.value !== null) {
      // Update question
      const q = questions.value[editIdx.value]
      await api.put(`/questions/${q.id}/update`, payload)
      alert('Question updated successfully!')
    } else {
      // Add question
      await api.post(`/surveys/${surveyId}/questions/create`, payload)
      alert('Question added successfully!')
    }
    await fetchQuestions()
    closeModal()
  } catch (e) {
    console.error('Error saving question:', e.response?.data || e.message)
    alert('Error saving question: ' + (e.response?.data?.message || e.message))
  }
}

const editQuestion = (idx) => {
  console.log('Edit question clicked:', idx, questions.value[idx])
  editIdx.value = idx
  const q = questions.value[idx]
  
  // Map backend types to frontend types
  const getFrontendType = (backendType) => {
    switch (backendType) {
      case 'text':
        return 'text'
      case 'single_choice':
        return 'radio'
      case 'multiple_choice':
        return 'checkbox'
      case 'rating':
        return 'select'
      default:
        return 'text'
    }
  }
  
  form.value = {
    text: q.text,
    type: getFrontendType(q.type),
    required: q.required,
    options: (q.options || []).join(', ')
  }
  showAdd.value = false
}

const deleteQuestion = async (id) => {
  console.log('Delete question clicked:', id)
  const question = questions.value.find(q => q.id === id)
  const questionText = question ? question.text.substring(0, 50) + (question.text.length > 50 ? '...' : '') : 'this question'
  
  if (!confirm(`Are you sure you want to delete "${questionText}"? This action cannot be undone.`)) return
  try {
    await api.post(`/questions/${id}/delete`)
    alert('Question deleted successfully!')
    await fetchQuestions()
  } catch (e) {
    console.error('Error deleting question:', e.response?.data || e.message)
    alert('Error deleting question: ' + (e.response?.data?.message || e.message))
  }
}

const moveQuestion = async (idx, direction) => {
  if (direction === 'up' && idx === 0) return
  if (direction === 'down' && idx === questions.value.length - 1) return
  
  const newQuestions = [...questions.value]
  const targetIdx = direction === 'up' ? idx - 1 : idx + 1
  
  // Swap questions
  const temp = newQuestions[idx]
  newQuestions[idx] = newQuestions[targetIdx]
  newQuestions[targetIdx] = temp
  
  try {
    const order = newQuestions.map(q => q.id)
    await api.post(`/surveys/${surveyId}/questions/reorder`, { order })
    await fetchQuestions()
  } catch (e) {
    console.error('Error reordering questions:', e.response?.data || e.message)
    alert('Error reordering questions: ' + (e.response?.data?.message || e.message))
  }
}

const getTypeText = (type) => {
  const typeMap = {
    'text': 'Text',
    'number': 'Number',
    'select': 'Select',
    'radio': 'Radio',
    'checkbox': 'Checkbox',
    'multiple_choice': 'Multiple Choice',
    'single_choice': 'Single Choice',
    'rating': 'Rating'
  }
  return typeMap[type] || type
}

onMounted(() => {
  fetchQuestions()
})
</script> 