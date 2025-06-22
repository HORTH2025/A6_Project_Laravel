<template>
    
  <div class="p-6 space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">📚 Book Story Categories</h1>
      <button @click="openCreateForm" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg transition">
        ➕ Create Book
      </button>
    </div>

    <!-- Category Filter -->
    <div class="flex gap-2 flex-wrap">
      <button
        v-for="cat in categories"
        :key="cat"
        @click="active = cat"
        :class="[
          'px-6 py-1 rounded-full text-sm font-semibold transition',
          active === cat ? 'bg-black text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
        ]"
      >
        {{ cat }}
      </button>
    </div>

    <!-- Book Table -->
    <div class="overflow-x-auto overflow-y-auto max-h-[400px] rounded-xl shadow-lg" ref="tableWrapper">
      <table class="min-w-full text-sm text-left text-gray-800">
        <thead class="bg-blue-100 text-blue-800 uppercase text-xs font-bold">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">ISBN</th>
            <th class="px-4 py-3">Title</th>
            <th class="px-4 py-3">Author</th>
            <th class="px-4 py-3">Year</th>
            <th class="px-4 py-3">Copies</th>
            <th class="px-4 py-3">Category</th>
            <th class="px-4 py-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="book in filteredBooks" :key="book.id" class="border-b hover:bg-gray-50 transition duration-150">
            <td class="px-4 py-2">{{ book.id }}</td>
            <td class="px-4 py-2">{{ book.isbn }}</td>
            <td class="px-4 py-2">{{ book.title }}</td>
            <td class="px-4 py-2">{{ book.author_name }}</td>
            <td class="px-4 py-2">{{ book.publication_year }}</td>
            <td class="px-4 py-2">{{ book.number_of_copies }}</td>
            <td class="px-4 py-2">{{ book.category }}</td>
            <td class="px-4 py-2 relative">
              <div class="relative inline-block text-left">
                <button @click="openMenu === book.id ? openMenu = null : openMenu = book.id"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 text-xl">
                  ⋮
                </button>
                <div v-if="openMenu === book.id"
                     class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                  <div class="py-1 text-sm text-gray-700">
                    <button @click="showBook(book); openMenu = null" class="w-full text-left px-4 py-2 hover:bg-gray-100">👁 Show</button>
                    <button @click="openUpdateForm(book); openMenu = null" class="w-full text-left px-4 py-2 hover:bg-gray-100">✏️ Update</button>
                    <button @click="deleteBook(book.id); openMenu = null" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-100">🗑 Delete</button>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="formMode !== ''" class="fixed inset-0 z-40 bg-black/30 flex justify-center items-center px-4" @click.self="formMode = ''">
      <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-xl z-50 relative">
        <div v-if="formMode === 'create' || formMode === 'update'">
          <h2 class="text-xl font-bold mb-4" :class="formMode === 'create' ? 'text-blue-700' : 'text-yellow-700'">
            {{ formMode === 'create' ? 'Create Book' : 'Update Book' }}
          </h2>
          <form @submit.prevent="formMode === 'create' ? addBook() : saveUpdate()" class="space-y-4">
            <input v-model="formBook.isbn" placeholder="ISBN" class="input w-full" />
            <input v-model="formBook.title" placeholder="Title" class="input w-full" />
            <input v-model="formBook.author_name" placeholder="Author Name" class="input w-full" />
            <input v-model="formBook.publication_year" type="number" placeholder="Year" class="input w-full" />
            <input v-model="formBook.number_of_copies" type="number" placeholder="Copies" class="input w-full" />
            <select v-model="formBook.category" class="input w-full">
              <option disabled value="">Select Category</option>
              <option v-for="cat in categories.filter(c => c !== 'All')" :key="cat">{{ cat }}</option>
            </select>
            <div class="flex justify-end gap-2">
              <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">📂 Save</button>
              <button type="button" @click="formMode = ''" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</button>
            </div>
          </form>
        </div>

        <div v-else-if="formMode === 'show'">
          <h2 class="text-xl font-bold mb-4 text-green-700">📖 Book Details</h2>
          <div class="space-y-1 text-sm text-gray-800">
            <p><strong>Title:</strong> {{ formBook.title }}</p>
            <p><strong>ISBN:</strong> {{ formBook.isbn }}</p>
            <p><strong>Author:</strong> {{ formBook.author_name }}</p>
            <p><strong>Year:</strong> {{ formBook.publication_year }}</p>
            <p><strong>Copies:</strong> {{ formBook.number_of_copies }}</p>
            <p><strong>Category:</strong> {{ formBook.category }}</p>
          </div>
          <div class="flex justify-end mt-4">
            <button @click="formMode = ''" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const API_URL = 'http://192.168.108.14:8000/api/books'

const categories = ['All', 'Programming', 'Drama', 'Funny', 'Ghost']
const active = ref('All')
const books = ref([])
const formMode = ref('')
const openMenu = ref(null)
const tableWrapper = ref(null)

const formBook = ref({
  id: null,
  title: '',
  isbn: '',
  author_name: '',
  publication_year: '',
  number_of_copies: '',
  category: ''
})

const filteredBooks = computed(() =>
  active.value === 'All'
    ? books.value
    : books.value.filter(book => book.category === active.value)
)

const fetchBooks = async () => {
  try {
    const res = await axios.get(API_URL)
    books.value = res.data.data || res.data
  } catch (err) {
    console.error('Fetch error:', err)
  }
}

const openCreateForm = () => {
  formMode.value = 'create'
  formBook.value = {
    id: null,
    title: '',
    isbn: '',
    author_name: '',
    publication_year: '',
    number_of_copies: '',
    category: ''
  }
}

const addBook = async () => {
  try {
    const res = await axios.post(API_URL, formBook.value)
    books.value.push(res.data)
    formMode.value = ''
    scrollToBottom()
  } catch (err) {
    console.error('Create error:', err)
  }
}

const openUpdateForm = (book) => {
  formMode.value = 'update'
  formBook.value = { ...book }
}

const saveUpdate = async () => {
  try {
    const res = await axios.put(`${API_URL}/${formBook.value.id}`, formBook.value)
    const index = books.value.findIndex(b => b.id === formBook.value.id)
    if (index !== -1) books.value[index] = res.data
    formMode.value = ''
  } catch (err) {
    console.error('Update error:', err)
  }
}

const deleteBook = async (id) => {
  if (!confirm('Are you sure you want to delete this book?')) return
  try {
    await axios.delete(`${API_URL}/${id}`)
    books.value = books.value.filter(book => book.id !== id)
    if (formBook.value.id === id) formMode.value = ''
  } catch (err) {
    console.error('Delete error:', err)
  }
}

const showBook = (book) => {
  formMode.value = 'show'
  formBook.value = { ...book }
}

const scrollToBottom = () => {
  if (tableWrapper.value) {
    tableWrapper.value.scrollTop = tableWrapper.value.scrollHeight
  }
}

onMounted(fetchBooks)
</script>

<style scoped>
.input {
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 8px;
  outline: none;
}
</style>
