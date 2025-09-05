<template>
  <div v-if="visible" class="fixed inset-0 z-50 flex items-start justify-center pt-20">
    <div class="fixed inset-0 bg-black opacity-40" @click="close"></div>
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[80vh] overflow-y-auto relative z-50 p-6">
      <div class="flex items-start justify-between mb-4">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">{{ analytics?.title || 'Survey Analytics' }}</h3>
            <p class="text-sm text-gray-600">
              <router-link v-if="surveyId" :to="{ path: '/responses', query: { survey_id: surveyId } }" @click="close" class="text-blue-600 hover:underline">
                {{ analytics?.total_responses || 0 }}
              </router-link>
              <span v-else>{{ analytics?.total_responses || 0 }}</span>
              &nbsp;total responses
            </p>
        </div>
        <div class="flex items-center space-x-2">
          <button @click="close" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <div v-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-3 text-gray-600">Loading analytics...</p>
      </div>

      <div v-else-if="analytics && analytics.questions && analytics.questions.length > 0" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="question in analytics.questions" :key="question.question_id" class="border rounded-lg p-4 bg-gray-50">
            <h4 class="font-medium text-gray-900 mb-2">{{ question.question_text }}</h4>
            <div class="text-xs text-gray-500 mb-3">
              <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ formatType(question.question_type) }}</span>
              <span class="ml-2">{{ question.total_answers }} responses</span>
            </div>

            <div v-if="isChoiceQuestion(question.question_type)">
              <div v-if="Object.keys(question.answers || {}).length > 0" class="flex items-center justify-center mb-3">
                <PieChart :data="Object.values(question.answers)" :labels="Object.keys(question.answers)" :colors="pieColors" :width="180" :height="180" />
              </div>
              <div v-if="Object.keys(question.answers || {}).length > 0" class="space-y-2">
                <div v-for="(count, option, idx) in question.answers" :key="option" class="flex items-center justify-between p-2 bg-white rounded border">
                  <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-3" :style="{ backgroundColor: pieColors[idx % pieColors.length] }"></div>
                    <div class="text-sm text-gray-700 truncate">{{ option }}</div>
                  </div>
                  <div class="text-sm text-gray-900">{{ count }} ({{ ((count / question.total_answers) * 100).toFixed(1) }}%)</div>
                </div>
              </div>
              <div v-else class="text-gray-500 text-sm">No responses yet</div>
            </div>

            <div v-else>
              <div v-if="Object.keys(question.text_answers || {}).length > 0" class="space-y-2">
                <div v-for="(count, answer) in question.text_answers" :key="answer" class="flex items-center justify-between p-2 bg-white rounded border">
                  <div class="text-sm text-gray-700 truncate">{{ answer }}</div>
                  <div class="text-sm text-gray-900">{{ count }}</div>
                </div>
              </div>
              <div v-else class="text-gray-500 text-sm">No textual responses yet</div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-8 text-gray-500">
        No analytics available for this survey.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import PieChart from '@/components/PieChart.vue'
import api from '@/services/api'

const props = defineProps({
  surveyId: [String, Number],
  visible: { type: Boolean, default: false }
})

const emit = defineEmits(['close'])

const analytics = ref(null)
const loading = ref(false)

const pieColors = [
  '#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#06B6D4','#EC4899','#84CC16'
]

const isChoiceQuestion = (type) => {
  return ['single_choice','multiple_choice','rating'].includes(type)
}

const formatType = (type) => type ? type.replace('_',' ').toUpperCase() : ''

const fetchAnalytics = async (id) => {
  if (!id) return
  loading.value = true
  try {
    const res = await api.get(`/responses/analytics?survey_id=${id}`)
    analytics.value = res.data.data
  } catch (e) {
    console.error('Error fetching survey analytics:', e)
    analytics.value = null
  } finally {
    loading.value = false
  }
}

watch(() => props.visible, (v) => {
  if (v && props.surveyId) {
    fetchAnalytics(props.surveyId)
  }
})

onMounted(() => {
  if (props.visible && props.surveyId) fetchAnalytics(props.surveyId)
})

const close = () => emit('close')
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity .15s }
.modal-enter-from, .modal-leave-to { opacity: 0 }
</style>
