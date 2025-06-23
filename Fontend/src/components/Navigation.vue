<template>
  <nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <router-link to="/book" class="flex items-center gap-2">
          <span class="text-2xl font-bold text-blue-700">Library</span>
        </router-link>

        <!-- Hamburger Icon (Mobile) -->
        <button
          @click="isOpen = !isOpen"
          class="sm:hidden text-gray-700 focus:outline-none"
        >
          <span class="material-symbols-outlined text-3xl">menu</span>
        </button>

        <!-- Navigation Links (Desktop) -->
        <div class="hidden sm:flex items-center gap-6">
          <NavigationItem
            v-for="item in navItems"
            :key="item.label"
            :item="item"
          />
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div v-if="isOpen" class="sm:hidden border-t">
      <div class="px-4 py-2 space-y-2">
        <NavigationItem
          v-for="item in navItems"
          :key="item.label"
          :item="item"
        />
      </div>
    </div>
  </nav>
</template>

<script setup>
import NavigationItem from "./NavigationItem.vue";
import { ref } from "vue";

const isOpen = ref(false);

const navItems = ref([
  { label: "Book", path: "/book", icon: "menu_book" },
  { label: "Author", path: "/author", icon: "person" },
  { label: "Member", path: "/member", icon: "groups" },
]);
</script>
