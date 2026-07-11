import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'
import type { User } from '../types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))

  const isAuthenticated = computed(() => !!token.value && !!user.value)

  async function login(email: string, password: string) {
    const { data } = await api.post('/auth/login', { email, password })
    token.value = data.token
    user.value = data.user
    localStorage.setItem('auth_token', data.token)
  }

  async function logout() {
    await api.post('/auth/logout')
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
  }

  async function fetchUser() {
    if (!token.value) return
    const { data } = await api.get('/auth/user')
    user.value = data
  }

  return { user, token, isAuthenticated, login, logout, fetchUser }
})
