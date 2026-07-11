<script setup lang="ts">
import { ref } from 'vue'
import Menubar from 'primevue/menubar'

const items = ref([
  { label: 'Dashboard', icon: 'pi pi-home', to: '/' },
  { label: 'Pages', icon: 'pi pi-file', to: '/pages' },
  { label: 'Posts', icon: 'pi pi-book', to: '/posts' },
  { label: 'Users', icon: 'pi pi-users', to: '/users' },
  { label: 'Settings', icon: 'pi pi-cog', to: '/settings' },
])
</script>

<template>
  <div class="flex h-screen">
    <aside class="w-60 bg-surface-800 text-white p-4 flex flex-col">
      <h2 class="text-xl font-bold mb-6">CMS Admin</h2>
      <Menubar :model="items" class="flex-column border-none bg-transparent" />
    </aside>
    <main class="flex-1 p-6 overflow-auto">
      <router-view />
    </main>
  </div>
</template>
