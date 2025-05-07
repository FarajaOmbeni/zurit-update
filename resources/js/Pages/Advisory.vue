<template>

    <Head title="Advisory" />
    <Navbar />
    <div class="mt-36 mb-24">
        <div class="text-2xl md:ml-8 md:text-4xl lg:text-7xl md:text-left text-center font-bold lg:mb-6">
            Get Advisory <span class="text-yellow-600">Help Today</span>
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
        type: 'advisory',
        imageUrl: '/images/training/advisory/advisory1.avif',
        title: 'Transactional & Fundraising',
        description: 'At Zurit Consulting, we provide transactional and fundraising advisory services designed to help businesses structure deals, raise capital, and execute growth transactions with confidence and precision. This is not a session — it’s a collaborative process where we work closely with your leadership team to deliver results.',
        details: [
            'Structuring and negotiating commercial and investment transactions',
            'Fundraising strategy design (equity, debt, blended finance)',
            'Investor engagement and pitch support',
            'Financial and business due diligence coordination',
            'Transaction documentation review and insights',
            'Risk assessment and mitigation strategy',
            'Real-time case analysis based on your transaction needs'
        ],
        attends: [
            'SME founders, CEOs, and CFOs preparing for capital raising',
            'Businesses seeking to expand through strategic partnerships, acquisitions, or deal structuring',
            'Enterprises navigating institutional investor requirements or growth transactions',
        ],
        price: '15,000 for a discovery session'
    },
    {
        id: 2,
        type: 'advisory',
        imageUrl: '/images/training/advisory/advisory2.avif',
        title: 'Employee Benefits Advisory',
        description: 'Zurit Consulting offers strategic advisory to help businesses design competitive, compliant, and cost-effective employee benefits packages that drive employee satisfaction and organizational loyalty.We don`t just review policies—we work with you to build benefits that align with your budget, company culture, and talent goals.',
        details: [
            'In-depth review and analysis of current benefit structures',
            'Advisory on designing innovative, high-impact benefit packages',
            'Guidance on compliance with labor laws and regulatory standards',
            'Cost-benefit analysis to ensure sustainability and value'
        ],
        attends: [
            'HR Managers and People & Culture leads',
            'SME and corporate business owners',
            'Payroll, compensation & benefits administrators',
            'Organizations restructuring or scaling employee programs'
        ],
        price: '15,000'
    },
    // {
    //     id: 3,
    //     type: 'sme',
    //     imageUrl: '/images/training/advisory/advisory3.avif',
    //     title: 'Transactional Advisory and Fundraising',
    //     description: 'Comprehensive advisory covering the essentials of corporate transactions and effective fundraising techniques for business growth.',
    //     details: [
    //         'Structuring and negotiating transactions',
    //         'Fundraising strategies and investor engagement',
    //         'Due diligence and risk assessment',
    //         'Practical case studies'
    //     ],
    //     attend: 'SME owners and managers looking to secure capital and execute strategic transactions.',
    //     price: '15,000'
    // },
    // {
    //     id: 4,
    //     type: 'sme',
    //     imageUrl: '/images/training/advisory/advisory1.avif',
    //     title: 'Employee Benefits',
    //     description: 'Advisory on designing competitive employee benefits packages that attract and retain top talent while balancing costs.',
    //     details: [
    //         'Analysis of current benefits schemes',
    //         'Design of innovative benefits packages',
    //         'Compliance with labor laws',
    //         'Cost–benefit analysis'
    //     ],
    //     attend: 'SME HR managers, business owners, and benefits administrators.',
    //     price: '15,000'
    // },
    // {
    //     id: 5,
    //     type: 'sme',
    //     imageUrl: '/images/training/advisory/advisory2.avif',
    //     title: 'Business Process Review',
    //     description: 'An in-depth review aimed at streamlining business operations and improving overall efficiency through process optimization.',
    //     details: [
    //         'Mapping and analysis of business processes',
    //         'Identification of inefficiencies and bottlenecks',
    //         'Recommendations for process improvement',
    //         'Implementation strategies and performance metrics'
    //     ],
    //     attend: 'SME leaders, operations managers, and process improvement teams.',
    //     price: '15,000'
    // },
    // {
    //     id: 6,
    //     type: 'sme',
    //     imageUrl: '/images/training/advisory/advisory3.avif',
    //     title: 'Business and Strategic Plans',
    //     description: 'Guidance on creating comprehensive business and strategic plans that drive sustainable growth and competitive advantage.',
    //     details: [
    //         'Market analysis and strategic visioning',
    //         'Goal setting and roadmap development',
    //         'Financial forecasting and risk management',
    //         'Actionable implementation steps'
    //     ],
    //     attend: 'SME owners and senior managers seeking structured growth strategies.',
    //     price: '15,000'
    // },
    // {
    //     id: 7,
    //     type: 'corporate',
    //     imageUrl: '/images/training/advisory/advisory3.avif',
    //     title: 'Transactional Advisory',
    //     description: 'Expert corporate advisory services to support transactions from initiation through to successful closure.',
    //     details: [
    //         'Transaction structuring and valuation',
    //         'Risk and compliance assessments',
    //         'Negotiation and deal-making strategies',
    //         'Post-transaction integration'
    //     ],
    //     attend: 'Corporate executives and finance teams involved in mergers, acquisitions, and other transactions.',
    //     price: '15,000'
    // },
    // {
    //     id: 8,
    //     type: 'corporate',
    //     imageUrl: '/images/training/advisory/advisory1.avif',
    //     title: 'Employee Benefits',
    //     description: 'Strategic advisory on developing employee benefits programs that align with corporate goals and drive engagement.',
    //     details: [
    //         'Evaluation of current benefits structures',
    //         'Designing competitive benefit plans',
    //         'Cost management and compliance',
    //         'Benchmarking against industry standards'
    //     ],
    //     attend: 'Corporate HR professionals and benefits managers.',
    //     price: '15,000'
    // },
    // {
    //     id: 9,
    //     type: 'corporate',
    //     imageUrl: '/images/training/advisory/advisory2.avif',
    //     title: 'Business Process Review',
    //     description: 'A comprehensive review of corporate processes aimed at boosting productivity and streamlining operations.',
    //     details: [
    //         'Operational analysis and performance benchmarking',
    //         'Identification of process inefficiencies',
    //         'Strategic recommendations for improvement',
    //         'Implementation roadmap and KPIs'
    //     ],
    //     attend: 'Corporate operations teams and process improvement consultants.',
    //     price: '15,000'
    // },
    // {
    //     id: 10,
    //     type: 'corporate',
    //     imageUrl: '/images/training/advisory/advisory3.avif',
    //     title: 'Business and Strategic Plans',
    //     description: 'Develop robust business and strategic plans to enhance market position and drive corporate growth.',
    //     details: [
    //         'Comprehensive market and competitive analysis',
    //         'Strategic goal formulation',
    //         'Financial and operational planning',
    //         'Risk assessment and contingency planning'
    //     ],
    //     attend: 'Corporate strategists, executive teams, and business development leaders.',
    //     price: '15,000'
    // },
    // {
    //     id: 11,
    //     type: 'corporate',
    //     imageUrl: '/images/training/advisory/advisory1.avif',
    //     title: 'Governance Review',
    //     description: 'A detailed review of corporate governance structures aimed at enhancing accountability and transparency.',
    //     details: [
    //         'Assessment of board composition and practices',
    //         'Evaluation of internal controls',
    //         'Compliance with regulatory standards',
    //         'Recommendations for governance improvements'
    //     ],
    //     attend: 'Corporate boards, governance committees, and compliance officers.',
    //     price: '15,000'
    // },
    // {
    //     id: 12,
    //     type: 'corporate',
    //     imageUrl: '/images/training/advisory/advisory2.avif',
    //     title: 'Research and Feasibility Review',
    //     description: 'Evaluate new business ideas and projects through comprehensive research and feasibility studies.',
    //     details: [
    //         'Market and competitor research',
    //         'Feasibility analysis and risk assessment',
    //         'Cost–benefit evaluation',
    //         'Strategic recommendations'
    //     ],
    //     attend: 'Corporate project managers, business analysts, and development teams.',
    //     price: '15,000'
    // },
    // {
    //     id: 13,
    //     type: 'pension_scheme',
    //     imageUrl: '/images/training/advisory/advisory3.avif',
    //     title: 'Investment Advisory',
    //     description: 'Specialized advisory services to optimize investment strategies and portfolio performance within pension schemes.',
    //     details: [
    //         'Comprehensive portfolio review',
    //         'Asset allocation strategies',
    //         'Risk management and mitigation',
    //         'Performance tracking and reporting'
    //     ],
    //     attend: 'Pension fund managers, trustees, and financial advisors.',
    //     price: '15,000'
    // },
    // {
    //     id: 14,
    //     type: 'pension_scheme',
    //     imageUrl: '/images/training/advisory/advisory1.avif',
    //     title: 'Investment Policy Preparation',
    //     description: 'Expert guidance in crafting robust investment policies that align with the objectives of pension schemes.',
    //     details: [
    //         'Development of policy framework',
    //         'Risk tolerance and asset allocation',
    //         'Regulatory compliance',
    //         'Periodic review and adjustment'
    //     ],
    //     attend: 'Pension fund administrators, trustees, and policy makers.',
    //     price: '15,000'
    // },
    // {
    //     id: 15,
    //     type: 'pension_scheme',
    //     imageUrl: '/images/training/advisory/advisory2.avif',
    //     title: 'Strategic Plan',
    //     description: 'Long-term strategic planning services designed to ensure the sustainable growth and resilience of pension schemes.',
    //     details: [
    //         'Setting strategic goals',
    //         'Market trend and risk analysis',
    //         'Financial forecasting',
    //         'Actionable implementation plan'
    //     ],
    //     attend: 'Pension fund stakeholders, trustees, and management teams.',
    //     price: '15,000'
    // },
    // {
    //     id: 16,
    //     type: 'pension_scheme',
    //     imageUrl: '/images/training/advisory/advisory3.avif',
    //     title: 'Performance Review',
    //     description: 'An in-depth performance review to evaluate investment returns and operational efficiency within pension schemes.',
    //     details: [
    //         'Performance metrics evaluation',
    //         'Benchmarking against industry standards',
    //         'Risk and compliance assessment',
    //         'Improvement recommendations'
    //     ],
    //     attend: 'Pension fund managers and investment analysts.',
    //     price: '15,000'
    // },
    // {
    //     id: 17,
    //     type: 'sacco',
    //     imageUrl: '/images/training/advisory/advisory1.avif',
    //     title: 'Training',
    //     description: 'Customized training programs designed to empower sacco members with essential financial management and operational skills.',
    //     details: [
    //         'Fundamentals of financial management',
    //         'Savings and loan best practices',
    //         'Risk management strategies',
    //         'Interactive workshops and case studies'
    //     ],
    //     attend: 'Sacco members, management teams, and financial officers.',
    //     price: '15,000'
    // },
    // {
    //     id: 18,
    //     type: 'sacco',
    //     imageUrl: '/images/training/advisory/advisory2.avif',
    //     title: 'Board Induction',
    //     description: 'Orientation sessions to equip new board members with the necessary insights into sacco governance and operational best practices.',
    //     details: [
    //         'Overview of sacco governance',
    //         'Roles and responsibilities of board members',
    //         'Regulatory and compliance framework',
    //         'Effective decision-making processes'
    //     ],
    //     attend: 'New board members and governance committees within saccos.',
    //     price: '15,000'
    // },
    // {
    //     id: 19,
    //     type: 'sacco',
    //     imageUrl: '/images/training/advisory/advisory2.avif',
    //     title: 'Investment Advisory',
    //     description: 'Tailored advisory services to help sacco members optimize their investment portfolios for improved returns.',
    //     details: [
    //         'Portfolio review and analysis',
    //         'Investment strategy development',
    //         'Risk profiling and management',
    //         'Market trends and performance monitoring'
    //     ],
    //     attend: 'Sacco members, investment committees, and financial advisors.',
    //     price: '15,000'
    // },
    // {
    //     id: 20,
    //     type: 'sacco',
    //     imageUrl: '/images/training/advisory/advisory3.avif',
    //     title: 'Performance Review',
    //     description: 'A thorough review of sacco operations and investment performance to identify opportunities for strategic improvements.',
    //     details: [
    //         'Data-driven performance analysis',
    //         'Benchmarking against key indicators',
    //         'Risk and efficiency assessment',
    //         'Strategic improvement recommendations'
    //     ],
    //     attend: 'Sacco management teams, performance review committees, and members.',
    //     price: '15,000'
    // }
];


// const tabs = [
//     { id: 'individual', label: 'Individual' },
//     { id: 'sme', label: 'SME' },
//     { id: 'corporate', label: 'Corporate' },
//     { id: 'pension_scheme', label: 'Pension Scheme' },
//     { id: 'sacco', label: 'Sacco' },
// ]

const activeTab = ref('advisory')
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
