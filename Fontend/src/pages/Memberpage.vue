<template>
  <div class="min-h-screen bg-white">
    <main class="container mx-auto px-4 py-6">
      <h1 class="text-2xl font-semibold text-gray-900 text-center mb-6">
        Team Members
      </h1>

      <!-- Search and Add Member Button -->
      <div
        class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-2"
      >
        <!-- <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by first name, last name or email"
          class="w-full sm:w-1/3 border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
        /> -->
        <button
          @click="toggleForm"
          class="bg-blue-600 text-white text-sm font-medium rounded-md py-2 px-4 hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
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

      <!-- Create/Edit Member Form -->
      <transition name="fade">
        <div
          v-if="showForm"
          class="bg-white border border-gray-200 rounded-lg p-5 mb-8"
        >
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            {{ isEditing ? "Edit Member" : "Add New Member" }}
          </h2>
          <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-4">
            <div>
              <label
                for="firstname"
                class="block text-sm font-medium text-gray-700"
                >First Name</label
              >
              <input
                v-model="formMember.firstname"
                id="firstname"
                type="text"
                placeholder="Enter first name"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
                required
              />
            </div>
            <div>
              <label
                for="lastname"
                class="block text-sm font-medium text-gray-700"
                >Last Name</label
              >
              <input
                v-model="formMember.lastname"
                id="lastname"
                type="text"
                placeholder="Enter last name"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
                required
              />
            </div>
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700"
                >Email</label
              >
              <input
                v-model="formMember.email"
                id="email"
                type="email"
                placeholder="Enter email"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
                required
              />
            </div>
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700"
                >Phone</label
              >
              <input
                v-model="formMember.phone"
                id="phone"
                type="text"
                placeholder="Enter phone"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label
                for="address"
                class="block text-sm font-medium text-gray-700"
                >Address</label
              >
              <input
                v-model="formMember.address"
                id="address"
                type="text"
                placeholder="Enter address"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label for="dob" class="block text-sm font-medium text-gray-700"
                >Date of Birth</label
              >
              <input
                v-model="formMember.dob"
                id="dob"
                type="date"
                class="mt-1 w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="cancelForm"
                class="bg-gray-100 text-gray-700 text-sm rounded-md py-2 px-4 hover:bg-gray-200 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="bg-blue-600 text-white text-sm rounded-md py-2 px-4 hover:bg-blue-700 transition-colors"
              >
                {{ isEditing ? "Save" : "Create" }}
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
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Name
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Email
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Phone
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Address
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="member in filteredMembers"
              :key="member.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 text-sm font-medium text-gray-900">
                {{ member.firstname }} {{ member.lastname }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ member.email }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ member.phone || "No phone" }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ member.address || "No address" }}
              </td>
              <td class="px-6 py-4 text-sm flex gap-3">
                <button
                  @click="startEdit(member)"
                  class="text-blue-600 hover:text-blue-800"
                  title="Edit"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button
                  @click="deleteMember(member.id)"
                  class="text-red-600 hover:text-red-800"
                  title="Delete"
                >
                  <i class="fas fa-trash"></i>
                </button>
                <button
                  @click="showMemberDetail(member.id)"
                  class="text-green-600 hover:text-green-800"
                  title="Show Detail"
                >
                  <i class="fas fa-eye"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Member Detail Modal -->
      <div
        v-if="showDetailModal"
        class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm"
      >
        <div
          class="bg-white rounded-xl shadow-xl max-w-md w-full p-8 relative border border-gray-300"
        >
          <!-- Close Button -->
          <button
            @click="closeDetailModal"
            aria-label="Close modal"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-900 transition"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>

          <h2 class="text-2xl font-semibold mb-6 text-gray-900 border-b pb-2">
            Member Details
          </h2>

          <div v-if="selectedMember" class="space-y-4 text-gray-700 text-sm">
            <div>
              <span class="font-semibold text-gray-900">First Name:</span>
              {{ selectedMember.firstname }}
            </div>
            <div>
              <span class="font-semibold text-gray-900">Last Name:</span>
              {{ selectedMember.lastname }}
            </div>
            <div>
              <span class="font-semibold text-gray-900">Email:</span>
              <a
                :href="`mailto:${selectedMember.email}`"
                class="text-blue-600 hover:underline"
                >{{ selectedMember.email }}</a
              >
            </div>
            <div>
              <span class="font-semibold text-gray-900">Phone:</span>
              {{ selectedMember.phone || "No phone" }}
            </div>
            <div>
              <span class="font-semibold text-gray-900">Address:</span>
              {{ selectedMember.address || "No address" }}
            </div>
            <div>
              <span class="font-semibold text-gray-900">Date of Birth:</span>
              {{ selectedMember.dob || "N/A" }}
            </div>
            <div>
              <span class="font-semibold text-gray-900">Created At:</span>
              {{ new Date(selectedMember.created_at).toLocaleString() }}
            </div>
            <div>
              <span class="font-semibold text-gray-900">Updated At:</span>
              {{ new Date(selectedMember.updated_at).toLocaleString() }}
            </div>
          </div>

          <div v-else class="text-center text-gray-500 py-10">
            Loading details...
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
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
  },
  methods: {
    async fetchMembers() {
      try {
        const res = await fetch("http://192.168.108.14:8000/api/members");
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
        ? `http://192.168.108.14:8000/api/members/update/${this.editId}`
        : `http://192.168.108.14:8000/api/members/create`;

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
          await fetch(`http://192.168.108.14:8000/api/members/delete/${id}`, {
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
        const res = await fetch(`http://192.168.108.14:8000/api/members/${id}`);
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
  transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

/* Modal styles */
.fixed {
  z-index: 1000;
}
</style>
<script setup>

</script scoped>

<style>

</style>
