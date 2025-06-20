<template>
  <header class="shadow-md">
    <Navigation />
  </header>
  <main class="py-8">
    <router-view />
  </main>
</template>

<script setup>
import Navigation from "@/components/Navigation.vue";
</script>
