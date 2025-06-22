<template>
  <nav class="no-swipe h-16 flex w-10/12 m-auto items-center justify-between">
    <!-- Left: Navigation Items -->
    <div class="flex items-center gap-12">
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

const navItems = ref([
  { label: "Book", path: "/book", icon: "menu_book" },
  { label: "Author", path: "/author", icon: "person" },
  { label: "Member", path: "/member", icon: "groups" },
]);

const searchQuery = ref("");

function search() {
  alert("You searched for: " + searchQuery.value);
}
</script>

<style scoped>
/* Prevent any scroll, swipe, or bounce behavior on the nav bar */
.no-swipe {
  touch-action: none;
  overscroll-behavior: contain;
  user-select: none;
  -webkit-user-select: none;
  pointer-events: auto; /* still allow clicking buttons and typing input */
}

/* Optional: Disable text selection just on navigation bar */
nav input,
nav button {
  user-select: text;
  pointer-events: auto;
}
</style>
