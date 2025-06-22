<template>
  <div class="border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition">
    <div>
      <h1 class="text-lg font-semibold">{{ user.name }}</h1>
      <p class="text-sm text-gray-600">{{ user.email }}</p>
    </div>
    <div class="mt-4">
      <p class="flex items-center gap-2 mb-1 text-gray-700">
        <span class="material-symbols-outlined text-base"> location_on </span>
        <span class="font-semibold">Address</span>
      </p>
      <p class="text-sm text-gray-600">
        St {{ user.address.street }}, {{ user.address.suite }}, {{ user.address.city }}
      </p>
      <button
        @click="$emit('handle')"
        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
      >
        Detail
      </button>
    </div>
  </div>
</template>
<script setup>
defineProps(["user"]);
</script>
