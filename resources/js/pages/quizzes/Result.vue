<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    quiz: Object,
    attempt: Object,
    results: Array,
    reviewSuggestions: Array,
});

const scoreColorClass = computed(() => {
    if (props.attempt.score >= 80) return 'text-green-600 dark:text-green-400';
    if (props.attempt.score >= 50) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-red-600 dark:text-red-400';
});
</script>

<template>
    <Head :title="`Quiz Results: ${quiz.title}`" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <h2 class="text-2xl md:text-3xl font-semibold mb-4">Results for: {{ quiz.title }}</h2>

                    <!-- Score Display -->
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-center">
                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Your Score:</p>
                        <p class="text-5xl font-bold" :class="scoreColorClass">{{ attempt.score }}%</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Completed on: {{ new Date(attempt.completed_at).toLocaleString() }}</p>
                    </div>

                    <!-- Review Suggestions -->
                    <div v-if="reviewSuggestions.length > 0" class="mb-8 p-4 border border-yellow-300 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-2">Lessons to Review</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-400 mb-3">Based on your answers, you might want to review the following topics:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li v-for="suggestion in reviewSuggestions" :key="suggestion.id">
                                <Link :href="suggestion.url" class="text-blue-600 hover:underline dark:text-blue-400 font-medium">
                                    {{ suggestion.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Back/Next Links -->
                    <div class="mt-8 border-t pt-6 flex justify-between">
                        <!-- Link back to course/module? Need data for this -->
                        <Link v-if="false" href="#" class="text-blue-600 hover:underline dark:text-blue-400">« Back to Module</Link>
                        <!-- Link to next module/course overview? -->
                        <Link :href="route('dashboard')" class="text-blue-600 hover:underline dark:text-blue-400">Go to Dashboard »</Link>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
