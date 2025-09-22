<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminSidebar from '@/Components/AdminSidebar.vue'

const props = defineProps({
  users: { type: Array, default: () => [] },
  courses: { type: Array, default: () => [] },
  access: { type: Array, default: () => [] },
})

const form = useForm({
  user_id: '',
  course_id: '',
})

// User typeahead state
const userQuery = ref('')
const showUserList = ref(false)

const filteredUsers = computed(() => {
  const q = userQuery.value.trim().toLowerCase()
  if (!q) return props.users.slice(0, 10)
  return props.users
    .filter(
      (u) =>
        (u.name && u.name.toLowerCase().includes(q)) ||
        (u.email && u.email.toLowerCase().includes(q)),
    )
    .slice(0, 10)
})

function selectUser(u) {
  form.user_id = u.id
  userQuery.value = `${u.name} - ${u.email}`
  showUserList.value = false
}

function onUserInput() {
  form.user_id = ''
  showUserList.value = true
}

function grantAccess() {
  form.post(route('admin.courses.access.grant'), {
    preserveScroll: true,
    onError: () => {
      showUserList.value = true
    },
  })
}

function revokeAccess(id) {
  if (!confirm('Revoke access?')) return
  useForm({}).delete(route('admin.courses.access.revoke', { id }), {
    preserveScroll: true,
  })
}
</script>

<template>

  <Head title="Course Access" />

  <!-- ✅ Use AdminSidebar as a layout and pass content via the slot -->
  <AdminSidebar title="Manage Course Access">
    <div class="max-w-5xl mx-auto">
      <!-- Removed the extra <h1>; AdminSidebar shows the page title -->

      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Grant Access</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
            <input v-model="userQuery" @focus="showUserList = true" @input="onUserInput" type="text"
              placeholder="Type name or email…"
              class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
              autocomplete="off" />
            <div v-if="showUserList"
              class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-md shadow max-h-60 overflow-auto">
              <button v-for="u in filteredUsers" :key="u.id" type="button" @mousedown.prevent="selectUser(u)"
                class="w-full text-left px-3 py-2 hover:bg-indigo-50">
                <div class="text-sm text-gray-900">{{ u.name }}</div>
                <div class="text-xs text-gray-500">{{ u.email }}</div>
              </button>
              <div v-if="filteredUsers.length === 0" class="px-3 py-2 text-sm text-gray-500">
                No matches
              </div>
            </div>
            <div v-if="form.errors.user_id" class="text-sm text-red-600 mt-1">
              {{ form.errors.user_id }}
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
            <select v-model="form.course_id"
              class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
              <option value="" disabled>Select course</option>
              <option v-for="c in props.courses" :key="c.id" :value="c.id">
                {{ c.title }} (KES {{ (c.price || 0).toLocaleString() }})
              </option>
            </select>
            <div v-if="form.errors.course_id" class="text-sm text-red-600 mt-1">
              {{ form.errors.course_id }}
            </div>
          </div>

          <div>
            <button @click="grantAccess" :disabled="form.processing || !form.user_id || !form.course_id"
              class="w-full md:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md disabled:opacity-50">
              Grant Access
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Current Access</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  User
                </th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Email
                </th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Course
                </th>
                <th class="px-4 py-2"></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="row in props.access" :key="row.id">
                <td class="px-4 py-2 text-sm text-gray-900">{{ row.user_name }}</td>
                <td class="px-4 py-2 text-sm text-gray-500">{{ row.user_email }}</td>
                <td class="px-4 py-2 text-sm text-gray-900">{{ row.course_title }}</td>
                <td class="px-4 py-2 text-right">
                  <button @click="revokeAccess(row.id)"
                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                    Revoke
                  </button>
                </td>
              </tr>
              <tr v-if="props.access.length === 0">
                <td class="px-4 py-6 text-center text-gray-500" colspan="4">
                  No access records yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminSidebar>
</template>
