<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import { ref } from "vue";

const props = defineProps({
    events: Array,
    testimonials: Array,
    blogs: {
        type: Array,
        default: () => [],
    },
});

const form = ref({
    firstName: "",
    email: "",
    processing: false,
});

const submitNewsletter = () => {
    // Newsletter submission logic
    form.value.processing = true;
    // Add your newsletter submission logic here
    setTimeout(() => {
        form.value.processing = false;
        form.value.firstName = "";
        form.value.email = "";
    }, 1000);
};
</script>

<template>
    <div class="page">
        <Head title="Home - Zurit Consulting" />
        <Navbar />

        <!-- HERO SECTION -->
        <section id="home" class="hero-section">
            <div class="section-container hero-grid">
                <div class="hero-left">
                    <h1 class="hero-title">
                        <span class="hero-line">Champions of</span>
                        <span class="hero-line hero-sustainable"
                            >Sustainable</span
                        >
                        <span class="hero-line hero-wealth">Wealth</span>
                    </h1>
                </div>
                <div class="hero-right">
                    <p class="hero-copy">
                        Zurit helps individuals and businesses create
                        sustainable wealth. We train and advise you on how to
                        overcome your personal and business investment hurdles.
                    </p>
                    <Link href="/advisory" class="btn btn-primary">
                        Partner with us
                    </Link>
                </div>
            </div>
        </section>

        <!-- WHAT WE DO SECTION -->
        <section id="solutions" class="what-we-do-section">
            <div class="section-container">
                <header class="section-header">
                    <h2>What we do</h2>
                    <p>
                        We provide a wide array of financial trainings and
                        products to help individuals and businesses apply
                        investment knowledge and solutions to the challenges
                        they face.
                    </p>
                </header>

                <div class="services-grid">
                    <article class="service-card">
                        <h3>Training</h3>
                        <p>
                            Our training programs cover a broad range of
                            subjects to assist in building the capacity and
                            skills to grow your wealth.
                        </p>
                        <Link href="/training" class="service-link">
                            See our training programs
                        </Link>
                    </article>

                    <article class="service-card">
                        <h3>Advisory</h3>
                        <p>
                            We help accelerate your consideration and decision
                            making for your personal and business wealth
                            journey.
                        </p>
                        <Link href="/advisory" class="service-link">
                            Book a call
                        </Link>
                    </article>

                    <article class="service-card">
                        <h3>Consulting</h3>
                        <p>
                            We support you in designing, managing and executing
                            investment strategies to help attain your business
                            financial goals.
                        </p>
                        <Link href="/business-support" class="service-link">
                            Book a call
                        </Link>
                    </article>
                </div>
            </div>
        </section>

        <!-- WHY WORK WITH US SECTION -->
        <section id="about" class="why-us-section">
            <div class="section-container">
                <header class="section-header why-header">
                    <h2>Why work with us</h2>
                    <p>
                        Working with Zurit goes far beyond the client/vendor
                        relationship. As an equal partner to our business, you
                        have access to a wide array of knowledge and solutions
                        to help solidify your financial wealth creation journey.
                    </p>
                </header>

                <div class="why-card">
                    <div class="why-item">
                        <div class="why-icon">
                            <svg viewBox="0 0 64 64" aria-hidden="true">
                                <rect
                                    x="8"
                                    y="10"
                                    width="10"
                                    height="44"
                                    rx="2"
                                    fill="#f43f5e"
                                />
                                <rect
                                    x="24"
                                    y="10"
                                    width="10"
                                    height="44"
                                    rx="2"
                                    fill="#f43f5e"
                                />
                                <rect
                                    x="40"
                                    y="10"
                                    width="10"
                                    height="44"
                                    rx="2"
                                    transform="rotate(10 45 32)"
                                    fill="#fb7185"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3>Content Creation</h3>
                            <p>
                                As part of our community, you will enjoy the
                                access of our consistent, reliable and valuable
                                content.
                            </p>
                        </div>
                    </div>

                    <div class="why-item">
                        <div class="why-icon">
                            <svg viewBox="0 0 64 64" aria-hidden="true">
                                <rect
                                    x="14"
                                    y="8"
                                    width="36"
                                    height="48"
                                    rx="4"
                                    fill="#8b5cf6"
                                />
                                <rect
                                    x="22"
                                    y="18"
                                    width="20"
                                    height="4"
                                    rx="2"
                                    fill="#f9fafb"
                                />
                                <rect
                                    x="22"
                                    y="28"
                                    width="16"
                                    height="4"
                                    rx="2"
                                    fill="#f9fafb"
                                />
                                <rect
                                    x="22"
                                    y="38"
                                    width="12"
                                    height="4"
                                    rx="2"
                                    fill="#f9fafb"
                                />
                                <rect
                                    x="32"
                                    y="12"
                                    width="18"
                                    height="48"
                                    rx="4"
                                    fill="#f43f5e"
                                    opacity="0.85"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3>Knowledge Repository</h3>
                            <p>
                                Access our knowledge that includes insights on
                                the markets, investments, trends and foresight.
                            </p>
                        </div>
                    </div>

                    <div class="why-item">
                        <div class="why-icon">
                            <svg viewBox="0 0 64 64" aria-hidden="true">
                                <circle cx="24" cy="32" r="16" fill="#7c3aed" />
                                <circle cx="40" cy="32" r="16" fill="#f43f5e" />
                                <circle cx="32" cy="32" r="8" fill="#ffffff" />
                            </svg>
                        </div>
                        <div>
                            <h3>Networking</h3>
                            <p>
                                By virtue of being our client and partner, we
                                give you access to our networks and connections.
                            </p>
                        </div>
                    </div>

                    <div class="why-item">
                        <div class="why-icon">
                            <svg viewBox="0 0 64 64" aria-hidden="true">
                                <path
                                    d="M32 6l7.6 15.4 17 2.4-12.3 12 2.9 17-15.2-8-15.2 8 2.9-17-12.3-12 17-2.4z"
                                    fill="#8b5cf6"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3>Expertise</h3>
                            <p>
                                You get to enjoy the benefits of our years of
                                expertise practising in the investment industry.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- BOOKS SECTION -->
        <section id="books" class="books-section">
            <div class="section-container">
                <div class="books-header">
                    <div class="bolt-icon" aria-hidden="true">
                        <svg viewBox="0 0 32 32">
                            <path
                                d="M12 2l-4 14h6l-2 14 12-18h-6l4-10z"
                                fill="url(#boltGradient)"
                            />
                            <defs>
                                <linearGradient
                                    id="boltGradient"
                                    x1="0%"
                                    y1="0%"
                                    x2="100%"
                                    y2="100%"
                                >
                                    <stop offset="0%" stop-color="#f97316" />
                                    <stop offset="50%" stop-color="#f43f5e" />
                                    <stop offset="100%" stop-color="#7c3aed" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <h2 class="gradient-heading">
                        Get books from our Team Lead
                    </h2>
                </div>

                <div class="book-feature">
                    <div class="book-cover">
                        <img
                            src="/images/books/thrive.webp"
                            alt="Thrive book cover"
                        />
                    </div>
                    <div class="book-details">
                        <h3>
                            Strategies to turn uncertainty to competitive
                            advantage
                        </h3>
                        <p>
                            The book provides a step-by-step guide on each of
                            the key important matters on Wealth creation and if
                            implemented well together with the help of the
                            various professionals, one can live their lives more
                            freely.
                        </p>
                        <Link href="/books" class="btn btn-primary">
                            Discover Thrive
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- BLOG SECTION -->
        <section id="learn" class="blog-section">
            <div class="section-container">
                <div class="blog-header-row">
                    <h2>Learn from our blog</h2>
                    <Link href="/blogs" class="btn btn-dark">
                        Open the blog
                    </Link>
                </div>

                <div class="blog-grid">
                    <article class="blog-card blog-card-large">
                        <div class="blog-image">
                            <img
                                src="/images/home/8ce4bcfe96282ff672e015c3c4b1c22a8eec39d9.jpg"
                                alt="Investing offshore"
                            />
                        </div>
                        <div class="blog-body">
                            <h3>Things to know before investing offshore</h3>
                            <Link href="/blogs" class="blog-cta">
                                Read the article <span>→</span>
                            </Link>
                        </div>
                    </article>

                    <div class="blog-column-right">
                        <article class="blog-card blog-card-horizontal">
                            <div class="blog-image">
                                <img
                                    src="/images/home/d7a69b219ccb1532182813cadcde4b7864a3f5b7.jpg"
                                    alt="Real estate investing"
                                />
                            </div>
                            <div class="blog-body">
                                <h3>
                                    Tips for beginners investing in real estate
                                </h3>
                                <p class="blog-date">7 April 2025</p>
                                <Link href="/blogs" class="blog-cta">
                                    Read the article <span>→</span>
                                </Link>
                            </div>
                        </article>

                        <article class="blog-card blog-card-horizontal">
                            <div class="blog-image">
                                <img
                                    src="/images/home/a53cc00b558b46f07859fca789cf445caa94ba02.jpg"
                                    alt="Unit Trusts"
                                />
                            </div>
                            <div class="blog-body">
                                <h3>
                                    Using Unit Trusts to achieve your financial
                                    goals
                                </h3>
                                <p class="blog-date">4 April 2025</p>
                                <Link href="/blogs" class="blog-cta">
                                    Read the article <span>→</span>
                                </Link>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTACT/NEWSLETTER SECTION -->
        <section class="contact-section">
            <div class="section-container">
                <header class="section-header contact-header">
                    <h2>Looking for more info?</h2>
                    <p>
                        Follow our social feeds to see what we're working on,
                        gain real-time insights, and learn about how to make
                        consistent strides in your financial freedom journey.
                    </p>
                </header>

                <div class="social-row">
                    <a
                        href="https://facebook.com/zuritconsulting"
                        target="_blank"
                        class="social-icon facebook"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="white"
                        >
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                            />
                        </svg>
                    </a>
                    <a
                        href="https://linkedin.com/company/zurit-consulting"
                        target="_blank"
                        class="social-icon linkedin"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="white"
                        >
                            <path
                                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"
                            />
                        </svg>
                    </a>
                    <a
                        href="https://youtube.com/@zuritconsulting"
                        target="_blank"
                        class="social-icon youtube"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="white"
                        >
                            <path
                                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                            />
                        </svg>
                    </a>
                </div>

                <div class="newsletter">
                    <p class="newsletter-title">
                        Get tips and tactics to grow your wealth the right way.
                    </p>
                    <form
                        @submit.prevent="submitNewsletter"
                        class="newsletter-form"
                    >
                        <input
                            v-model="form.firstName"
                            type="text"
                            placeholder="Enter first name"
                            required
                        />
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="Enter email address"
                            required
                        />
                        <button
                            type="submit"
                            class="btn btn-gradient"
                            :disabled="form.processing"
                        >
                            {{
                                form.processing
                                    ? "Joining..."
                                    : "Join mailing list"
                            }}
                        </button>
                    </form>
                </div>

                <div class="contact-details">
                    <p class="contact-title">
                        Pay us a visit or get in touch via call or mail
                    </p>
                    <div class="contact-grid">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="white"
                                >
                                    <path
                                        d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
                                    />
                                </svg>
                            </div>
                            <p>
                                Zuidier Complex, Mbagathi Hospital Road, Off
                                Mbagathi Way
                            </p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="white"
                                >
                                    <path
                                        d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
                                    />
                                </svg>
                            </div>
                            <p>
                                info@zuritconsulting.com<br />
                                +254 793 726 300
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <Footer />
    </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Zilla+Slab:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap");

/* ROOT VARIABLES */
:root {
    --purple: #6b27ff;
    --purple-deep: #4e1dbd;
    --coral: #ff5c5c;
    --dark: #111111;
    --card-purple-1: #7e3df5;
    --card-purple-2: #c438ff;
    --blog-maroon: #642222;
}

/* GLOBAL STYLES */
.page {
    min-height: 100vh;
    background: #ffffff;
    font-family: "Zilla Slab", serif;
}

.section-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

@media (max-width: 768px) {
    .section-container {
        padding: 0 1.25rem;
    }
}

.btn {
    border: none;
    cursor: pointer;
    border-radius: 999px;
    padding: 0.8rem 1.8rem;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background: linear-gradient(to bottom, #7c3aed, #6d28d9);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-dark {
    background: #000000;
    color: #ffffff;
}

.btn-gradient {
    background: linear-gradient(to bottom, #f43f5e, #dc2626);
    color: #fafaf9;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* HERO SECTION */
.hero-section {
    padding-top: 10rem;
    padding-bottom: 4rem;
    background: #ffffff;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: "";
    position: absolute;
    width: 501px;
    height: 501px;
    right: -80px;
    top: 150px;
    background: #f43f5e;
    opacity: 0.7;
    border-radius: 50%;
    filter: blur(125px);
    z-index: 0;
}

.hero-grid {
    position: relative;
    z-index: 1;
}

.hero-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
    gap: 3rem;
    align-items: center;
    position: relative;
    z-index: 1;
}

.hero-title {
    margin: 0;
    font-size: clamp(2.5rem, 5vw, 4.5rem);
    font-weight: 700;
    line-height: 1.23;
    font-family: "Zilla Slab", serif;
}

.hero-line {
    display: block;
    margin-top: 0.5rem;
}

.hero-sustainable {
    color: #7c3aed;
    text-decoration: underline;
    text-decoration-thickness: 3px;
    text-underline-offset: 6px;
}

.hero-wealth {
    color: #f43f5e;
    text-decoration: underline;
    text-decoration-thickness: 3px;
    text-underline-offset: 6px;
}

.hero-right {
    max-width: 480px;
}

.hero-copy {
    font-size: 1.25rem;
    line-height: 2rem;
    font-weight: 500;
    color: #737373;
    margin-bottom: 2rem;
}

@media (max-width: 960px) {
    .hero-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .hero-right {
        max-width: 100%;
    }
}

/* SECTION HEADERS */
.section-header {
    max-width: 750px;
    margin-bottom: 3rem;
}

.why-header {
    text-align: center;
    margin-left: auto;
    margin-right: auto;
}

.section-header h2 {
    font-size: 3rem;
    margin-bottom: 1rem;
    font-weight: 500;
    color: #27272a;
}

.section-header p {
    margin: 0;
    color: #737373;
    font-size: 1.125rem;
    font-weight: 500;
    line-height: 1.25rem;
}

/* WHAT WE DO SECTION */
.what-we-do-section {
    padding: 4rem 0;
    background: #ffffff;
    position: relative;
    overflow: hidden;
}

.what-we-do-section::before {
    content: "";
    position: absolute;
    width: 320px;
    height: 320px;
    left: -100px;
    top: -80px;
    background: #fbbf24;
    opacity: 0.5;
    border-radius: 50%;
    filter: blur(125px);
    z-index: 0;
}

.what-we-do-section .section-container {
    position: relative;
    z-index: 1;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 2rem;
    position: relative;
}

.services-grid::before {
    content: "";
    position: absolute;
    width: 320px;
    height: 320px;
    left: -80px;
    top: 50%;
    transform: translateY(-50%);
    background: #f43f5e;
    opacity: 0.6;
    border-radius: 50%;
    filter: blur(125px);
    z-index: -1;
}

.services-grid::after {
    content: "";
    position: absolute;
    width: 320px;
    height: 320px;
    right: -60px;
    top: 30%;
    background: #8b5cf6;
    opacity: 0.6;
    border-radius: 50%;
    filter: blur(125px);
    z-index: -1;
}

.service-card {
    padding: 2.5rem 2rem;
    border-radius: 20px;
    color: #ffffff;
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    box-shadow: 0 20px 45px rgba(56, 0, 120, 0.35);
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(56, 0, 120, 0.45);
}

.service-card h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    font-size: 1.875rem;
    font-weight: 500;
}

.service-card p {
    margin-top: 0;
    margin-bottom: 2rem;
    font-size: 1.125rem;
    font-weight: 400;
    line-height: 1.5rem;
}

.service-link {
    font-weight: 500;
    font-size: 1.125rem;
    text-decoration: underline;
    color: #ffffff;
    transition: opacity 0.3s ease;
}

.service-link:hover {
    opacity: 0.8;
}

@media (max-width: 960px) {
    .services-grid {
        grid-template-columns: minmax(0, 1fr);
    }
}

/* WHY US SECTION */
.why-us-section {
    padding: 4rem 0;
    background: #ffffff;
}

.why-card {
    margin-top: 2.5rem;
    border-radius: 32px;
    padding: 3rem 3rem;
    background: #000000;
    color: #ffffff;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 3rem;
}

.why-item {
    display: flex;
    gap: 1.5rem;
}

.why-item h3 {
    margin: 0 0 0.8rem 0;
    font-size: 1.5rem;
    font-weight: 500;
}

.why-item p {
    margin: 0;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5rem;
    color: #ffffff;
}

.why-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(250, 250, 250, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.why-icon svg {
    width: 48px;
    height: 48px;
}

@media (max-width: 960px) {
    .why-card {
        grid-template-columns: minmax(0, 1fr);
        gap: 2rem;
        padding: 2rem;
    }
}

/* BOOKS SECTION */
.books-section {
    padding: 4rem 0;
    background: linear-gradient(180deg, #ffffff 0%, #fff5f7 60%, #ffd5df 100%);
}

.books-header {
    text-align: center;
    margin-bottom: 2.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

.bolt-icon {
    width: 36px;
    height: 36px;
}

.bolt-icon svg {
    width: 100%;
    height: 100%;
}

.gradient-heading {
    font-size: 3rem;
    font-weight: 600;
    background: linear-gradient(90deg, #f59e0b, #f43f5e, #7c3aed);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin: 0;
}

.book-feature {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 2fr);
    gap: 1.8rem;
    align-items: center;
    border-radius: 32px;
    padding: 20px;
    background: #e7d9c5;
    box-shadow:
        inset 0 0 0 1px rgba(0, 0, 0, 0.04),
        0 25px 60px rgba(80, 60, 20, 0.15);
}

.book-cover img {
    width: 100%;
    max-width: 427px;
    height: 320px;
    border-radius: 18px;
    object-fit: cover;
    object-position: top center;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
}

.book-details h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    font-size: 2rem;
    font-weight: 500;
    color: #111111;
    line-height: 1;
}

.book-details p {
    margin-top: 0;
    margin-bottom: 2rem;
    font-size: 1.125rem;
    font-weight: 500;
    line-height: 1;
    color: #6b6b6b;
}

@media (max-width: 960px) {
    .book-feature {
        grid-template-columns: minmax(0, 1fr);
        padding: 2rem;
    }

    .book-cover img {
        max-width: 100%;
    }
}

/* BLOG SECTION */
.blog-section {
    padding: 4rem 0;
    background:
        radial-gradient(
            circle at 0% 20%,
            rgba(244, 63, 94, 0.35),
            transparent 55%
        ),
        radial-gradient(
            circle at 100% 85%,
            rgba(251, 191, 36, 0.4),
            transparent 45%
        ),
        #ffffff;
}

.blog-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.blog-header-row h2 {
    font-size: 3rem;
    margin: 0;
    font-weight: 500;
    color: #000000;
}

.blog-grid {
    display: grid;
    grid-template-columns: minmax(0, 0.75fr) minmax(0, 1.25fr);
    gap: 2.6rem;
}

.blog-card {
    background: #4b1f20;
    color: #ffffff;
    border-radius: 22px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 18px 45px rgba(75, 15, 21, 0.35);
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 22px 55px rgba(75, 15, 21, 0.45);
}

.blog-card-large {
    min-height: 180px;
}

.blog-card-horizontal {
    flex-direction: row;
    min-height: 75px;
}

.blog-image {
    position: relative;
    overflow: hidden;
    border-radius: 16px;
    margin: 0.9rem;
}

.blog-image::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        145deg,
        rgba(244, 63, 94, 0.45),
        rgba(91, 28, 24, 0.6)
    );
    mix-blend-mode: multiply;
}

.blog-image img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    filter: grayscale(100%) saturate(60%) brightness(0.9);
    mix-blend-mode: luminosity;
}

.blog-card-horizontal .blog-image {
    flex: 0 0 42%;
    margin: 0.9rem;
    border-radius: 16px;
}

.blog-card-horizontal .blog-image img {
    height: 100%;
}

.blog-card-large .blog-image {
    margin: 0.9rem 0.9rem 0 0.9rem;
    border-radius: 16px;
}

.blog-card-large .blog-image img {
    height: 140px;
}

.blog-body {
    padding: 0.9rem 1.3rem 1.3rem;
    flex: 1;
}

.blog-body h3 {
    margin: 0 0 0.65rem 0;
    font-size: 1.55rem;
    font-weight: 600;
}

.blog-date {
    margin: 0 0 0.5rem;
    font-size: 0.95rem;
    opacity: 0.75;
}

.blog-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    color: #f8c23d;
    text-decoration: none;
    font-weight: 600;
}

.blog-cta span {
    display: inline-block;
    transition: transform 0.2s ease;
}

.blog-cta:hover span {
    transform: translateX(4px);
}

.blog-column-right {
    display: grid;
    grid-template-rows: repeat(2, minmax(0, 1fr));
    gap: 2rem;
}

@media (max-width: 960px) {
    .blog-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .blog-column-right {
        grid-template-rows: auto;
    }

    .blog-header-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .blog-card-horizontal {
        flex-direction: column;
    }

    .blog-card-horizontal .blog-image {
        flex: initial;
        border-radius: 18px 18px 0 0;
    }
}

/* CONTACT SECTION */
.contact-section {
    margin-top: 0;
    padding-top: 4rem;
    padding-bottom: 2rem;
    background: linear-gradient(
        to bottom,
        rgba(255, 255, 255, 0) 0%,
        rgba(113, 113, 122, 1) 50%,
        rgb(15, 23, 42) 100%
    );
    color: #ffffff;
    position: relative;
}

.contact-header {
    max-width: 100%;
}

.contact-header h2 {
    font-size: 3rem;
    font-weight: 500;
}

.contact-header p {
    color: #737373;
    font-size: 1.125rem;
    font-weight: 500;
}

.social-row {
    display: flex;
    gap: 1rem;
    margin: 2rem 0 3rem;
}

.social-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.social-icon:hover {
    transform: translateY(-3px);
}

.facebook {
    background: #1877f2;
}

.linkedin {
    background: #0077b5;
}

.youtube {
    background: #ff0000;
}

.newsletter {
    margin-top: 2rem;
}

.newsletter-title {
    margin-bottom: 1.2rem;
    font-weight: 500;
    font-size: 1.5rem;
    color: #000000;
}

.newsletter-form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr) auto;
    gap: 1rem;
    margin-bottom: 3rem;
}

.newsletter-form input {
    border-radius: 6px;
    border: 1px solid #a1a1aa;
    background: rgba(255, 255, 255, 0.9);
    padding: 1rem 1rem;
    font-size: 1.125rem;
    color: #737373;
}

.contact-title {
    margin: 0 0 1.5rem 0;
    font-weight: 500;
    font-size: 1.5rem;
    color: #ffffff;
}

.contact-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.contact-card {
    flex: 1 1 280px;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    padding: 1.5rem 1.5rem;
    border-radius: 14px;
    background: rgba(0, 0, 0, 0.3);
}

.contact-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #ff5b5b, #ff3535);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

@media (max-width: 960px) {
    .newsletter-form {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
