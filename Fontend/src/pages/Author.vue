<template>
  <div class="p-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-5xl font-bold text-gray-800">Author</h1>
      <button
        @click="createAuthor"
        class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700"
      >
        + Create Author
      </button>
    </div>

    <p class="text-lg text-gray-600 mb-10">
      An author is a person who creates and writes books, articles, or other written works.
    </p>

    <!-- Author Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <div
        v-for="(author, index) in authors"
        :key="author.id"
        class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 relative"
      >
        <!-- Author Image -->
        <img
          :src="author.image"
          alt="Author"
          class="w-24 h-24 rounded-full mx-auto object-cover border-4 border-blue-500 mb-4"
        />

        <!-- Author Info -->
        <h2 class="text-xl font-bold text-center text-gray-800">{{ author.name || 'New Author' }}</h2>
        <p class="text-sm text-center text-gray-500">{{ author.email || 'No email provided' }}</p>
        <p class="text-center text-blue-700 font-semibold my-2">
          {{ author.books.length }} Book{{ author.books.length !== 1 ? 's' : '' }} Created
        </p>

        <!-- Menu Button -->
        <div class="absolute top-4 right-4">
          <button
            @click="toggleMenu(index)"
            class="text-gray-700 hover:text-black px-2 py-1 rounded"
          >
            ⋮
          </button>

          <!-- Dropdown Menu -->
          <div
            v-if="author.showMenu"
            class="absolute right-0 mt-2 w-40 bg-white border border-gray-300 rounded-lg shadow-lg z-10"
          >
            <button
              @click="toggleDetails(author); closeMenu(index)"
              class="block w-full text-left px-4 py-2 hover:bg-gray-100"
            >
              {{ author.showDetails ? 'Hide Info' : 'View More' }}
            </button>
            <button
              @click="author.isEditing = true; closeMenu(index)"
              class="block w-full text-left px-4 py-2 hover:bg-gray-100"
            >
              Edit
            </button>
            <button
              @click="deleteAuthor(index); closeMenu(index)"
              class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-100"
            >
              Delete
            </button>
          </div>
        </div>

        <!-- Details View -->
        <div v-if="author.showDetails && !author.isEditing" class="mt-4 text-sm text-gray-700 space-y-1">
          <p><strong>ID:</strong> {{ author.id }}</p>
          <p><strong>Date of Birth:</strong> {{ author.dob || 'Not specified' }}</p>
          <p><strong>Latest Book Date:</strong> {{ author.latestBookDate || 'Not specified' }}</p>
          <div v-if="author.books.length > 0">
            <strong>Books:</strong>
            <ul class="list-disc ml-5 mt-1">
              <li v-for="(book, i) in author.books" :key="i">{{ book }}</li>
            </ul>
          </div>
        </div>

        <!-- Edit Form -->
        <div v-if="author.isEditing" class="mt-4 space-y-4 text-sm">
          <input v-model="author.name" class="w-full border rounded px-3 py-2" placeholder="Name" />
          <input v-model="author.email" type="email" class="w-full border rounded px-3 py-2" placeholder="Email" />
          <input v-model="author.dob" type="date" class="w-full border rounded px-3 py-2" placeholder="Date of Birth" />
          <input v-model="author.latestBookDate" type="date" class="w-full border rounded px-3 py-2" placeholder="Latest Book Date" />
          
          <div class="space-y-2">
            <div v-for="(book, idx) in author.books" :key="idx" class="flex items-center">
              <input v-model="author.books[idx]" class="flex-1 border rounded px-3 py-2" placeholder="Book title" />
              <button @click="removeBook(author, idx)" class="ml-2 text-red-500">×</button>
            </div>
            <button @click="addBook(author)" class="text-blue-600 text-sm">+ Add Book</button>
          </div>

          <div class="flex gap-3 pt-2">
            <button
              @click="saveAuthor(author)"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
              Save
            </button>
            <button
              @click="cancelEdit(author, index)"
              class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AuthorGrid',
  data() {
    return {
      authors: [
        {
          id: 'A001',
          name: 'John Doe',
          email: 'john@example.com',
          dob: '1980-01-15',
          latestBookDate: '2025-06-05',
          image: 'https://i.pravatar.cc/150?img=1',
          books: ['Book One', 'Book Two'],
          showDetails: false,
          showMenu: false,
          isEditing: false
        },
        {
          id: 'A002',
          name: 'Jane Smith',
          email: 'jane@example.com',
          dob: '1975-09-21',
          latestBookDate: '2024-11-18',
          image: 'https://i.pravatar.cc/150?img=2',
          books: ['UX Design', 'Creative Code'],
          showDetails: false,
          showMenu: false,
          isEditing: false
        }
      ]
    };
  },
  methods: {
    toggleMenu(index) {
      this.authors.forEach((author, i) => {
        author.showMenu = i === index ? !author.showMenu : false;
      });
    },
    closeMenu(index) {
      this.authors[index].showMenu = false;
    },
    toggleDetails(author) {
      author.showDetails = !author.showDetails;
    },
    deleteAuthor(index) {
      if (confirm('Are you sure you want to delete this author?')) {
        this.authors.splice(index, 1);
      }
    },
    createAuthor() {
      const newId = 'A' + String(this.authors.length + 1).padStart(3, '0');
      this.authors.unshift({
        id: newId,
        name: '',
        email: '',
        dob: '',
        latestBookDate: '',
        image: 'https://i.pravatar.cc/150?img=' + (this.authors.length + 1),
        books: [],
        showDetails: false,
        showMenu: false,
        isEditing: true
      });
    },
    saveAuthor(author) {
      // Basic validation
      if (!author.name) {
        alert('Please enter a name for the author');
        return;
      }
      
      author.isEditing = false;
      // In a real app, you would save to an API here
      alert('Author information saved!');
    },
    cancelEdit(author, index) {
      if (!author.name) {
        // If it's a new author with no name, remove it
        this.authors.splice(index, 1);
      } else {
        // Otherwise just cancel editing
        author.isEditing = false;
      }
    },
    addBook(author) {
      author.books.push('');
    },
    removeBook(author, index) {
      author.books.splice(index, 1);
    }
  }
};
</script>