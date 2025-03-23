<template>

    <Head title="Advisory" />
    <Navbar />
    <div class="mt-36 mb-24">
        <div class="text-2xl md:ml-8 md:text-4xl lg:text-7xl md:text-left text-center font-bold lg:mb-6">
            Get Advisory <span class="text-yellow-600">Help Today</span>
        </div>
        <div class="max-w-6xl mx-auto p-6">
            <!-- Advisory Cards Grid -->
            <div class="flex justify-center flex-col items-center lg:flex-row gap-6 max-w-7xl">
                <ServiceCard v-for="service in advisoryServices" :key="service.id" :imageUrl="service.imageUrl"
                    :title="service.title" :description="service.description" :price="service.price"
                    @learn-more="openModal(service)" class="w-full md:w-[48%] lg:w-[31%]" />
            </div>

            <!-- Advisory Details Modal -->
            <AdvisoryModal :is-open="isModalOpen" :service="selectedService" @close="closeModal"
                @enroll="openContactForm" />

            <!-- Contact Form Modal -->
            <EnrollModal :is-open="isContactFormOpen" @close="closeContactForm" @submit="handleContactSubmit" />
        </div>
    </div>
    <Footer />
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/Footer.vue'
import ServiceCard from '@/Components/Shared/ServiceCard.vue'
import AdvisoryModal from '@/Components/Shared/AdvisoryModal.vue'
import EnrollModal from '@/Components/Shared/EnrollModal.vue'

// Sample advisory services data
const advisoryServices = [
    {
        id: 1,
        imageUrl: '/images/advisory/financial_planning.jpg',
        title: 'Financial Planning Advisory',
        description: 'Expert guidance on managing your personal finances and investments for a secure future.',
        price: 'Free Consultation'
    },
    {
        id: 2,
        imageUrl: '/images/advisory/business_strategy.jpg',
        title: 'Business Strategy Advisory',
        description: 'Strategic insights to help your business grow, optimize operations, and achieve long-term success.',
        price: 'Starting at $500'
    },
    {
        id: 3,
        imageUrl: '/images/advisory/retirement_planning.jpg',
        title: 'Retirement Planning Advisory',
        description: 'Tailored advisory services to prepare for a financially stable and comfortable retirement.',
        price: 'Consultation at $300'
    },
    // Add more advisory services as needed...
]

const isModalOpen = ref(false)
const selectedService = ref(null)
const isContactFormOpen = ref(false)

const openModal = (service) => {
    selectedService.value = service
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedService.value = null
}

const openContactForm = () => {
    isModalOpen.value = false
    isContactFormOpen.value = true
}

const closeContactForm = () => {
    isContactFormOpen.value = false
}

const handleContactSubmit = (formData) => {
    console.log('Contact form submitted:', formData)
    // Handle submission logic (e.g., API call)
    closeContactForm()
}
</script>
