<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="px-4 py-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900">Survey</h1>
        <button @click="router.back()" class="btn btn-secondary text-sm">Back</button>
      </div>
    </header>
    <main class="px-4 py-6">
      <div v-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-600">Loading survey...</p>
      </div>
      <div v-else-if="!survey" class="text-center py-8">
        <p class="text-gray-600">Survey not found</p>
      </div>
      <div v-else>
        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ survey.title }}</h2>
        <p class="text-gray-600 mb-4">{{ survey.description }}</p>
        
        <!-- Respondent Information Section -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
          <h3 class="text-sm font-medium text-green-900 mb-3">Respondent Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Respondent Name</label>
              <input 
                v-model="respondentInfo.name" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="Enter respondent's name"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
              <input 
                v-model="respondentInfo.phone" 
                type="tel" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                placeholder="Enter phone number"
              />
            </div>
          </div>
        </div>

       

        <!-- Voice Recording Section -->
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-purple-900">Voice Response</h3>
            <div class="flex items-center space-x-2">
              <!-- Location Access Badge -->
              <div v-if="locationPermissionGranted" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                ✓ Location Access
              </div>
              <!-- Microphone Access Badge -->
              <div v-if="microphonePermissionGranted" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                ✓ Microphone Access
              </div>
              <div v-else class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">
                ✗ Microphone Access
              </div>
            </div>
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600">Record voice response</span>
              <div class="flex space-x-2">
                <button 
                  v-if="!isRecording && !audioBlob && microphonePermissionGranted"
                  @click="startRecording"
                  type="button"
                  class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm transition-colors"
                >
                  Start Recording
                </button>
                <button 
                  v-if="!isRecording && !audioBlob && !microphonePermissionGranted"
                  @click="requestMicrophonePermission"
                  type="button"
                  class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm transition-colors"
                >
                  Grant Microphone Permission
                </button>
                <button 
                  v-if="isRecording"
                  @click="stopRecording"
                  type="button"
                  class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm transition-colors"
                >
                  Stop Recording
                </button>
                <button 
                  v-if="audioBlob && !isRecording"
                  @click="startRecording"
                  type="button"
                  class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm transition-colors"
                >
                  Re-record
                </button>
              </div>
            </div>
            
            <div v-if="isRecording" class="flex items-center space-x-2">
              <div class="animate-pulse w-3 h-3 bg-red-500 rounded-full"></div>
              <span class="text-sm text-red-600">Recording...</span>
              <span class="text-sm text-gray-500">({{ recordingDuration }}s)</span>
            </div>
            
            <div v-if="audioBlob && !isRecording" class="space-y-2">
              <audio controls class="w-full">
                <source :src="audioUrl" type="audio/wav">
                Your browser does not support the audio element.
              </audio>
              <button 
                @click="clearRecording"
                type="button"
                class="text-sm text-red-600 hover:text-red-700"
              >
                Clear recording
              </button>
            </div>
            
            <div v-if="recordingError" class="text-sm text-red-600">
              {{ recordingError }}
              <button 
                v-if="!microphonePermissionGranted"
                @click="requestMicrophonePermission"
                class="ml-2 text-purple-600 hover:text-purple-700 underline"
              >
                Grant Permission
              </button>
            </div>
          </div>
        </div>
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div v-for="q in survey.questions" :key="q.id" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ q.text }} <span v-if="q.required" class="text-red-500">*</span></label>
            <component :is="getInputComponent(q)" v-model="response[q.id]" :question="q" />
          </div>
          <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-3">
            <p class="text-sm text-red-600">{{ error }}</p>
          </div>
          <div v-if="success" class="bg-green-50 border border-green-200 rounded-lg p-3">
            <p class="text-sm text-green-700">
              Response submitted successfully! 
              <span v-if="currentLocation?.address" class="block mt-1 text-xs text-green-600">
                Location: {{ currentLocation.address }}
              </span>
            </p>
          </div>
          <button type="submit" class="w-full btn btn-primary" :disabled="submitting">
            <span v-if="submitting">Submitting...</span>
            <span v-else-if="success">Next Response</span>
            <span v-else>Submit Response</span>
          </button>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, defineAsyncComponent } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import { getCurrentLocation } from '@/utils/getLocation'
import { Capacitor } from '@capacitor/core'
import { Geolocation } from '@capacitor/geolocation'
import { VoiceRecorder } from 'capacitor-voice-recorder'
import { Filesystem, Directory } from '@capacitor/filesystem'

const route = useRoute()
const router = useRouter()
const survey = ref(null)
const loading = ref(false)
const submitting = ref(false)
const error = ref('')
const success = ref(false)
const response = ref({})
const location = ref(null)
const locationLoading = ref(false)
const locationError = ref('')
const currentLocation = ref(null)
const respondentInfo = ref({
  name: '',
  phone: ''
})

// Voice recording variables
const isRecording = ref(false)
const audioBlob = ref(null)
const audioUrl = ref('')
const recordingDuration = ref(0)
const recordingError = ref('')
const mediaRecorder = ref(null)
const recordingTimer = ref(null)

// Permission states
const locationPermissionGranted = ref(false)
const microphonePermissionGranted = ref(false)

// Request location permission
const requestLocationPermission = async () => {
  if (!Capacitor.isNativePlatform()) {
    // On web, permissions are handled by the browser
    locationPermissionGranted.value = true
    return true
  }

  try {
    const permission = await Geolocation.checkPermissions()
    
    if (permission.location === 'granted') {
      locationPermissionGranted.value = true
      return true
    } else if (permission.location === 'prompt') {
      const request = await Geolocation.requestPermissions()
      if (request.location === 'granted') {
        locationPermissionGranted.value = true
        return true
      } else {
        locationError.value = 'Location permission denied. Please enable location access in settings.'
        return false
      }
    } else {
      locationError.value = 'Location permission denied. Please enable location access in settings.'
      return false
    }
  } catch (error) {
    console.error('Error requesting location permission:', error)
    locationError.value = 'Could not request location permission.'
    return false
  }
}

// Request microphone permission
const requestMicrophonePermission = async () => {
  try {
    
    // Check if device can record voice
    const canRecord = await VoiceRecorder.canDeviceVoiceRecord()
    
    if (!canRecord.value) {
      recordingError.value = 'This device cannot record audio.'
      return false
    }
    
    // Request permission
    const permission = await VoiceRecorder.requestAudioRecordingPermission()
    
    if (permission.value) {
      microphonePermissionGranted.value = true
      return true
    } else {
      recordingError.value = 'Microphone permission denied. Please enable microphone access in settings.'
      return false
    }
  } catch (error) {
    console.error('VoiceRecorder permission error:', error)
    recordingError.value = 'Could not request microphone permission.'
    return false
  }
}

// Request all permissions
const requestPermissions = async () => {
  
  // Request location permission
  const locationGranted = await requestLocationPermission()
  
  // Request microphone permission
  const microphoneGranted = await requestMicrophonePermission()
  
  return { location: locationGranted, microphone: microphoneGranted }
}

const fetchSurvey = async () => {
  loading.value = true
  try {
    const res = await api.get(`/surveys/${route.params.id}`)
    if (res.data.success) {
      survey.value = res.data.data
      // Initialize response object
      response.value = {}
      for (const q of survey.value.questions) {
        if (q.type === 'multiple_choice') {
          response.value[q.id] = []
        } else {
          response.value[q.id] = ''
        }
      }
    }
  } catch (e) {
    survey.value = null
  } finally {
    loading.value = false
  }
}

const getInputComponent = (q) => {
  // Dynamically select input component based on question type
  switch (q.type) {
    case 'text':
      return defineAsyncComponent(() => import('@/components/inputs/TextInput.vue'))
    case 'number':
      return defineAsyncComponent(() => import('@/components/inputs/NumberInput.vue'))
    case 'single_choice':
      return defineAsyncComponent(() => import('@/components/inputs/RadioInput.vue'))
    case 'multiple_choice':
      return defineAsyncComponent(() => import('@/components/inputs/CheckboxInput.vue'))
    case 'select':
    case 'rating':
      return defineAsyncComponent(() => import('@/components/inputs/SelectInput.vue'))
    default:
      return defineAsyncComponent(() => import('@/components/inputs/TextInput.vue'))
  }
}

const getLocation = async () => {
  locationLoading.value = true
  locationError.value = ''
  
  // Check if location permission is granted
  if (!locationPermissionGranted.value) {
    locationError.value = 'Location permission not granted. Please grant location permission first.'
    locationLoading.value = false
    return
  }
  
  try {
    const loc = await getCurrentLocation()
    location.value = loc
    // Try to get address from coordinates (reverse geocoding)
    if (loc.latitude && loc.longitude) {
      try {
        const response = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=json&lat=${loc.latitude}&lon=${loc.longitude}&zoom=18&addressdetails=1`
        )
        const data = await response.json()
        if (data.display_name) {
          location.value.address = data.display_name
        }
      } catch (e) {
        // Ignore address error
      }
    }
    locationLoading.value = false
  } catch (e) {
    locationError.value = e.message || 'Could not get location.'
    locationLoading.value = false
  }
}

// Voice recording functions
const startRecording = async () => {
  try {
    recordingError.value = ''
    
    // Check if microphone permission is granted
    if (!microphonePermissionGranted.value) {
      recordingError.value = 'Microphone permission not granted. Please grant microphone permission first.'
      return
    }
    
    // Start recording with file save option for better performance
    const result = await VoiceRecorder.startRecording({
      directory: Directory.Data,
      subDirectory: 'recordings'
    })
    
    if (result.value) {
      isRecording.value = true
      recordingDuration.value = 0
      
      // Start timer
      recordingTimer.value = setInterval(() => {
        recordingDuration.value++
      }, 1000)
    } else {
      recordingError.value = 'Failed to start recording.'
    }
    
  } catch (error) {
    console.error('Recording error:', error)
    recordingError.value = 'Could not start recording: ' + error.message
  }
}

const stopRecording = async () => {
  if (!isRecording.value) return
  
  try {
    const result = await VoiceRecorder.stopRecording()
    
    isRecording.value = false
    clearInterval(recordingTimer.value)
    if (result && (result.value.recordDataBase64 || result.value.path)) {
      
      if (result.value.path) {
        // File was saved to filesystem, read it
        try {
          const fileData = await Filesystem.readFile({
            directory: Directory.Data,
            path: result.value.path
          })
          
          // Convert base64 to blob
          const byteCharacters = atob(fileData.data)
          const byteNumbers = new Array(byteCharacters.length)
          for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i)
          }
          const byteArray = new Uint8Array(byteNumbers)
          
          audioBlob.value = new Blob([byteArray], { type: result.value.mimeType || 'audio/aac' })
          audioUrl.value = URL.createObjectURL(audioBlob.value)
        } catch (error) {
          console.error('Error reading audio file:', error)
          recordingError.value = 'Error reading recorded audio file.'
        }
      } else if (result.value.recordDataBase64) {
        // Base64 data directly
        const byteCharacters = atob(result.value.recordDataBase64)
        const byteNumbers = new Array(byteCharacters.length)
        for (let i = 0; i < byteCharacters.length; i++) {
          byteNumbers[i] = byteCharacters.charCodeAt(i)
        }
        const byteArray = new Uint8Array(byteNumbers)
        
        audioBlob.value = new Blob([byteArray], { type: result.value.mimeType || 'audio/aac' })
        audioUrl.value = URL.createObjectURL(audioBlob.value)
      }
    } else {
      console.error('No recording data returned')
      recordingError.value = 'Recording failed - no data received.'
    }
  } catch (error) {
    console.error('Error stopping recording:', error)
    recordingError.value = 'Error stopping recording: ' + error.message
    isRecording.value = false
    clearInterval(recordingTimer.value)
  }
}

const clearRecording = () => {
  audioBlob.value = null
  audioUrl.value = ''
  recordingDuration.value = 0
  recordingError.value = ''
}

// Debug function to check audio blob
const debugAudioBlob = () => {
  console.log('Audio blob info:', {
    exists: !!audioBlob.value,
    size: audioBlob.value?.size,
    type: audioBlob.value?.type,
    url: audioUrl.value
  })
}

// Debug function to check VoiceRecorder methods
const debugVoiceRecorder = () => {
  console.log('VoiceRecorder methods:', Object.getOwnPropertyNames(VoiceRecorder))
  console.log('VoiceRecorder prototype:', Object.getOwnPropertyNames(Object.getPrototypeOf(VoiceRecorder)))
}

const handleSubmit = async () => {
  error.value = ''
  success.value = false
  submitting.value = true
  try {
    // Validate required fields
    for (const q of survey.value.questions) {
      if (q.required && (!response.value[q.id] || (Array.isArray(response.value[q.id]) && response.value[q.id].length === 0))) {
        error.value = `Please answer: ${q.text}`
        submitting.value = false
        return
      }
    }
    
            // Get fresh GPS coordinates before submission
        currentLocation.value = location.value
        if (locationPermissionGranted.value) {
          try {
            console.log('Getting fresh GPS coordinates for submission...')
            const freshLocation = await getCurrentLocation()
            currentLocation.value = freshLocation
            
            // Try to get address from coordinates (reverse geocoding)
            if (freshLocation.latitude && freshLocation.longitude) {
              try {
                const response = await fetch(
                  `https://nominatim.openstreetmap.org/reverse?format=json&lat=${freshLocation.latitude}&lon=${freshLocation.longitude}&zoom=18&addressdetails=1`
                )
                const data = await response.json()
                if (data.display_name) {
                  currentLocation.value.address = data.display_name
                }
              } catch (e) {
                // Ignore address error
              }
            }
          } catch (e) {
            console.error('Error getting fresh location:', e)
            // Use existing location if fresh location fails
          }
        }
    
    // Submit response
    const payload = {
      survey_id: survey.value.id,
      respondent_name: respondentInfo.value.name,
      respondent_phone: respondentInfo.value.phone,
      latitude: currentLocation.value?.latitude || null,
      longitude: currentLocation.value?.longitude || null,
      location_address: currentLocation.value?.address || null,
      answers: Object.entries(response.value).map(([question_id, value]) => ({
        question_id,
        value: Array.isArray(value) ? JSON.stringify(value) : value
      }))
    }

    // Add voice recording if available
    debugAudioBlob() // Debug the audio blob before submission
    if (audioBlob.value) {
      const formData = new FormData()
      // Add all payload fields as top-level fields
      Object.entries(payload).forEach(([key, value]) => {
        if (Array.isArray(value) || typeof value === 'object') {
          formData.append(key, JSON.stringify(value))
        } else {
          formData.append(key, value)
        }
      })
      
      // Add the audio file - use the blob we created in stopRecording
      if (audioBlob.value) {
        // Determine file extension based on mime type
        const fileExtension = audioBlob.value.type.includes('aac') ? 'aac' : 
                             audioBlob.value.type.includes('webm') ? 'webm' : 
                             audioBlob.value.type.includes('mp4') ? 'mp4' : 'wav'
        
        formData.append('voice_recording', audioBlob.value, `voice_response.${fileExtension}`)
      } else {
        console.error('No audio blob available for submission')
      }
      
      // Send as FormData instead of JSON
      const res = await api.post('/responses/submit', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      if (res.data.success) {
        success.value = true
        error.value = ''
        
        // Auto-refresh page after 5 seconds to show success message
        setTimeout(() => {
          window.location.reload()
        }, 5000)
      } else {
        error.value = res.data.message || 'Failed to submit response.'
      }
    } else {
      // Send without voice recording
      const res = await api.post('/responses/submit', payload)
      if (res.data.success) {
        success.value = true
        error.value = ''
        
        // Auto-refresh page after 5 seconds to show success message
        setTimeout(() => {
          window.location.reload()
        }, 5000)
      } else {
        error.value = res.data.message || 'Failed to submit response.'
      }
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to submit response.'
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  fetchSurvey()
  
  // Debug VoiceRecorder on native platform
  if (Capacitor.isNativePlatform()) {
    debugVoiceRecorder()
  }
  
  // Request permissions first
  const permissions = await requestPermissions()
  
  // If location permission is granted, get location
  if (permissions.location) {
    getLocation()
  }
})
</script> 