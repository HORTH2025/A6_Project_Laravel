<template>
  <div class="p-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-5 ">
      <h1 class="text-3xl font-bold text-gray-700 text-center mb-5 tracking-tight">
      Book Categories
      </h1>
      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <input
          v-model="searchTerm"
          type="text"
          placeholder="🔍 Search by Title, Author, or ISBN..."
          class="w-full sm:w-72 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
        />
        <button
          @click="openCreateForm"
          class="flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-lg transition"
        >
          <i class="fas fa-plus-circle text-white text-lg"></i> Create Book
        </button>
      </div>
    </div>

    <!-- Category Filter -->
    <div class="flex flex-wrap gap-3 mb-4">
      <button
        v-for="cat in categories"
        :key="cat"
        @click="active = cat"
        :class="[
          'px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-200',
          active === cat
            ? 'bg-black text-white shadow-md'
            : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100'
        ]"
      >
        {{ cat }}
      </button>
    </div>

    <!-- Table -->
    <div class=" rounded-l bg-white ring-1 ring-gray-200">
      <table class="min-w-full text-sm divide-y divide-gray-200">
        <thead class="bg-blue-100 sticky top-0 z-10 text-blue-900">
          <tr>
            <th v-for="header in ['ID','ISBN','Title','Author','Year','Copies','Category','Actions']"
                :key="header"
                class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">
              {{ header }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="book in filteredBooks"
            :key="book.id"
            class="hover:bg-blue-50 transition"
          >
            <td class="px-4 py-2">{{ book.id }}</td>
            <td class="px-4 py-2">{{ book.isbn }}</td>
            <td class="px-4 py-2 font-semibold text-gray-800">{{ book.title }}</td>
            <td class="px-4 py-2">{{ book.author_name }}</td>
            <td class="px-4 py-2">{{ book.publication_year }}</td>
            <td class="px-4 py-2">{{ book.number_of_copies }}</td>
            <td class="px-4 py-2">
              <span class="inline-block bg-gray-100 text-gray-800 px-3 py-0.5 rounded-full text-xs font-medium">
                {{ book.category }}
              </span>
            </td>
            <td class="px-4 py-2">
              <div class="relative">
                <button
                  @click="openMenu === book.id ? openMenu = null : openMenu = book.id"
                  class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 text-lg transition"
                >
                  ⋮
                </button>
                <div
                  v-if="openMenu === book.id"
                  class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg ring-1 ring-black/5 ring-opacity-5 z-10 animate-fade-in-down"
                >
                  <ul class="text-sm text-gray-700">
                    <li>
                      <button @click="showBook(book); openMenu = null"
                              class="w-full px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-eye text-blue-600"></i> Show
                      </button>
                    </li>
                    <li>
                      <button @click="openUpdateForm(book); openMenu = null"
                              class="w-full px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                        <i class="fas fa-edit text-yellow-600"></i> Edit
                      </button>
                    </li>
                    <li>
                      <button @click="deleteBook(book.id); openMenu = null"
                              class="w-full px-4 py-2 text-red-600 hover:bg-red-100 flex items-center gap-2">
                        <i class="fas fa-trash-alt"></i> Delete
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
            </td>
          </tr>
          <tr v-if="filteredBooks.length === 0">
            <td colspan="8" class="text-center py-6 text-gray-400 font-medium">No books found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Form Modal -->
    <div
      v-if="formMode !== ''"
      class="fixed inset-0 z-40 bg-black/30 flex justify-center items-center px-4"
      @click.self="formMode = ''"
    >
      <div class="bg-white w-full max-w-xl p-6 rounded-2xl shadow-2xl z-50 animate-fade-in-down">
        <div v-if="formMode === 'create' || formMode === 'update'">
          <h2 class="text-2xl font-bold mb-6"
              :class="formMode === 'create' ? 'text-blue-700' : 'text-yellow-700'">
            {{ formMode === 'create' ? '📘 Create Book' : '✏️ Update Book' }}
          </h2>
          <form @submit.prevent="formMode === 'create' ? addBook() : saveUpdate()" class="space-y-4">
            <input v-model="formBook.isbn" placeholder="ISBN"
                   class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300" required />
            <input v-model="formBook.title" placeholder="Title"
                   class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300" required />
            <input v-model="formBook.author_name" placeholder="Author Name"
                   class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300" required />
            <input v-model.number="formBook.publication_year" type="number" placeholder="Year"
                   class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300" required />
            <input v-model.number="formBook.number_of_copies" type="number" placeholder="Copies"
                   class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300" required />
            <select v-model="formBook.category"
                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300" required>
              <option disabled value="">Select Category</option>
              <option v-for="cat in categories.filter(c => c !== 'All')" :key="cat">{{ cat }}</option>
            </select>
            <div class="flex justify-end gap-2 pt-4">
              <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
                ✅ Save
              </button>
              <button @click="formMode = ''" type="button" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">
                Cancel
              </button>
            </div>
          </form>
        </div>

        <div v-else-if="formMode === 'show'">
          <h2 class="text-2xl font-bold mb-4 text-green-700">📖 Book Details</h2>
          <div class="space-y-2 text-sm text-gray-700">
            <p><strong>Title:</strong> {{ formBook.title }}</p>
            <p><strong>ISBN:</strong> {{ formBook.isbn }}</p>
            <p><strong>Author:</strong> {{ formBook.author_name }}</p>
            <p><strong>Year:</strong> {{ formBook.publication_year }}</p>
            <p><strong>Copies:</strong> {{ formBook.number_of_copies }}</p>
            <p><strong>Category:</strong> {{ formBook.category }}</p>
          </div>
          <div class="flex justify-end mt-6">
            <button @click="formMode = ''" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const API = 'http://192.168.108.14:8000/api/books'

const books = ref([])
const active = ref('All')
const formMode = ref('')
const openMenu = ref(null)
const searchTerm = ref('')
const categories = ['All', 'Programming', 'Drama', 'Funny', 'Ghost']

const formBook = ref({
  id: null,
  title: '',
  isbn: '',
  author_name: '',
  publication_year: null,
  number_of_copies: null,
  category: ''
})

const fetchBooks = async () => {
  try {
    const res = await axios.get(API)
    books.value = res.data.data || []
  } catch (err) {
    alert('Error fetching books.')
    console.error(err)
  }
}

const addBook = async () => {
  try {
    const res = await axios.post(`${API}/create`, formBook.value)
    books.value.push(res.data.data)
    formMode.value = ''
  } catch (err) {
    alert('Failed to create book.')
    console.error(err)
  }
}

const saveUpdate = async () => {
  try {
    await axios.put(`${API}/edit/${formBook.value.id}`, formBook.value)
    const index = books.value.findIndex(b => b.id === formBook.value.id)
    if (index !== -1) books.value[index] = { ...formBook.value }
    formMode.value = ''
  } catch (err) {
    alert('Failed to update book.')
    console.error(err)
  }
}

const deleteBook = async (id) => {
  if (!confirm('Are you sure you want to delete this book?')) return
  try {
    await axios.delete(`${API}/delete/${id}`)
    books.value = books.value.filter(book => book.id !== id)
  } catch (err) {
    alert('Failed to delete book.')
    console.error(err)
  }
}

const showBook = async (book) => {
  try {
    const res = await axios.get(`${API}/${book.id}`)
    formBook.value = res.data.data
    formMode.value = 'show'
  } catch (err) {
    alert('Failed to load book details.')
    console.error(err)
  }
}

const openCreateForm = () => {
  formMode.value = 'create'
  formBook.value = {
    id: null,
    title: '',
    isbn: '',
    author_name: '',
    publication_year: null,
    number_of_copies: null,
    category: ''
  }
}

const openUpdateForm = (book) => {
  formMode.value = 'update'
  formBook.value = { ...book }
}

const filteredBooks = computed(() => {
  const term = searchTerm.value.toLowerCase().trim()
  return books.value.filter(book => {
    const matchesCategory = active.value === 'All' || book.category === active.value
    const matchesSearch =
      book.title.toLowerCase().includes(term) ||
      book.author_name.toLowerCase().includes(term) ||
      book.isbn.toLowerCase().includes(term)
    return matchesCategory && matchesSearch
  })
})

onMounted(fetchBooks)
</script>
