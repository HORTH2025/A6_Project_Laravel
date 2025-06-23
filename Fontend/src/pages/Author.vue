<template>
  <div class="">
    <main class="">
      <h1 class="text-3xl font-bold text-gray-700 text-center mb-10 tracking-tight">
        Author Management
      </h1>

      <!-- Search and Add Author Button -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 mb-8 mx-10">
        <input v-model="searchQuery" type="text" placeholder=" 🔍 Search by name or nationality"
          class="w-full sm:w-1/2 border border-gray-300 rounded-lg p-3 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
        <button @click="openCreateForm"
          class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white text-sm font-semibold rounded-lg px-5 py-3 shadow-md transition">
          <i class="fas fa-plus mr-2"></i> Add Author
        </button>
      </div>

      <!-- Table -->
      <div class="rounded-lg border border-gray-200 mx-10">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-blue-100">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nationality</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Books Written</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(author, index) in filteredAuthors" :key="author.id" class="hover:bg-blue-50 transition">
              <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ index + 1 }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ author.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ author.nationality }}</td>
              <td class="px-6 py-4 text-sm text-gray-700 text-center">{{ author.writen_book }}</td>
              <td class="px-6 py-4 text-sm text-center relative">
                <button @click="toggleDropdown(author.id)"
                  class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 transition">
                  <i class="fas fa-ellipsis-v text-gray-600"></i>
                </button>
                <div v-if="openDropdown === author.id"
                  class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                  <button @click="() => { viewAuthor(author); closeDropdown(); }"
                    class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-eye mr-2"></i> Show
                  </button>
                  <button @click="() => { openEditForm(author); closeDropdown(); }"
                    class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-edit mr-2"></i> Edit
                  </button>
                  <button @click="() => { deleteAuthor(author.id); closeDropdown(); }"
                    class="block w-full px-4 py-2 text-sm text-red-600 hover:bg-red-100">
                  <i class="fas fa-trash mr-2"></i> Delete
                  </button>
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

      <!-- Create/Edit Form Modal -->
      <div v-if="showForm" class="fixed inset-0 bg-black/30 z-40 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-8 relative">
          <button @click="closeForm" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
          <h2 class="text-2xl font-semibold text-center mb-6">
            {{ isEdit ? "Edit Author" : "Add New Author" }}
          </h2>
          <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Name</label>
              <input v-model="form.name" type="text"
                class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required />
            </div>
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Nationality</label>
              <input v-model="form.nationality" type="text"
                class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required />
            </div>
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Books Written</label>
              <input v-model.number="form.writen_book" type="number" min="0"
                class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required />
            </div>
            <div class="flex justify-end gap-4 pt-4">
              <button type="button" @click="closeForm"
                class="bg-gray-200 text-gray-700 px-5 py-2 rounded-md hover:bg-gray-300">
                Cancel
              </button>
              <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700">
                {{ isEdit ? "Save" : "Create" }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- View Modal -->
      <div v-if="showViewModal" class="fixed inset-0 bg-black/30 z-40 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-8 relative">
          <button @click="closeViewModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
          <h2 class="text-2xl font-semibold text-center mb-6">Author Details</h2>
          <div class="space-y-3 text-gray-700 text-sm">
            <p><strong>Name:</strong> {{ viewData.name }}</p>
            <p><strong>Nationality:</strong> {{ viewData.nationality }}</p>
            <p><strong>Books Written:</strong> {{ viewData.writen_book }}</p>
            <p><strong>Created At:</strong> {{ new Date(viewData.created_at).toLocaleString() }}</p>
            <p><strong>Updated At:</strong> {{ new Date(viewData.updated_at).toLocaleString() }}</p>
          </div>
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
const form = ref({ name: "", nationality: "", writen_book: 0 });
const viewData = ref({});
const selectedId = ref(null);
const isEdit = ref(false);
const showForm = ref(false);
const showViewModal = ref(false);
const openDropdown = ref(null);
const searchQuery = ref("");

const fetchAuthors = async () => {
  const res = await axios.get(API_URL);
  authors.value = res.data.data;
};

const submitForm = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`${API_URL}/edit/${selectedId.value}`, form.value);
    } else {
      await axios.post(`${API_URL}/create`, form.value);
    }
    fetchAuthors();
    closeForm();
  } catch (err) {
    console.error("Failed to save author", err);
  }
};

const deleteAuthor = async (id) => {
  if (confirm("Are you sure?")) {
    await axios.delete(`${API_URL}/delete/${id}`);
    fetchAuthors();
  }
};

const viewAuthor = (author) => {
  viewData.value = author;
  showViewModal.value = true;
};

const openCreateForm = () => {
  form.value = { name: "", nationality: "", writen_book: 0 };
  isEdit.value = false;
  showForm.value = true;
};

const openEditForm = (author) => {
  form.value = { ...author };
  selectedId.value = author.id;
  isEdit.value = true;
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
};

const closeViewModal = () => {
  showViewModal.value = false;
};

const toggleDropdown = (id) => {
  openDropdown.value = openDropdown.value === id ? null : id;
};
const closeDropdown = () => {
  openDropdown.value = null;
};

const filteredAuthors = computed(() => {
  const q = searchQuery.value.toLowerCase();
  return authors.value.filter(
    (a) =>
      a.name.toLowerCase().includes(q) ||
      a.nationality.toLowerCase().includes(q)
  );
});

onMounted(fetchAuthors);
</script>

<style scoped>
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css");
</style>
