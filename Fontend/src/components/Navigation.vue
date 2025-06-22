<template>
  <nav class="h-16 flex w-10/12 m-auto items-center justify-between">
    <!-- Left: Logo and Navigation -->
    <div class="flex items-center gap-12">
      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-2">
        <span class="text-2xl font-bold">Library</span>
      </router-link>

      <!-- Navigation Items -->
      <NavigationItem
        v-for="item in navItems"
        :key="item.label"
        :item="item"
      />
    </div>

    <!-- Right: Search Input and Button -->
    <div class="flex items-center gap-2">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search authors..."
        class="border border-gray-300 px-3 py-1 rounded-md"
        @keyup.enter="searchAuthors"
      />
      <button
        @click="searchAuthors"
        class="bg-black text-white px-4 py-1 rounded-md"
      >
        Search
      </button>
    </div>
  </nav>
</template>

<script setup>
import NavigationItem from "./NavigationItem.vue";
import { ref } from "vue";
import { useRouter } from 'vue-router';

const router = useRouter();

// Navigation Items
const navItems = ref([
  { label: "Book", path: "/book", icon: "menu_book" },
  { label: "Author", path: "/author", icon: "person" },
  { label: "Member", path: "/member", icon: "groups" },
]);

// Search Functionality
const searchQuery = ref("");

function searchAuthors() {
  if (router.currentRoute.value.path === '/author') {
    // If we're already on the author page, navigate with query params
    router.push({
      path: '/author',
      query: { search: searchQuery.value }
    });
  } else {
    // If we're on another page, navigate to author page with search query
    router.push({
      path: '/author',
      query: { search: searchQuery.value }
    });
  }
}
</script>