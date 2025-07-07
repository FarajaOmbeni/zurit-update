<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const partners = ref([
    { id: 1, src: "/images/partners/taaj.webp", alt: "Beyond the Stethoscope" },
    { id: 1, src: "/images/partners/dtb.png", alt: "Diamond Trust Bank" },
    { id: 2, src: "/images/partners/college-of-insurance.png", alt: "College of Insurance" },
    { id: 3, src: "/images/partners/kozi.png", alt: "Kozi" },
    { id: 4, src: "/images/partners/look-up-tv.webp", alt: "Look-Up TV" },
    { id: 5, src: "/images/partners/mol-logistics.webp", alt: "MOL Logistics" },
    { id: 6, src: "/images/partners/mywage-pay.png", alt: "MyWage Pay" },
    { id: 7, src: "/images/partners/nca.png", alt: "NCA" },
    { id: 8, src: "/images/partners/nita.webp", alt: "NITA" },
    { id: 9, src: "/images/partners/parklands.webp", alt: "Parklands" },
    { id: 10, src: "/images/partners/salaam.png", alt: "Salaam" },
    { id: 11, src: "/images/partners/sinapis.webp", alt: "Sinapis" },
    { id: 12, src: "/images/partners/sme.webp", alt: "SME" },
    { id: 13, src: "/images/partners/beyond-the-stethescope.png", alt: "TAAJ" },
    { id: 14, src: "/images/partners/maasai_mara.webp", alt: "Maasai Mara" },
    { id: 15, src: "/images/partners/masinde.webp", alt: "Masinde" },
    { id: 16, src: "/images/partners/kibabii.webp", alt: "Kibabii" },
    { id: 17, src: "/images/partners/kise.webp", alt: "KISE" },
    { id: 18, src: "/images/partners/kpc.webp", alt: "KPC" },
    { id: 19, src: "/images/partners/lake_basin.webp", alt: "Lake Basin" },
]);

const currentIndex = ref(0);
const totalSlides = Math.ceil(partners.value.length / 3); // Show 3 logos per slide
let interval = null;

const startAutoSlide = () => {
    interval = setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % totalSlides;
    }, 3500);
};

const stopAutoSlide = () => {
    clearInterval(interval);
};

onMounted(startAutoSlide);
onUnmounted(stopAutoSlide);
</script>

<template>
    <div class="bg-white py-12">
        <h2 class="text-center text-4xl md:text-5xl font-extrabold text-yellow-500 mb-10">Our Partners</h2>

        <div class="relative max-w-6xl mx-auto overflow-hidden" @mouseover="stopAutoSlide" @mouseleave="startAutoSlide">
            <div class="flex transition-transform duration-700 ease-in-out"
                :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                <div v-for="(group, index) in totalSlides" :key="index" class="min-w-full flex justify-center gap-12">
                    <img v-for="partner in partners.slice(index * 3, index * 3 + 3)" :key="partner.id"
                        :src="partner.src" :alt="partner.alt"
                        class="h-40 md:h-52 object-contain transition-transform duration-300 hover:scale-110"
                        loading="lazy" />
                </div>
            </div>
        </div>

        <!-- Navigation Dots -->
        <div class="flex justify-center mt-6">
            <span v-for="(dot, index) in totalSlides" :key="index"
                class="h-4 w-4 mx-2 rounded-full transition-all duration-300"
                :class="currentIndex === index ? 'bg-yellow-500 scale-150' : 'bg-gray-300'"
                @click="currentIndex = index"></span>
        </div>
    </div>
</template>