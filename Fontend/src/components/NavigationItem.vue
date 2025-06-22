<template>
  <router-link
    :to="item.path"
    class="text-gray-600 hover:text-blue-600 transition-all flex items-center gap-1 text-sm font-medium"
  >
    <span class="material-symbols-outlined text-base">{{ item.icon }}</span>
    <span>{{ item.label }}</span>
  </router-link>
</template>

<script setup>
defineProps(["item"]);
</script>
