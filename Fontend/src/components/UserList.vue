<template>
  <section class="w-10/12 m-auto">
    <h1 class="text-2xl font-semibold mb-2">All Users</h1>
    <p class="mb-6 text-gray-600">All the users registered in our system.</p>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
      <UserCard
        v-for="user in users"
        :key="user.id"
        :user="user"
        @handle="show(user)"
      />
    </div>
  </section>
</template>

<script setup>
import UserCard from "./UserCard.vue";
defineProps(["users"]);

const show = (user) => {
  console.log("User Details:", user);
};
</script>
