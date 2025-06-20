<template>
  <nav class="h-16 flex w-10/12 m-auto items-center justify-between">
    <!-- Left: Logo and Navigation -->
    <div class="flex items-center gap-12">
      <!-- Logo -->
     

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
        placeholder="Search..."
        class="border border-gray-300 px-3 py-1 rounded-md"
      />
      <button
        @click="search"
        class="bg-black text-white px-4 py-1 rounded-md "
      >
        Search
      </button>
    </div>
  </nav>
</template>

<script setup>
import NavigationItem from "./NavigationItem.vue";
import { ref } from "vue";

// Navigation Items
const navItems = ref([
  { label: "Book", path: "/book", icon: "menu_book" },
  { label: "Author", path: "/author", icon: "person" },
  { label: "Member", path: "/member", icon: "groups" },
]);

// Search Functionality
const searchQuery = ref("");

function search() {
  alert("You searched for: " + searchQuery.value);
}
</script>
