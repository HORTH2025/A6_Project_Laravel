<template>
  <nav class="no-swipe h-16 flex w-10/12 m-auto items-center justify-between">
    <!-- Left: Navigation Items -->
    <div class="flex items-center gap-12">
      <!-- Logo -->
      <router-link to="/Book" class="flex items-center gap-2">
        <span class="text-2xl font-bold text-blue-700">Library</span>
      </router-link>

      <!-- Navigation Items -->
      <NavigationItem
        v-for="item in navItems"
        :key="item.label"
        :item="item" class="text-right"
      />
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

</script>

