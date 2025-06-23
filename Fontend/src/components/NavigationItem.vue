<template>
  <router-link
    :to="item.path"
    class="flex items-center gap-2 text-sm font-medium px-3 py-2 transition-all relative"
    :class="isActive
      ? 'text-blue-700 border-b-2 border-blue-700'
      : 'text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-300 border-b-2 border-transparent'"
  >
    <span class="material-symbols-outlined text-base">{{ item.icon }}</span>
    <span>{{ item.label }}</span>
  </router-link>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const props = defineProps(['item'])
const route = useRoute()

const isActive = computed(() => route.path.startsWith(props.item.path))
</script>
