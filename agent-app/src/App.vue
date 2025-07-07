<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <div class="safe-area">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {
  // Prevent pull-to-refresh on mobile
  document.body.style.overscrollBehavior = 'none'
  
  // Handle back button on mobile
  if (window.history && window.history.pushState) {
    window.addEventListener('popstate', () => {
      if (router.currentRoute.value.name === 'login') {
        router.push('/login')
      }
    })
  }
})
</script>

<style>
#app {
  font-family: 'Inter', system-ui, sans-serif;
}
</style> 