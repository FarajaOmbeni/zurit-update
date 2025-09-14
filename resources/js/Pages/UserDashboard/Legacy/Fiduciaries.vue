<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue'
import LegacyProgress from '@/Components/Legacy/LegacyProgress.vue'
import Input from '@/Components/Shared/Input.vue'
import Select from '@/Components/Shared/Select.vue'
import Alert from '@/Components/Shared/Alert.vue'
import { useAlert } from '@/Components/Composables/useAlert'
import { ref, computed } from 'vue'
import { PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  // Expect an array of records from the backend:
  // [{ id, institution_type, institution_name, contact_name, email, phone }, ...]
  fiduciaries: { type: Array, default: () => [] },
})

const { openAlert, clearAlert, alertState } = useAlert()

// Kenya corporate trustee directory
const DIRECTORY = [
  { value: 'Co-operative Bank of Kenya', label: 'Co-operative Bank of Kenya — Corporate Trustee Services', website: 'https://www.co-opbank.co.ke/corporate/corporate-trustee-services/' },
  { value: 'KCB Bank Kenya', label: 'KCB Bank Kenya — Trustee Services', website: 'https://ke.kcbgroup.com/for-corporates/services/corporate-trustee' },
  { value: 'ICEA LION Corporate Trustee Services', label: 'ICEA LION — Corporate Trustee Services', website: 'https://icealion.co.ke/wp-content/uploads/2021/04/ICEA-LION-CORPORATE-TRUSTEE-SERVICES.pdf' },
  { value: 'MTC Trust & Corporate Services Limited', label: 'MTC Trust & Corporate Services Limited', website: 'https://www.meghraj.com/businesses/mtc-trust-kenyan-fiduciary-services/about-us/' },
  { value: 'NCBA Bank Kenya', label: 'NCBA Bank Kenya — Corporate Trustee Services', website: 'https://licensees.cma.or.ke/licenses/18/' },
  { value: 'Standard Chartered Bank Kenya', label: 'Standard Chartered Bank Kenya — Corporate Trustee Services', website: 'https://www.cma.or.ke/cma-licenses-three-new-corporate-trustees/' },
  { value: 'Stanbic Bank Kenya', label: 'Stanbic Bank Kenya — Corporate Trustee Services', website: 'https://licensees.cma.or.ke/licenses/18/' },
  { value: 'Kingsland Court Trustee Services Limited', label: 'Kingsland Court Trustee Services Limited', website: 'https://kingslandcourt.co.ke/' },
  { value: 'Corporate & Pension Trust Services Ltd (Zamara)', label: 'Zamara — Corporate & Pension Trust Services Ltd (C&P)', website: 'https://zamaragroup.com/ke/our-solutions/corporate-pension-trustee-services-kenya/' },
  { value: 'Natbank Trustees & Investment Services Ltd (NBK)', label: 'Natbank Trustees & Investment Services Ltd (NBK)', website: 'https://www.nationalbank.co.ke/home-ntisl' },
  { value: '__other__', label: 'Other (manual entry)' },
]
const directoryOptions = computed(() => DIRECTORY.map(d => ({ value: d.value, label: d.label })))

const showForm = ref(false)
const showDeleteModal = ref(false)
const editingFiduciary = ref(null) // holds the record when editing
const fiduciaryToDelete = ref(null)

const form = useForm({
  institution_type: '',   // e.g., "Corporate Trustee"
  institution_name: '',   // required
  contact_name: '',
  email: '',
  phone: '',
})

function onPickFromDirectory(val) {
  if (val === '__other__') return
  const picked = DIRECTORY.find(d => d.value === val)
  if (picked) {
    form.institution_name = picked.value
    // opinionated default
    if (!form.institution_type) form.institution_type = 'Corporate Trustee'
  }
}

function startAdd() {
  editingFiduciary.value = null
  form.reset()
  showForm.value = true
}

function startEdit(fid) {
  editingFiduciary.value = fid
  form.institution_type = fid.institution_type || ''
  form.institution_name = fid.institution_name || ''
  form.contact_name = fid.contact_name || ''
  form.email = fid.email || ''
  form.phone = fid.phone || ''
  showForm.value = true
}

function cancelEdit() {
  editingFiduciary.value = null
  form.reset()
  showForm.value = false
}

function submitForm() {
  // Mirror Assets page: if editing -> PUT; else -> POST
  if (editingFiduciary.value) {
    form.put(route('legacy.fiduciaries.update', editingFiduciary.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        showForm.value = false
        editingFiduciary.value = null
        openAlert('success', 'Fiduciary updated successfully!', 5000)
      },
      onError: (errors) => {
        const msg = Object.values(errors || {}).flat().join(' ')
        openAlert('danger', msg || 'Could not update fiduciary.', 10000)
      },
    })
  } else {
    form.post(route('legacy.fiduciaries.save'), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset()
        showForm.value = false
        openAlert('success', 'Fiduciary added successfully!', 5000)
      },
      onError: (errors) => {
        const msg = Object.values(errors || {}).flat().join(' ')
        openAlert('danger', msg || 'Could not add fiduciary.', 10000)
      },
    })
  }
}

function confirmDelete(fid) {
  fiduciaryToDelete.value = fid
  showDeleteModal.value = true
}

function deleteFiduciary() {
  if (!fiduciaryToDelete.value) return
  form.delete(route('legacy.fiduciaries.destroy', fiduciaryToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false
      fiduciaryToDelete.value = null
      openAlert('success', 'Fiduciary deleted successfully!', 5000)
    },
    onError: (errors) => {
      const msg = Object.values(errors || {}).flat().join(' ')
      openAlert('danger', msg || 'Could not delete fiduciary.', 10000)
    },
  })
}

function cancelDelete() {
  showDeleteModal.value = false
  fiduciaryToDelete.value = null
}

const count = computed(() => props.fiduciaries.length)
</script>

<template>
  <Head title="Fiduciaries - Legacy & Estate Planning" />
  <AuthenticatedLayout>
    <div class="w-full text-gray-900">
      <Sidebar>
        <DashboardBackButton />

        <div class="max-w-6xl mx-auto p-6">
          <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                 :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />

          <!-- Header (mirrors Assets) -->
          <div class="flex justify-between items-center mb-8">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 mb-2">Step 3: Fiduciaries (Companies)</h1>
              <p class="text-gray-600">Add corporate trustee / fiduciary companies relevant to your estate plan.</p>
            </div>
            <div class="text-right">
              <div class="text-sm text-gray-500 mb-1">Companies Added</div>
              <div class="text-2xl font-bold" :class="count ? 'text-purple-600' : 'text-gray-400'">
                {{ count }}
              </div>
            </div>
          </div>

          <LegacyProgress :current="3" />

          <!-- Add Button -->
          <div class="mb-6">
            <button
              @click="showForm ? (editingFiduciary ? cancelEdit() : showForm = false) : startAdd()"
              class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
              <PlusIcon class="w-5 h-5 mr-2" />
              {{ showForm ? 'Cancel' : 'Add Fiduciary Company' }}
            </button>
          </div>

          <!-- Add/Edit Form (same outline & handlers as Assets) -->
          <div v-if="showForm" class="bg-white rounded-lg shadow-sm border p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              {{ editingFiduciary ? 'Edit Fiduciary' : 'Add New Fiduciary' }}
            </h3>

            <form @submit.prevent="submitForm" class="space-y-4">
              <div class="grid md:grid-cols-2 gap-4">
                <Select
                  label="Pick from Kenya directory (or choose 'Other')"
                  :options="directoryOptions"
                  @update:modelValue="onPickFromDirectory"
                />
                <Input
                  v-model="form.institution_name"
                  label="Institution Name"
                  placeholder="e.g., Co-operative Bank of Kenya"
                  :error="form.errors?.institution_name"
                  required
                />
              </div>

              <div class="grid md:grid-cols-3 gap-4">
                <Input
                  v-model="form.institution_type"
                  label="Institution Type (optional)"
                  placeholder="e.g., Corporate Trustee"
                  :error="form.errors?.institution_type"
                />
                <Input
                  v-model="form.contact_name"
                  label="Contact Person (optional)"
                  placeholder="e.g., Jane Doe"
                  :error="form.errors?.contact_name"
                />
                <Input
                  v-model="form.email"
                  label="Email (optional)"
                  placeholder="name@company.co.ke"
                  :error="form.errors?.email"
                />
              </div>

              <div class="grid md:grid-cols-3 gap-4">
                <Input
                  v-model="form.phone"
                  label="Phone (optional)"
                  placeholder="+254 ..."
                  :error="form.errors?.phone"
                />
              </div>

              <div class="flex space-x-4">
                <button type="submit" :disabled="form.processing"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                  {{ form.processing
                      ? (editingFiduciary ? 'Updating...' : 'Adding...')
                      : (editingFiduciary ? 'Update Fiduciary' : 'Add Fiduciary') }}
                </button>
                <button type="button" @click="editingFiduciary ? cancelEdit() : (showForm = false)"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                  Cancel
                </button>
              </div>
            </form>
          </div>

          <!-- List (mirrors Assets cards) -->
          <div v-if="props.fiduciaries.length" class="space-y-4 mb-8">
            <h3 class="text-lg font-semibold text-gray-900">Your Fiduciary Companies</h3>

            <div class="grid gap-4">
              <div v-for="fid in props.fiduciaries" :key="fid.id"
                   class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h4 class="text-lg font-semibold text-gray-900">
                      {{ fid.institution_name }}
                    </h4>
                    <div class="text-sm text-gray-600 mt-1 space-y-0.5">
                      <div v-if="fid.institution_type"><span class="font-medium">Type:</span> {{ fid.institution_type }}</div>
                      <div v-if="fid.contact_name"><span class="font-medium">Contact:</span> {{ fid.contact_name }}</div>
                      <div v-if="fid.email"><span class="font-medium">Email:</span> {{ fid.email }}</div>
                      <div v-if="fid.phone"><span class="font-medium">Phone:</span> {{ fid.phone }}</div>
                    </div>
                  </div>

                  <div class="flex items-center space-x-2 ml-4">
                    <button @click="startEdit(fid)"
                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                            title="Edit">
                      <PencilIcon class="w-5 h-5" />
                    </button>
                    <button @click="confirmDelete(fid)"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                            title="Delete">
                      <TrashIcon class="w-5 h-5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div v-else class="text-center py-12">
            <h3 class="text-lg font-medium text-gray-900 mb-2">No fiduciary companies added yet</h3>
            <p class="text-gray-600 mb-6">Add at least one company to proceed.</p>
            <button @click="startAdd"
                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
              <PlusIcon class="w-5 h-5 mr-2" />
              Add Your First Company
            </button>
          </div>

          <!-- Wizard footer (like Assets) -->
          <div class="flex items-center justify-between pt-8 border-t">
            <Link :href="route('legacy.beneficiaries')"
                  class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
              ← Back to Beneficiaries
            </Link>
            <Link :href="route('legacy.insurance')"
                  class="bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
              Continue to Insurance →
            </Link>
          </div>
        </div>
      </Sidebar>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
              <TrashIcon class="w-6 h-6 text-red-600" />
            </div>
            <div class="ml-3">
              <h3 class="text-lg font-semibold text-gray-900">Delete Fiduciary</h3>
            </div>
          </div>

          <div class="mb-6">
            <p class="text-gray-600">
              Are you sure you want to delete <strong>{{ fiduciaryToDelete?.institution_name }}</strong>?
              This action cannot be undone.
            </p>
          </div>

          <div class="flex space-x-4">
            <button @click="deleteFiduciary" :disabled="form.processing"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
              {{ form.processing ? 'Deleting...' : 'Delete' }}
            </button>
            <button @click="cancelDelete"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
