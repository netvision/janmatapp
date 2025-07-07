import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/user'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/debug',
    name: 'Debug',
    component: () => import('@/views/Debug.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/surveys',
    name: 'Surveys',
    component: () => import('@/views/Surveys.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/surveys/create',
    name: 'CreateSurvey',
    component: () => import('@/views/CreateSurvey.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/surveys/:id',
    name: 'EditSurvey',
    component: () => import('@/views/EditSurvey.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/surveys/:id/questions',
    name: 'SurveyQuestions',
    component: () => import('@/views/SurveyQuestions.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/responses',
    name: 'Responses',
    component: () => import('@/views/Responses.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/responses/:id',
    name: 'ResponseDetail',
    component: () => import('@/views/ResponseDetail.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/agents',
    name: 'Agents',
    component: () => import('@/views/Agents.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/agents/create',
    name: 'CreateAgent',
    component: () => import('@/views/CreateAgent.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/agents/:id',
    name: 'EditAgent',
    component: () => import('@/views/EditAgent.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const userStore = useUserStore()
  
  if (to.meta.requiresAuth && !userStore.isAuthenticated) {
    next('/login')
  } else if (to.path === '/login' && userStore.isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router 