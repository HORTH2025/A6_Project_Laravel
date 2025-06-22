<template>
  <div class="min-h-screen bg-white">
    <main class="container mx-auto px-4 py-6">
      <h1 class="text-2xl font-semibold text-gray-900 text-center">Team Members</h1>
<!-- Search and Toggle Button -->
<div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-2">
  <input v-model="searchQuery" type="text" placeholder="Search by name or email"
    class="w-full sm:w-1/3 border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" />
  <button @click="toggleForm"
    class="bg-blue-600 text-white text-sm font-medium rounded-md py-2 px-4 hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    {{ showForm ? (isEditing ? 'Close Edit Form' : 'Close Create Form') : (isEditing ? 'Edit Member' : 'Add Member') }}
  </button>
</div>


      <!-- Create/Edit Member Form -->
      <transition name="fade">
        <div v-if="showForm" class="bg-white border border-gray-200 rounded-lg p-5 mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            {{ isEditing ? 'Edit Member' : 'Add New Member' }}
          </h2>
          <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
              <input v-model="formMember.name" id="name" type="text" placeholder="Enter name"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
                required>
            </div>
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <input v-model="formMember.email" id="email" type="email" placeholder="Enter email"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
                required>
            </div>
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
              <input v-model="formMember.phone" id="phone" type="text" placeholder="Enter phone"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
              <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
              <input v-model="formMember.website" id="website" type="text" placeholder="Enter website"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-2">
              <button type="button" @click="cancelForm"
                class="bg-gray-100 text-gray-700 text-sm rounded-md py-2 px-4 hover:bg-gray-200 transition-colors">
                Cancel
              </button>
              <button type="submit"
                class="bg-blue-600 text-white text-sm rounded-md py-2 px-4 hover:bg-blue-700 transition-colors">
                {{ isEditing ? 'Save' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </transition>



      <!-- Members Table -->
      <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <template v-for="member in filteredMembers" :key="member.id">
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ member.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ member.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ member.phone || 'No phone' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ member.website || 'No website' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm flex gap-3">
                  <button @click="startEdit(member)" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="deleteMember(member.id)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                  </button>
                  <button @click="toggleDetails(member.id)"
                    class="flex items-center space-x-1 text-gray-600 hover:text-gray-800">
                    <i :class="isDetailsVisible(member.id) ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                </td>
              </tr>
              <tr v-if="isDetailsVisible(member.id)" class="bg-gray-50">
                <td colspan="5" class="px-6 py-4 text-sm text-gray-700">
                  <div><strong>Name:</strong> {{ member.name }}</div>
                  <div><strong>Email:</strong> {{ member.email }}</div>
                  <div><strong>Phone:</strong> {{ member.phone || 'No phone' }}</div>
                  <div><strong>Website:</strong> {{ member.website || 'No website' }}</div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'MembersPageTable',
  data() {
    return {
      members: [],
      formMember: { name: '', email: '', phone: '', website: '' },
      isEditing: false,
      showForm: false,
      editId: null,
      detailsVisibleIds: [],
      searchQuery: '', // Search term
    }
  },
  computed: {
    filteredMembers() {
      const q = this.searchQuery.trim().toLowerCase()
      if (!q) return this.members
      return this.members.filter(member =>
        member.name.toLowerCase().includes(q) ||
        member.email.toLowerCase().includes(q)
      )
    }
  },
  async mounted() {
    await this.fetchMembers()
  },
  methods: {
    async fetchMembers() {
      try {
        const response = await fetch('https://jsonplaceholder.typicode.com/users')
        this.members = await response.json()
      } catch (error) {
        console.error('Error fetching members:', error)
      }
    },
    toggleForm() {
      if (this.showForm) {
        this.cancelForm()
      } else {
        this.showForm = true
        this.isEditing = false
        this.formMember = { name: '', email: '', phone: '', website: '' }
        this.editId = null
      }
    },
    startEdit(member) {
      this.isEditing = true
      this.showForm = true
      this.formMember = { ...member }
      this.editId = member.id
    },
    cancelForm() {
      this.showForm = false
      this.isEditing = false
      this.formMember = { name: '', email: '', phone: '', website: '' }
      this.editId = null
    },
    async submitForm() {
      if (this.isEditing) {
        try {
          const response = await fetch(`https://jsonplaceholder.typicode.com/users/${this.editId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.formMember)
          })
          const updatedMember = await response.json()
          const index = this.members.findIndex(m => m.id === this.editId)
          if (index !== -1) {
            this.members.splice(index, 1, updatedMember)
          }
          this.cancelForm()
        } catch (error) {
          console.error('Error updating member:', error)
        }
      } else {
        try {
          const response = await fetch('https://jsonplaceholder.typicode.com/users', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.formMember)
          })
          const newMember = await response.json()
          this.members.push({ ...this.formMember, id: newMember.id || Date.now() })
          this.cancelForm()
        } catch (error) {
          console.error('Error creating member:', error)
        }
      }
    },
    async deleteMember(id) {
      if (confirm('Are you sure you want to delete this member?')) {
        try {
          await fetch(`https://jsonplaceholder.typicode.com/users/${id}`, { method: 'DELETE' })
          this.members = this.members.filter(member => member.id !== id)
          this.detailsVisibleIds = this.detailsVisibleIds.filter(visibleId => visibleId !== id)
          if (this.editId === id) {
            this.cancelForm()
          }
        } catch (error) {
          console.error('Error deleting member:', error)
        }
      }
    },
    toggleDetails(id) {
      const index = this.detailsVisibleIds.indexOf(id)
      if (index === -1) {
        this.detailsVisibleIds.push(id)
      } else {
        this.detailsVisibleIds.splice(index, 1)
      }
    },
    isDetailsVisible(id) {
      return this.detailsVisibleIds.includes(id)
    },
  }
}
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

/* Fade transition for form */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
