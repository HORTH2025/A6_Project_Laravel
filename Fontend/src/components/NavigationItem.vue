<template>
  <router-link
    :to="item.path"
    class="flex flex-col items-center gap-1 text-gray-700 hover:text-blue-600 sm:flex-row sm:items-center"
  >
    <span class="material-symbols-outlined">{{ item.icon }}</span>
    <span class="font-medium">{{ item.label }}</span>
  </router-link>
</template>

<script setup>
defineProps(["item"]);
</script>
