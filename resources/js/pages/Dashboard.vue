<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps({
    quizAttempts: Array,
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
}

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">You're logged in!</div>
                </div>

                <!-- Quiz History -->
                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4">Recent Quiz Attempts</h3>
                        <div v-if="quizAttempts && quizAttempts.length > 0">
                            <ul class="space-y-3">
                                <li v-for="attempt in quizAttempts" :key="attempt.id" class="flex justify-between items-center border-b pb-2">
                                    <div>
                                        <span class="font-medium">{{ attempt.quiz?.title ?? 'Quiz Deleted' }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                        on {{ formatDate(attempt.completed_at) }}
                                    </span>
                                    </div>
                                    <span class="text-lg font-semibold"
                                          :class="{
                                        'text-green-600 dark:text-green-400': attempt.score >= 80,
                                        'text-yellow-600 dark:text-yellow-400': attempt.score >= 50 && attempt.score < 80,
                                        'text-red-600 dark:text-red-400': attempt.score < 50
                                      }">
                                    {{ attempt.score ?? 'N/A' }}%
                                </span>
                                </li>
                            </ul>
                        </div>
                        <div v-else>
                            <p>You haven't attempted any quizzes yet.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
