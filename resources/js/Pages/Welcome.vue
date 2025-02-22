<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Hero from '@/Components/Hero.vue';
import Events from '@/Components/Events.vue';
import Footer from '@/Components/Footer.vue';
import Partners from '@/Components/Partners.vue';
import WhyChooseUs from '@/Components/WhyChooseUs.vue';
import ContactUs from '@/Components/ContactUs.vue';

</script>

<template>

    <Head title="Home" />
    <Navbar />
    <Hero />
    <Events />
    <!--Circles-->
    <div class="absolute -top-60 -left-60">
        <img class="w-[35rem]" src="../../assets/images/circles.svg" alt="Circles">
    </div>
    <div class="absolute top-0 left-0">
        <img src="../../assets/images/dots.svg" alt="">
    </div>
    <Partners/>
    <WhyChooseUs />
    <ContactUs/>
    <Footer/>
</template>
