<template>
  <div class="">
    <main class="">
      <h1 class="text-3xl font-bold text-gray-900 text-center mb-10 tracking-tight">
        Author Management
      </h1>

      <!-- Search and Add Author Button -->
      <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 max-w-4xl mx-auto"
      >
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name or nationality"
          class="w-full sm:w-1/2 border border-gray-300 rounded-lg p-3 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          aria-label="Search authors"
        />
        <button
          @click="openCreateForm"
          class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white text-sm font-semibold rounded-lg px-5 py-3 shadow-md transition"
        >
          <i class="fas fa-plus mr-2"></i> Add Author
        </button>
      </div>

      <!-- Author Table -->
      <div
        class="overflow-x-auto  rounded-lg  border border-gray-200 mx-10" 
      >
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"
              >
                #
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"
              >
                Name
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"
              >
                Nationality
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide"
              >
                Books Written
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"
              >
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="(author, index) in filteredAuthors"
              :key="author.id"
              class="hover:bg-blue-50 transition"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ author.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ author.nationality }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                {{ author.writen_book }}
              </td>
              <td class="relative px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                <button
                  @click="toggleDropdown(author.id)"
                  class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 transition"
                  aria-label="Open actions menu"
                  title="Actions"
                >
                  <i class="fas fa-ellipsis-v text-gray-600"></i>
                </button>

                <div
                  v-if="openDropdown === author.id"
                  class="absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                >
                  <div class="py-1">
                    <button
                      @click="() => { viewAuthor(author.id); closeDropdown(); }"
                      class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition"
                    >
                      <i class="fas fa-eye"></i> Show
                    </button>
                    <button
                      @click="() => { openEditForm(author); closeDropdown(); }"
                      class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition"
                    >
                      <i class="fas fa-edit"></i> Edit
                    </button>
                    <button
                      @click="() => { deleteAuthor(author.id); closeDropdown(); }"
                      class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-100 transition"
                    >
                      <i class="fas fa-trash"></i> Delete
                    </button>
                  </div>
                </div>
              </td>
            </tr>

            <tr v-if="filteredAuthors.length === 0">
              <td colspan="5" class="text-center py-6 text-gray-400 italic">
                No authors found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Create/Edit Author Modal -->
      <div
        v-if="showForm"
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title"
      >
        <div
          class="bg-white rounded-lg shadow-xl max-w-lg w-full p-8 relative"
          @keydown.escape.prevent="closeForm"
        >
          <!-- Close Button -->
          <button
            @click="closeForm"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition"
            aria-label="Close form"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <h2
            id="modal-title"
            class="text-2xl font-semibold text-gray-900 mb-6 text-center"
          >
            {{ isEdit ? "Edit Author" : "Add New Author" }}
          </h2>

          <form @submit.prevent="submitForm" class="space-y-5">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Name
              </label>
              <input
                v-model="form.name"
                id="name"
                type="text"
                placeholder="Enter author name"
                required
                class="w-full rounded-md border border-gray-300 px-4 py-3 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              />
            </div>

            <div>
              <label
                for="nationality"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Nationality
              </label>
              <input
                v-model="form.nationality"
                id="nationality"
                type="text"
                placeholder="Enter nationality"
                required
                class="w-full rounded-md border border-gray-300 px-4 py-3 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              />
            </div>

            <div>
              <label
                for="writen_book"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Books Written
              </label>
              <input
                v-model.number="form.writen_book"
                id="writen_book"
                type="number"
                min="0"
                placeholder="Enter number of books"
                required
                class="w-full rounded-md border border-gray-300 px-4 py-3 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              />
            </div>

            <div class="flex justify-end gap-4 mt-6">
              <button
                type="button"
                @click="closeForm"
                class="px-6 py-3 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-6 py-3 rounded-md bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
              >
                {{ isEdit ? "Save" : "Create" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";

const API_URL = "http://192.168.108.14:8000/api/authors";

const authors = ref([]);
const showForm = ref(false);
const isEdit = ref(false);
const selectedId = ref(null);
const searchQuery = ref("");
const openDropdown = ref(null);

const form = ref({
  name: "",
  nationality: "",
  writen_book: 0,
});

const fetchAuthors = async () => {
  try {
    const res = await axios.get(API_URL);
    authors.value = res.data.data;
  } catch (err) {
    console.error("Error fetching authors:", err);
  }
};

const filteredAuthors = computed(() => {
  const q = searchQuery.value.toLowerCase().trim();
  if (!q) return authors.value;
  return authors.value.filter(
    (a) =>
      a.name.toLowerCase().includes(q) ||
      a.nationality.toLowerCase().includes(q)
  );
});

const openCreateForm = () => {
  isEdit.value = false;
  form.value = { name: "", nationality: "", writen_book: 0 };
  showForm.value = true;
};

const openEditForm = (author) => {
  isEdit.value = true;
  selectedId.value = author.id;
  form.value = { ...author };
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  selectedId.value = null;
};

const submitForm = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`${API_URL}/edit/${selectedId.value}`, form.value);
    } else {
      await axios.post(`${API_URL}/create`, form.value);
    }
    await fetchAuthors();
    closeForm();
  } catch (err) {
    console.error("Error saving author:", err);
  }
};

const deleteAuthor = async (id) => {
  if (confirm("Are you sure you want to delete this author?")) {
    try {
      await axios.delete(`${API_URL}/delete/${id}`);
      await fetchAuthors();
    } catch (err) {
      console.error("Error deleting author:", err);
    }
  }
};

const viewAuthor = async (id) => {
  try {
    const res = await axios.get(`${API_URL}/${id}`);
    const a = res.data.data;
    alert(
      `📘 Name: ${a.name}\n🌍 Nationality: ${a.nationality}\n📖 Books Written: ${a.writen_book}`
    );
  } catch (err) {
    console.error("Error fetching author details:", err);
  }
};

const toggleDropdown = (id) => {
  openDropdown.value = openDropdown.value === id ? null : id;
};

const closeDropdown = () => {
  openDropdown.value = null;
};

onMounted(fetchAuthors);
</script>

<style scoped>
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css");
</style>
