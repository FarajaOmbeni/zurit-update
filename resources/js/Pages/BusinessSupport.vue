<template>

    <Head title="Advisory" />
    <Navbar />
    <div class="mt-36 mb-24">
        <div class="text-2xl md:ml-8 md:text-4xl lg:text-7xl md:text-left text-center font-bold lg:mb-6">
            Get Business <span class="text-yellow-600">Support Today</span>
        </div>
        <div class="max-w-6xl mx-auto p-6">
            <!-- Navigation Tabs -->
            <div class="grid grid-cols-2 sm:flex gap-x-6 gap-y-2 mb-8 rounded-lg overflow-hidden">
                <TrainingTab v-for="tab in tabs" :key="tab.id" :label="tab.label" :isActive="activeTab === tab.id"
                    @click="activeTab = tab.id" />
            </div>

            <!-- Training Cards Flexbox -->
            <div class="flex justify-center flex-col items-center 
            lg:flex-row gap-6 max-w-7xl">
                <TrainingCard v-for="training in filteredTrainings" :key="training.id" :imageUrl="training.imageUrl"
                    :title="training.title" :description="training.description" :price="training.price"
                    @learn-more="openModal(training)" class="w-full md:w-[48%] lg:w-[31%]" />
            </div>

            <!-- Modal -->
            <TrainingModal :is-open="isModalOpen" :training="selectedTraining" @close="closeModal"
                @enroll="openContactForm" />

            <!-- Contact Form Modal -->
            <EnrollModal :is-open="isContactFormOpen" @close="closeContactForm" @submit="handleContactSubmit" />
        </div>
    </div>
    <Footer />
</template>

<script setup>
import { ref, computed } from 'vue'
import TrainingTab from '@/Components/Shared/TrainingTab.vue'
import TrainingCard from '@/Components/Shared/TrainingCard.vue'
import TrainingModal from '@/Components/Shared/TrainingModal.vue'
import EnrollModal from '@/Components/Shared/EnrollModal.vue'
import Footer from '@/Components/Footer.vue'
import Navbar from '@/Components/Navbar.vue'
import { Head } from '@inertiajs/vue3'

// Sample data structure with more details
const trainings = [
    {
        id: 1,
        type: 'sme',
        imageUrl: '/images/training/businessSupport/biz1.avif',
        title: 'Training',
        description: 'A comprehensive workshop designed to build foundational business skills for SME owners. The session covers key topics from operational efficiency to strategic planning, ensuring participants gain practical insights to grow their businesses.',
        details: [
            'Introduction to business strategy',
            'Operational and financial management fundamentals',
            'Market analysis and competitive positioning',
            'Interactive case studies and group discussions'
        ],
        attend: 'Small and medium enterprise owners, managers, and emerging business leaders',
        price: '15,000'
    },
    {
        id: 2,
        type: 'sme',
        imageUrl: '/images/training/businessSupport/biz1.avif',
        title: 'Tax Planning and Compliance',
        description: 'This course provides an in-depth overview of tax regulations and practical strategies for tax planning. Participants will learn how to navigate complex tax laws, optimize deductions, and ensure compliance with statutory requirements.',
        details: [
            'Overview of current tax laws and regulations',
            'Strategies for tax optimization and savings',
            'Compliance best practices and audit preparation',
            'Real-life case studies and Q&A sessions'
        ],
        attend: 'Accountants, business owners, financial managers, and tax professionals',
        price: '15,000'
    },
    {
        id: 3,
        type: 'sme',
        imageUrl: '/images/training/businessSupport/biz3.avif',
        title: 'Book Keeping',
        description: 'Learn the essential skills of bookkeeping in this hands-on course. The training covers everything from basic ledger management to modern bookkeeping software, enabling participants to maintain accurate financial records for their businesses.',
        details: [
            'Fundamentals of bookkeeping and record keeping',
            'Practical use of bookkeeping software',
            'Invoicing, reconciliation, and financial reporting',
            'Tips for maintaining error-free financial records'
        ],
        attend: 'Small business owners, accounting personnel, and finance staff',
        price: '15,000'
    },
    {
        id: 4,
        type: 'sme',
        imageUrl: '/images/training/advisory/advisory1.avif',
        title: 'Business Compliance',
        description: 'This training is tailored to help businesses understand and implement robust compliance practices. It focuses on risk management, regulatory frameworks, and internal controls to ensure that business operations adhere to legal and ethical standards.',
        details: [
            'Understanding regulatory frameworks and legal obligations',
            'Developing and implementing compliance strategies',
            'Risk management and internal control systems',
            'Case studies on business compliance failures and successes'
        ],
        attend: 'Compliance officers, legal advisors, business owners, and risk management professionals',
        price: '15,000'
    },
];


const tabs = [
    { id: 'sme', label: 'SME' },
]

const activeTab = ref('sme')
const isModalOpen = ref(false)
const selectedTraining = ref(null)
const isTrainingModalOpen = ref(false)
const isContactFormOpen = ref(false)

const filteredTrainings = computed(() => {
    return trainings.filter(training => training.type === activeTab.value)
})

const openModal = (training) => {
    selectedTraining.value = training
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedTraining.value = null
}

const handleEnroll = (trainingId) => {
    // Handle enrollment logic
    console.log('Enrolling in training:', trainingId)
    closeModal()
}

const openTrainingModal = (training) => {
    selectedTraining.value = training
    isTrainingModalOpen.value = true
}

const closeTrainingModal = () => {
    isTrainingModalOpen.value = false
    selectedTraining.value = null
}

const openContactForm = () => {
    isTrainingModalOpen.value = false
    isContactFormOpen.value = true
}

const closeContactForm = () => {
    isContactFormOpen.value = false
}

const handleContactSubmit = (formData) => {
    console.log('Form submitted:', formData)
    // Handle form submission (e.g., API call)
    closeContactForm()
    // Optionally show a success message
}
</script>
