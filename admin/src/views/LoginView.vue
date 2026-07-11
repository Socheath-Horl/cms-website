<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await auth.login(email.value, password.value)
    router.push('/')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="flex items-center justify-center h-screen bg-surface-100">
    <form @submit.prevent="handleLogin" class="bg-white p-8 rounded-xl shadow-lg w-full max-w-sm">
      <h1 class="text-2xl font-bold mb-6 text-center">CMS Admin Login</h1>

      <Message v-if="error" severity="error" class="mb-4">{{ error }}</Message>

      <div class="mb-4">
        <label for="email" class="block text-sm font-medium mb-1">Email</label>
        <InputText id="email" v-model="email" type="email" class="w-full" required />
      </div>

      <div class="mb-6">
        <label for="password" class="block text-sm font-medium mb-1">Password</label>
        <Password id="password" v-model="password" :feedback="false" class="w-full" inputClass="w-full" required />
      </div>

      <Button type="submit" label="Login" class="w-full" :loading="loading" />
    </form>
  </div>
</template>
