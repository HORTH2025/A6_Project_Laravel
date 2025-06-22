<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <main class="container mx-auto px-4 py-8">
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
            Author Management
          </h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            Manage your library's authors and their works
          </p>
        </div>
        
        <div class="flex items-center gap-2">
          <button
            @click="toggleDarkMode"
            class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
            aria-label="Toggle dark mode"
          >
            <i :class="darkMode ? 'fas fa-sun' : 'fas fa-moon'" class="text-gray-600 dark:text-gray-300"></i>
          </button>
          <button
            @click="refreshAuthors"
            class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
            aria-label="Refresh authors"
            :disabled="isLoading"
          >
            <i class="fas fa-sync-alt text-gray-600 dark:text-gray-300" :class="{ 'animate-spin': isLoading }"></i>
          </button>
        </div>
      </div>

      <!-- Controls Section -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-8 transition-all duration-200">
        <div class="flex flex-col lg:flex-row justify-between gap-6">
          <!-- Search and Filters -->
          <div class="flex-1">
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-search text-gray-400"></i>
                </div>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search authors..."
                  class="pl-10 w-full border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                  aria-label="Search authors"
                  @input="resetPagination"
                />
              </div>
              
              <div class="flex gap-2">
                <select
                  v-model="nationalityFilter"
                  class="border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                  @change="resetPagination"
                >
                  <option value="">All Nationalities</option>
                  <option v-for="nation in uniqueNationalities" :value="nation">{{ nation }}</option>
                </select>
                
                <select
                  v-model="sortBy"
                  class="border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                >
                  <option value="name">Sort by Name</option>
                  <option value="nationality">Sort by Nationality</option>
                  <option value="writen_book">Sort by Books</option>
                </select>
              </div>
            </div>
          </div>
          
          <!-- Add Author Button -->
          <button
            @click="openCreateForm"
            class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 text-white text-sm font-semibold rounded-lg px-5 py-3 shadow-md transition whitespace-nowrap"
            :disabled="isLoading"
          >
            <i class="fas fa-plus mr-2"></i> Add Author
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Authors</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ totalAuthors }}</p>
            </div>
            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
              <i class="fas fa-users text-xl"></i>
            </div>
          </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Books Written</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ totalBooks }}</p>
            </div>
            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
              <i class="fas fa-book text-xl"></i>
            </div>
          </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Unique Nationalities</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ uniqueNationalities.length }}</p>
            </div>
            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
              <i class="fas fa-globe text-xl"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Author Table -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden transition-all duration-200">
        <!-- Loading State -->
        <div v-if="isLoading" class="p-8 flex justify-center items-center">
          <div class="animate-pulse flex flex-col items-center">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-500 mb-2"></i>
            <p class="text-gray-500 dark:text-gray-400">Loading authors...</p>
          </div>
        </div>
        
        <!-- Empty State -->
        <div 
          v-else-if="filteredAuthors.length === 0" 
          class="p-12 text-center"
        >
          <div class="mx-auto max-w-md">
            <i class="fas fa-user-edit text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
              No authors found
            </h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
              Try adjusting your search or add a new author
            </p>
            <button
              @click="resetFilters"
              class="text-blue-600 dark:text-blue-400 hover:underline font-medium"
            >
              Reset filters
            </button>
          </div>
        </div>
        
        <!-- Table Content -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  @click="toggleSortDirection"
                >
                  <div class="flex items-center gap-1">
                    <span>Name</span>
                    <i 
                      :class="{
                        'fas fa-sort': sortBy !== 'name',
                        'fas fa-sort-up': sortBy === 'name' && sortDirection === 'asc',
                        'fas fa-sort-down': sortBy === 'name' && sortDirection === 'desc'
                      }"
                      class="text-xs"
                    ></i>
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  @click="setSort('nationality')"
                >
                  <div class="flex items-center gap-1">
                    <span>Nationality</span>
                    <i 
                      :class="{
                        'fas fa-sort': sortBy !== 'nationality',
                        'fas fa-sort-up': sortBy === 'nationality' && sortDirection === 'asc',
                        'fas fa-sort-down': sortBy === 'nationality' && sortDirection === 'desc'
                      }"
                      class="text-xs"
                    ></i>
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  @click="setSort('writen_book')"
                >
                  <div class="flex items-center gap-1">
                    <span>Books</span>
                    <i 
                      :class="{
                        'fas fa-sort': sortBy !== 'writen_book',
                        'fas fa-sort-up': sortBy === 'writen_book' && sortDirection === 'asc',
                        'fas fa-sort-down': sortBy === 'writen_book' && sortDirection === 'desc'
                      }"
                      class="text-xs"
                    ></i>
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr 
                v-for="author in paginatedAuthors" 
                :key="author.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                      <i class="fas fa-user text-gray-600 dark:text-gray-300"></i>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ author.name }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        ID: {{ author.id }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-900 dark:text-white">{{ author.nationality }}</span>
                    <span 
                      v-if="author.nationality"
                      class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200"
                    >
                      {{ getCountryFlag(author.nationality) }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mr-2">
                      <div 
                        class="bg-blue-600 h-2.5 rounded-full" 
                        :style="{ width: `${Math.min(100, (author.writen_book / maxBooks) * 100)}%` }"
                      ></div>
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ author.writen_book }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end gap-2">
                    <button
                      @click="viewAuthor(author)"
                      class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors p-2"
                      aria-label="View author details"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      @click="openEditForm(author)"
                      class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors p-2"
                      aria-label="Edit author"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      @click="confirmDelete(author)"
                      class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors p-2"
                      aria-label="Delete author"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div 
          v-if="filteredAuthors.length > 0" 
          class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-600"
        >
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="previousPage"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600"
            >
              Previous
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600"
            >
              Next
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700 dark:text-gray-300">
                Showing <span class="font-medium">{{ startItem }}</span> to <span class="font-medium">{{ endItem }}</span> of <span class="font-medium">{{ filteredAuthors.length }}</span> results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  @click="previousPage"
                  :disabled="currentPage === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span class="sr-only">Previous</span>
                  <i class="fas fa-chevron-left"></i>
                </button>
                
                <template v-for="page in visiblePages" :key="page">
                  <button
                    @click="goToPage(page)"
                    :class="{
                      'z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 dark:border-blue-600 text-blue-600 dark:text-blue-300': currentPage === page,
                      'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700': currentPage !== page
                    }"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                  >
                    {{ page }}
                  </button>
                </template>
                
                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span class="sr-only">Next</span>
                  <i class="fas fa-chevron-right"></i>
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Author Form Modal -->
    <transition name="modal">
      <div
        v-if="showForm"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <div 
              class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-80 transition-opacity" 
              aria-hidden="true"
              @click="closeForm"
            ></div>
          </transition>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

          <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                    <i 
                      :class="isEdit ? 'fas fa-user-edit text-blue-600 dark:text-blue-300' : 'fas fa-user-plus text-blue-600 dark:text-blue-300'" 
                      class="text-lg"
                    ></i>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                      {{ isEdit ? 'Edit Author' : 'Add New Author' }}
                    </h3>
                    <div class="mt-2">
                      <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Name <span class="text-red-500">*</span>
                          </label>
                          <input
                            v-model="form.name"
                            id="name"
                            type="text"
                            placeholder="Author's full name"
                            required
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-4 py-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            :class="{ 'border-red-500': errors.name }"
                          />
                          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                        </div>

                        <div>
                          <label for="nationality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nationality <span class="text-red-500">*</span>
                          </label>
                          <input
                            v-model="form.nationality"
                            id="nationality"
                            type="text"
                            placeholder="Country of origin"
                            required
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-4 py-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            :class="{ 'border-red-500': errors.nationality }"
                          />
                          <p v-if="errors.nationality" class="mt-1 text-sm text-red-600">{{ errors.nationality }}</p>
                        </div>

                        <div>
                          <label for="writen_book" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Books Written <span class="text-red-500">*</span>
                          </label>
                          <input
                            v-model.number="form.writen_book"
                            id="writen_book"
                            type="number"
                            min="0"
                            step="1"
                            placeholder="Number of books written"
                            required
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-4 py-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            :class="{ 'border-red-500': errors.writen_book }"
                          />
                          <p v-if="errors.writen_book" class="mt-1 text-sm text-red-600">{{ errors.writen_book }}</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                          <button
                            type="button"
                            @click="closeForm"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                            :disabled="isSubmitting"
                          >
                            Cancel
                          </button>
                          <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                            :disabled="isSubmitting"
                          >
                            <span v-if="isSubmitting">
                              <i class="fas fa-spinner fa-spin mr-2"></i>
                              {{ isEdit ? 'Saving...' : 'Creating...' }}
                            </span>
                            <span v-else>
                              {{ isEdit ? 'Save Changes' : 'Create Author' }}
                            </span>
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </transition>

    <!-- Author Detail Modal -->
    <transition name="modal">
      <div
        v-if="showDetailModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="detail-modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div 
            class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-80 transition-opacity" 
            aria-hidden="true"
            @click="closeDetailModal"
          ></div>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

          <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 sm:p-6">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                  <i class="fas fa-user text-blue-600 dark:text-blue-300"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="detail-modal-title">
                    Author Details
                  </h3>
                  <div class="mt-4 space-y-4">
                    <div>
                      <h4 class="text-md font-semibold text-gray-900 dark:text-white">{{ selectedAuthor.name }}</h4>
                    </div>
                    <div class="flex items-center gap-2">
                      <i class="fas fa-globe text-gray-500 dark:text-gray-400"></i>
                      <span class="text-gray-700 dark:text-gray-300">{{ selectedAuthor.nationality }}</span>
                      <span 
                        v-if="selectedAuthor.nationality"
                        class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200"
                      >
                        {{ getCountryFlag(selectedAuthor.nationality) }}
                      </span>
                    </div>
                    <div class="flex items-center gap-2">
                      <i class="fas fa-book text-gray-500 dark:text-gray-400"></i>
                      <span class="text-gray-700 dark:text-gray-300">
                        {{ selectedAuthor.writen_book }} book{{ selectedAuthor.writen_book !== 1 ? 's' : '' }} written
                      </span>
                    </div>
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                      <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                        Created: {{ formatDate(selectedAuthor.created_at) }}
                      </p>
                      <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                        Last updated: {{ formatDate(selectedAuthor.updated_at) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                @click="closeDetailModal"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Delete Confirmation Modal -->
    <transition name="modal">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="delete-modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div 
            class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-80 transition-opacity" 
            aria-hidden="true"
          ></div>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

          <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 sm:p-6">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                  <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-300"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="delete-modal-title">
                    Delete Author
                  </h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      Are you sure you want to delete <span class="font-semibold">{{ authorToDelete?.name }}</span>? This action cannot be undone.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                @click="deleteAuthor"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                :disabled="isDeleting"
              >
                <span v-if="isDeleting">
                  <i class="fas fa-spinner fa-spin mr-2"></i>
                  Deleting...
                </span>
                <span v-else>
                  Delete
                </span>
              </button>
              <button
                type="button"
                @click="closeDeleteModal"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Toast Notification -->
    <transition name="toast">
      <div 
        v-if="toast.show"
        class="fixed bottom-4 right-4 z-50"
      >
        <div 
          :class="{
            'bg-green-500': toast.type === 'success',
            'bg-red-500': toast.type === 'error',
            'bg-blue-500': toast.type === 'info'
          }"
          class="text-white px-4 py-3 rounded-lg shadow-lg flex items-start max-w-xs"
          role="alert"
        >
          <div class="flex-shrink-0 pt-1">
            <i 
              :class="{
                'fas fa-check-circle': toast.type === 'success',
                'fas fa-exclamation-circle': toast.type === 'error',
                'fas fa-info-circle': toast.type === 'info'
              }"
              class="text-xl"
            ></i>
          </div>
          <div class="ml-3">
            <p class="font-medium">{{ toast.title }}</p>
            <p class="text-sm">{{ toast.message }}</p>
          </div>
          <button
            @click="toast.show = false"
            class="ml-4 text-white hover:text-gray-200 focus:outline-none"
            aria-label="Close notification"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

export default {
  name: 'AuthorManagement',
  setup() {
    // State
    const authors = ref([]);
    const isLoading = ref(false);
    const showForm = ref(false);
    const isEdit = ref(false);
    const isSubmitting = ref(false);
    const selectedId = ref(null);
    const searchQuery = ref('');
    const nationalityFilter = ref('');
    const sortBy = ref('name');
    const sortDirection = ref('asc');
    const darkMode = ref(false);
    const showDetailModal = ref(false);
    const selectedAuthor = ref({});
    const showDeleteModal = ref(false);
    const authorToDelete = ref(null);
    const isDeleting = ref(false);
    const currentPage = ref(1);
    const itemsPerPage = ref(10);
    
    // Toast notification
    const toast = ref({
      show: false,
      type: 'success',
      title: '',
      message: '',
      timeout: null
    });
    
    // Form errors
    const errors = ref({
      name: '',
      nationality: '',
      writen_book: ''
    });
    
    // Form data
    const form = ref({
      name: '',
      nationality: '',
      writen_book: 0
    });
    
    // Initialize dark mode from localStorage or prefer-color-scheme
    const initDarkMode = () => {
      const savedMode = localStorage.getItem('darkMode');
      if (savedMode !== null) {
        darkMode.value = savedMode === 'true';
      } else {
        darkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
      }
      updateDarkModeClass();
    };
    
    // Toggle dark mode
    const toggleDarkMode = () => {
      darkMode.value = !darkMode.value;
      localStorage.setItem('darkMode', darkMode.value);
      updateDarkModeClass();
    };
    
    // Update dark mode class on document
    const updateDarkModeClass = () => {
      if (darkMode.value) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    };
    
    // API URL
    const API_URL = "http://192.168.108.14:8000/api/authors";
    
    // Fetch authors from API
    const fetchAuthors = async () => {
      try {
        isLoading.value = true;
        const res = await axios.get(API_URL);
        authors.value = res.data.data || [];
      } catch (err) {
        console.error("Error fetching authors:", err);
        showToast('error', 'Error', 'Failed to fetch authors. Please try again.');
      } finally {
        isLoading.value = false;
      }
    };
    
    // Show toast notification
    const showToast = (type, title, message, duration = 5000) => {
      if (toast.value.timeout) {
        clearTimeout(toast.value.timeout);
      }
      
      toast.value = {
        show: true,
        type,
        title,
        message
      };
      
      toast.value.timeout = setTimeout(() => {
        toast.value.show = false;
      }, duration);
    };
    
    // Filtered authors based on search and filters
    const filteredAuthors = computed(() => {
      let result = [...authors.value];
      
      // Apply search query filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(author => 
          author.name.toLowerCase().includes(query) || 
          author.nationality.toLowerCase().includes(query)
        );
      }
      
      // Apply nationality filter
      if (nationalityFilter.value) {
        result = result.filter(author => 
          author.nationality === nationalityFilter.value
        );
      }
      
      // Apply sorting
      result.sort((a, b) => {
        let comparison = 0;
        
        if (sortBy.value === 'name') {
          comparison = a.name.localeCompare(b.name);
        } else if (sortBy.value === 'nationality') {
          comparison = a.nationality.localeCompare(b.nationality);
        } else if (sortBy.value === 'writen_book') {
          comparison = a.writen_book - b.writen_book;
        }
        
        return sortDirection.value === 'asc' ? comparison : -comparison;
      });
      
      return result;
    });
    
    // Unique nationalities for filter dropdown
    const uniqueNationalities = computed(() => {
      const nationalities = new Set();
      authors.value.forEach(author => {
        if (author.nationality) {
          nationalities.add(author.nationality);
        }
      });
      return Array.from(nationalities).sort();
    });
    
    // Total number of books written by all authors
    const totalBooks = computed(() => {
      return filteredAuthors.value.reduce((sum, author) => sum + author.writen_book, 0);
    });
    
    // Total number of authors
    const totalAuthors = computed(() => {
      return filteredAuthors.value.length;
    });
    
    // Maximum number of books for progress bars
    const maxBooks = computed(() => {
      if (filteredAuthors.value.length === 0) return 1;
      return Math.max(...filteredAuthors.value.map(a => a.writen_book), 1);
    });
    
    // Pagination computed properties
    const totalPages = computed(() => {
      return Math.ceil(filteredAuthors.value.length / itemsPerPage.value);
    });
    
    const paginatedAuthors = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      const end = start + itemsPerPage.value;
      return filteredAuthors.value.slice(start, end);
    });
    
    const startItem = computed(() => {
      return (currentPage.value - 1) * itemsPerPage.value + 1;
    });
    
    const endItem = computed(() => {
      return Math.min(currentPage.value * itemsPerPage.value, filteredAuthors.value.length);
    });
    
    const visiblePages = computed(() => {
      const pages = [];
      const maxVisible = 5;
      let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
      let end = Math.min(totalPages.value, start + maxVisible - 1);
      
      if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
      }
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    });
    
    // Pagination methods
    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
      }
    };
    
    const previousPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--;
      }
    };
    
    const goToPage = (page) => {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
      }
    };
    
    const resetPagination = () => {
      currentPage.value = 1;
    };
    
    // Sorting methods
    const setSort = (column) => {
      if (sortBy.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
      } else {
        sortBy.value = column;
        sortDirection.value = 'asc';
      }
    };
    
    const toggleSortDirection = () => {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    };
    
    // Form methods
    const openCreateForm = () => {
      isEdit.value = false;
      form.value = { name: '', nationality: '', writen_book: 0 };
      errors.value = { name: '', nationality: '', writen_book: '' };
      showForm.value = true;
    };
    
    const openEditForm = (author) => {
      isEdit.value = true;
      selectedId.value = author.id;
      form.value = { ...author };
      errors.value = { name: '', nationality: '', writen_book: '' };
      showForm.value = true;
    };
    
    const closeForm = () => {
      showForm.value = false;
      selectedId.value = null;
    };
    
    const validateForm = () => {
      let isValid = true;
      errors.value = { name: '', nationality: '', writen_book: '' };
      
      if (!form.value.name.trim()) {
        errors.value.name = 'Name is required';
        isValid = false;
      } else if (form.value.name.length > 100) {
        errors.value.name = 'Name must be less than 100 characters';
        isValid = false;
      }
      
      if (!form.value.nationality.trim()) {
        errors.value.nationality = 'Nationality is required';
        isValid = false;
      } else if (form.value.nationality.length > 50) {
        errors.value.nationality = 'Nationality must be less than 50 characters';
        isValid = false;
      }
      
      if (form.value.writen_book === '' || form.value.writen_book === null) {
        errors.value.writen_book = 'Number of books is required';
        isValid = false;
      } else if (form.value.writen_book < 0) {
        errors.value.writen_book = 'Number of books cannot be negative';
        isValid = false;
      } else if (form.value.writen_book > 10000) {
        errors.value.writen_book = 'Number of books seems too high';
        isValid = false;
      }
      
      return isValid;
    };
    
    const submitForm = async () => {
      if (!validateForm()) return;
      
      try {
        isSubmitting.value = true;
        
        if (isEdit.value) {
          await axios.put(`${API_URL}/edit/${selectedId.value}`, form.value);
          showToast('success', 'Success', 'Author updated successfully');
        } else {
          await axios.post(`${API_URL}/create`, form.value);
          showToast('success', 'Success', 'Author created successfully');
        }
        
        await fetchAuthors();
        closeForm();
      } catch (err) {
        console.error("Error saving author:", err);
        showToast('error', 'Error', 'Failed to save author. Please try again.');
      } finally {
        isSubmitting.value = false;
      }
    };
    
    // Author detail methods
    const viewAuthor = (author) => {
      selectedAuthor.value = author;
      showDetailModal.value = true;
    };
    
    const closeDetailModal = () => {
      showDetailModal.value = false;
      selectedAuthor.value = {};
    };
    
    // Delete methods
    const confirmDelete = (author) => {
      authorToDelete.value = author;
      showDeleteModal.value = true;
    };
    
    const closeDeleteModal = () => {
      showDeleteModal.value = false;
      authorToDelete.value = null;
      isDeleting.value = false;
    };
    
    const deleteAuthor = async () => {
      try {
        isDeleting.value = true;
        await axios.delete(`${API_URL}/delete/${authorToDelete.value.id}`);
        await fetchAuthors();
        showToast('success', 'Success', 'Author deleted successfully');
        closeDeleteModal();
      } catch (err) {
        console.error("Error deleting author:", err);
        showToast('error', 'Error', 'Failed to delete author. Please try again.');
      } finally {
        isDeleting.value = false;
      }
    };
    
    // Utility methods
    const getCountryFlag = (countryName) => {
      // This is a simplified version - in a real app you'd use a proper country code to flag mapping
      if (!countryName) return '';
      
      const countryCodeMap = {
        'usa': '🇺🇸',
        'united states': '🇺🇸',
        'uk': '🇬🇧',
        'united kingdom': '🇬🇧',
        'france': '🇫🇷',
        'germany': '🇩🇪',
        'spain': '🇪🇸',
        'italy': '🇮🇹',
        'japan': '🇯🇵',
        'china': '🇨🇳',
        'india': '🇮🇳',
        'brazil': '🇧🇷',
        'canada': '🇨🇦',
        'australia': '🇦🇺',
        'russia': '🇷🇺'
      };
      
      const lowerName = countryName.toLowerCase();
      return countryCodeMap[lowerName] || '🌍';
    };
    
    const formatDate = (dateString) => {
      if (!dateString) return 'Unknown';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    };
    
    const refreshAuthors = async () => {
      await fetchAuthors();
      showToast('info', 'Refreshed', 'Author list has been refreshed');
    };
    
    const resetFilters = () => {
      searchQuery.value = '';
      nationalityFilter.value = '';
      sortBy.value = 'name';
      sortDirection.value = 'asc';
      resetPagination();
    };
    
    // Lifecycle hooks
    onMounted(() => {
      initDarkMode();
      fetchAuthors();
    });
    
    // Watch for changes that should reset pagination
    watch([searchQuery, nationalityFilter, sortBy, sortDirection], () => {
      resetPagination();
    });
    
    // Expose all necessary data and methods to template
    return {
      authors,
      isLoading,
      showForm,
      isEdit,
      isSubmitting,
      form,
      errors,
      searchQuery,
      nationalityFilter,
      sortBy,
      sortDirection,
      filteredAuthors,
      paginatedAuthors,
      uniqueNationalities,
      totalBooks,
      totalAuthors,
      maxBooks,
      currentPage,
      totalPages,
      startItem,
      endItem,
      visiblePages,
      darkMode,
      showDetailModal,
      selectedAuthor,
      showDeleteModal,
      authorToDelete,
      isDeleting,
      toast,
      
      // Methods
      fetchAuthors,
      openCreateForm,
      openEditForm,
      closeForm,
      submitForm,
      viewAuthor,
      closeDetailModal,
      confirmDelete,
      closeDeleteModal,
      deleteAuthor,
      nextPage,
      previousPage,
      goToPage,
      setSort,
      toggleSortDirection,
      getCountryFlag,
      formatDate,
      toggleDarkMode,
      refreshAuthors,
      resetFilters
    };
  }
};
</script>

<style scoped>
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css");

/* Transition styles */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.toast-enter-active, .toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from, .toast-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Dark mode transitions */
.dark .bg-white {
  transition: background-color 0.3s ease;
}
.dark .text-gray-900 {
  transition: color 0.3s ease;
}
</style>