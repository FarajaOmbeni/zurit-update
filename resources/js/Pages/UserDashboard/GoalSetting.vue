<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { defineProps } from 'vue';
import GoalCard from '@/Components/Shared/GoalCard.vue';

const props = defineProps({
    goals: Array,
});

// Filter active goals (any goal not marked as achieved)
const activeGoals = computed(() => props.goals.filter(goal => goal.status !== 'achieved'));

// Filter completed goals (status set to achieved)
const completedGoals = computed(() => props.goals.filter(goal => goal.status === 'achieved'));
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="container mx-auto p-4">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-purple-700">Your Goals</h1>
                        <!-- Optional filter or sort controls can be added here -->
                    </div>

                    <!-- Active Goals Section -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold text-purple-700 mb-4">Active Goals</h2>
                        <div v-if="activeGoals.length" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <GoalCard v-for="goal in activeGoals" :key="goal.goal_id" :goal="goal" />
                        </div>
                        <div v-else class="text-gray-600">
                            No active goals found.
                        </div>
                    </section>

                    <!-- Completed Goals Section -->
                    <section>
                        <h2 class="text-xl font-semibold text-purple-700 mb-4">Completed Goals</h2>
                        <div v-if="completedGoals.length" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <GoalCard v-for="goal in completedGoals" :key="goal.goal_id" :goal="goal" />
                        </div>
                        <div v-else class="text-gray-600">
                            No completed goals found.
                        </div>
                    </section>

                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
