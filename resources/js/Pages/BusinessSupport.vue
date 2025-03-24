<template>

    <Head title="Business Support" />
    <Navbar />
    <div class="mt-36 mb-24">
        <div class="text-2xl md:ml-8 md:text-4xl lg:text-7xl md:text-left text-center font-bold lg:mb-6">
            Get Business <span class="text-yellow-600">Support Today</span>
        </div>
        <div class="max-w-6xl mx-auto p-6">
            <!-- Business Support Cards Grid -->
            <div class="flex justify-center flex-col items-center lg:flex-row gap-6 max-w-7xl">
                <ServiceCard v-for="service in businessServices" :key="service.id" :imageUrl="service.imageUrl"
                    :title="service.title" :description="service.description" :price="service.price"
                    @learn-more="openModal(service)" class="w-full md:w-[48%] lg:w-[31%]" />
            </div>

            <!-- Business Support Details Modal -->
            <BusinessSupportModal :is-open="isModalOpen" :service="selectedService" @close="closeModal"
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
import BusinessSupportModal from '@/Components/Shared/BusinessSupportModal.vue'
import EnrollModal from '@/Components/Shared/EnrollModal.vue'

// Sample business support services data
const businessServices = [
    {
        id: 1,
        imageUrl: '/images/training/businessSupport/biz1.avif',
        title: 'Business Consulting',
        description: 'Expert advice to streamline your operations and boost efficiency.',
        price: 'Consultation from $250'
    },
    {
        id: 2,
        imageUrl: '/images/training/businessSupport/biz2.avif',
        title: 'Strategic Planning',
        description: 'Tailored business strategies to drive growth and long-term success.',
        price: 'Starting at $500'
    },
    {
        id: 3,
        imageUrl: '/images/training/businessSupport/biz3.avif',
        title: 'Marketing Support',
        description: 'Comprehensive marketing solutions designed to elevate your brand.',
        price: 'Custom pricing'
    },
    // Add more business support services as needed...
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
