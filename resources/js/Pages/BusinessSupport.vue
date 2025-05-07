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
            <div class="flex flex-wrap justify-center flex-col items-center 
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
        title: 'Business Process Review',
        description: 'Streamline Operations. Optimize Performance. Drive Results.Zurit Consulting offers a practical and results- oriented Business Process Review service designed to help SMEs and organizations enhance operational efficiency, eliminate bottlenecks, and improve overall productivity. This is a hands - on engagement where we work closely with your team to assess and improve how your business runs.',
        details: [
            'Comprehensive mapping and analysis of current business processes',
            'Identification of inefficiencies, duplications, and bottlenecks',
            'Clear, actionable recommendations for process improvement',
            'Implementation strategies, KPIs, and performance measurement frameworks'
        ],
        attends: [
            'SME founders and senior leaders',
            'Operations managers and business unit heads',
            'Teams responsible for process improvement and organizational effectiveness',
        ],
        more_info: 'pricing scales with scope and size'
    },
    {
        id: 2,
        type: 'sme',
        imageUrl: '/images/training/businessSupport/biz1.avif',
        title: 'Business & Strategic Plans',
        description: 'Build with Clarity. Grow with Purpose. Zurit Consulting offers expert support in the development of comprehensive business and strategic plans that fuel growth, guide execution, and position your enterprise for long - term success. Whether you`re launching, scaling, or repositioning, we help you craft a strategy that`s grounded in insight and aligned with your ambitions.',
        details: [
            'Market analysis and strategic vision development',
            'Clear goal setting and growth roadmap design',
            'Financial projections and risk management planning',
            'Actionable implementation steps and monitoring tools'
        ],
        attends: [
            'SME owners and founders',
            'Senior managers and department heads',
            'Entrepreneurs seeking structured business growth',
            'Organizations preparing for fundraising, investment, or expansion'
        ],
        price: '15,000'
    },
    {
        id: 3,
        type: 'sme',
        imageUrl: '/images/training/businessSupport/biz3.avif',
        title: 'Governance Review',
        description: 'Strengthen Accountability. Enhance Transparency. Build Trust. Zurit Consulting offers a thorough Governance Review service tailored to help organizations assess and strengthen their governance frameworks.This service is designed to support sustainable leadership, ethical decision- making, and regulatory compliance. Whether you`re a growing company, a regulated institution, or a board seeking performance improvement, we provide clarity and direction.',
        details: [
            'Assessment of board composition, roles, and practices',
            'Evaluation of internal controls and risk oversight mechanisms',
            'Review of compliance with corporate governance standards and regulatory frameworks',
            'Tailored recommendations for governance enhancement and implementation priorities'
        ],
        attends: [
            'Corporate Boards and Board Committees',
            'SACCO and cooperative leadership teams',
            'Governance and compliance officers',
            'NGOs, trusts, and institutions undergoing growth or regulatory scrutiny'
        ],
        price: '15,000',
        more_info: 'Custom packages available for full governance audits or board training sessions'
    },
    {
        id: 4,
        type: 'sme',
        imageUrl: '/images/training/advisory/advisory1.avif',
        title: 'Research & Feasibility Review',
        description: 'Validate Ideas. Reduce Risk. Make Informed Decisions. Zurit Consulting offers comprehensive research and feasibility analysis services to help businesses evaluate new projects, ventures, or expansion opportunities with clarity and confidence. We transform raw ideas into well- informed decisions through market intelligence, risk analysis, and strategic evaluation.',
        details: [
            'Market and competitor research to assess demand, positioning, and entry potential',
            'Feasibility analysis covering operational, technical, and financial viability',
            'Cost-benefit evaluation for resource planning and ROI estimation',
            'Clear, actionable strategic recommendations tailored to your business goals'
        ],
        attends: [
            'Corporate project and product development teams',
            'SME founders exploring new ventures',
            'Business analysts, strategists, and innovation leads',
            'NGOs or donor-funded programs evaluating scalable solutions'
        ],
        price: '15,000',
        more_info: 'Custom pricing available for complex or sector-specific feasibility studies'
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
