import { createApp } from 'vue'
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'
import { createPinia } from 'pinia'
import router from './router'
import api from './services/api'
import './style.css'
import App from './App.vue'

const app = createApp(App)
app.use(PrimeVue, { theme: { preset: Aura } })
app.use(createPinia())
app.use(router)
app.mount('#app')

api.get('/sanctum/csrf-cookie').then(() => {
  return api.get('/settings')
}).then((res) => {
  console.log('Connected!', res.data)
}).catch((err) => {
  console.error('API error:', err)
})
