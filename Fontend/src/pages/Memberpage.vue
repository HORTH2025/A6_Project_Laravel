<template>
  <div class="">
    <main class="container">
      <h1 class="text-3xl font-semibold text-gray-900 text-center mb-8">Team Members</h1>

      <!-- Search and Add Button -->
      <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4 mx-15">
        <input v-model="searchQuery" type="text" placeholder="🔍 Search by first name, last name or email"
          class="w-full sm:w-1/2 border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
        <button @click="toggleForm"
          class="bg-blue-600 text-white text-sm font-semibold rounded-md py-3 px-6 hover:bg-blue-700 transition">
          {{
            showForm
              ? isEditing
                ? "Close Edit Form"
                : "Close Create Form"
              : isEditing
                ? "Edit Member"
                : "Add Member"
          }}
        </button>
      </div>

      <!-- Form Modal -->
      <transition name="fade">
        <div v-if="showForm" class="fixed inset-0 z-50 bg-black/30 bg-opacity-40 flex justify-center items-center p-10"
          role="dialog" aria-modal="true" aria-labelledby="form-title">
          <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-8 relative border border-gray-300"
            @keydown.escape.prevent="cancelForm">
            <button @click="cancelForm" aria-label="Close form"
              class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <h2 id="form-title" class="text-2xl font-semibold mb-6 text-gray-900 text-center">
              {{ isEditing ? "Edit Member" : "Add New Member" }}
            </h2>

            <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-5">
              <div>
                <label for="firstname" class="block text-sm font-medium text-gray-700">First Name</label>
                <input v-model="formMember.firstname" id="firstname" type="text" required
                  class="mt-1 w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
              </div>
              <div>
                <label for="lastname" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input v-model="formMember.lastname" id="lastname" type="text" required
                  class="mt-1 w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input v-model="formMember.email" id="email" type="email" required
                  class="mt-1 w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
              </div>
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input v-model="formMember.phone" id="phone" type="text"
                  class="mt-1 w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
              </div>
              <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <input v-model="formMember.address" id="address" type="text"
                  class="mt-1 w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
              </div>
              <div>
                <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                <input v-model="formMember.dob" id="dob" type="date"
                  class="mt-1 w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
              </div>
              <div class="flex justify-end gap-4 mt-6">
                <button type="button" @click="cancelForm"
                  class="bg-gray-100 text-gray-700 text-sm rounded-md py-3 px-6 hover:bg-gray-200 transition">
                  Cancel
                </button>
                <button type="submit"
                  class="bg-blue-600 text-white text-sm rounded-md py-3 px-6 hover:bg-blue-700 transition">
                  {{ isEditing ? "Save" : "Create" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>

      <!-- Table -->
      <div class="bg-white border border-gray-200 rounded-lg  shadow-sm mx-15">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-blue-100">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase tracking-wide">
                ID
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase tracking-wide">
                Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase tracking-wide">
                Email
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase tracking-wide">
                Phone
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase tracking-wide">
                Address
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase tracking-wide">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(member, index) in filteredMembers" :key="member.id" class="hover:bg-blue-50">
              <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ index + 1 }}</td>
              <td class="px-6 py-4 text-sm font-medium text-gray-900">
                {{ member.firstname }} {{ member.lastname }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ member.email }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ member.phone || "No phone" }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ member.address || "No address" }}</td>
              <td class="px-6 py-4 text-sm relative">
                <div class="relative" @click.stop="toggleDropdown(member.id)">
                  <button class="text-gray-600 hover:text-gray-800 focus:outline-none p-2 rounded-md" title="Actions">
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <div v-if="activeDropdown === member.id"
                    class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded shadow-lg z-20">
                    <button @click="startEdit(member)"
                      class="block w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50">
                      <i class="fas fa-edit mr-2"></i>Edit
                    </button>
                    <button @click="deleteMember(member.id)"
                      class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                      <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                    <button @click="showMemberDetail(member.id)"
                      class="block w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                      <i class="fas fa-eye mr-2"></i>View
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Detail Modal -->
      <div v-if="showDetailModal"
        class="fixed inset-0 bg-black/30 bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm p-6"
        role="dialog" aria-modal="true" aria-labelledby="detail-title">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-8 relative border border-gray-300">
          <button @click="closeDetailModal" aria-label="Close modal"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-900 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <h2 id="detail-title" class="text-2xl font-semibold mb-6 text-gray-900 border-b pb-2 text-center">
            Member Details
          </h2>
          <div v-if="selectedMember" class="space-y-4 text-gray-700 text-sm">
            <div><strong>First Name:</strong> {{ selectedMember.firstname }}</div>
            <div><strong>Last Name:</strong> {{ selectedMember.lastname }}</div>
            <div><strong>Email:</strong> {{ selectedMember.email }}</div>
            <div><strong>Phone:</strong> {{ selectedMember.phone || "No phone" }}</div>
            <div><strong>Address:</strong> {{ selectedMember.address || "No address" }}</div>
            <div><strong>Date of Birth:</strong> {{ selectedMember.dob || "N/A" }}</div>
            <div>
              <strong>Created At:</strong>
              {{ new Date(selectedMember.created_at).toLocaleString() }}
            </div>
            <div>
              <strong>Updated At:</strong>
              {{ new Date(selectedMember.updated_at).toLocaleString() }}
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
const BASE_URL = "http://192.168.108.14:8000/api/members";

export default {
  data() {
    return {
      members: [],
      formMember: {
        firstname: "",
        lastname: "",
        email: "",
        phone: "",
        address: "",
        dob: "",
      },
      isEditing: false,
      showForm: false,
      editId: null,
      searchQuery: "",
      selectedMember: null,
      showDetailModal: false,
      activeDropdown: null,
    };
  },
  computed: {
    filteredMembers() {
      const q = this.searchQuery.toLowerCase().trim();
      return this.members.filter(
        (m) =>
          m.firstname.toLowerCase().includes(q) ||
          m.lastname.toLowerCase().includes(q) ||
          m.email.toLowerCase().includes(q)
      );
    },
  },
  mounted() {
    this.fetchMembers();
    document.addEventListener("click", this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
  },
  methods: {
    toggleDropdown(id) {
      this.activeDropdown = this.activeDropdown === id ? null : id;
    },
    handleClickOutside(event) {
      if (!event.target.closest(".relative")) {
        this.activeDropdown = null;
      }
    },
    async fetchMembers() {
      try {
        const res = await fetch(BASE_URL);
        const json = await res.json();
        this.members = json.data || [];
      } catch (error) {
        console.error("Error fetching members:", error);
      }
    },
    toggleForm() {
      this.showForm = !this.showForm;
      if (!this.showForm) this.cancelForm();
    },
    startEdit(member) {
      this.isEditing = true;
      this.showForm = true;
      this.formMember = { ...member };
      this.editId = member.id;
    },
    cancelForm() {
      this.isEditing = false;
      this.showForm = false;
      this.editId = null;
      this.formMember = {
        firstname: "",
        lastname: "",
        email: "",
        phone: "",
        address: "",
        dob: "",
      };
    },
    async submitForm() {
      const url = this.isEditing
        ? `${BASE_URL}/update/${this.editId}`
        : `${BASE_URL}/create`;
      const method = this.isEditing ? "PUT" : "POST";
      try {
        const res = await fetch(url, {
          method,
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.formMember),
        });
        const saved = await res.json();
        if (this.isEditing) {
          const i = this.members.findIndex((m) => m.id === this.editId);
          if (i !== -1) this.members[i] = saved.data;
        } else {
          this.members.push(saved.data);
        }
        this.cancelForm();
      } catch (error) {
        console.error("Error saving member:", error);
      }
    },
    async deleteMember(id) {
      if (confirm("Delete this member?")) {
        try {
          await fetch(`${BASE_URL}/delete/${id}`, {
            method: "DELETE",
          });
          this.members = this.members.filter((m) => m.id !== id);
        } catch (error) {
          console.error("Error deleting member:", error);
        }
      }
    },
    async showMemberDetail(id) {
      this.selectedMember = null;
      this.showDetailModal = true;
      try {
        const res = await fetch(`${BASE_URL}/${id}`);
        if (!res.ok) throw new Error("Failed to fetch member details");
        const json = await res.json();
        this.selectedMember = json.data;
      } catch (error) {
        console.error("Error loading member details:", error);
        alert("Failed to load member details.");
        this.showDetailModal = false;
      }
    },
    closeDetailModal() {
      this.showDetailModal = false;
      this.selectedMember = null;
    },
  },
};
</script>


<style scoped>
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css");

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
