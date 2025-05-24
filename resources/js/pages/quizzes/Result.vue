<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    quiz: Object,
    attempt: Object,
    results: Array,
    reviewSuggestions: Array,
    deepReviewSuggestions: {
        type: Array,
        default: () => [],
    },
});

console.log(props.quiz);
console.log(props.attempt);
console.log(props.results);
console.log(props.reviewSuggestions);

const scoreColorClass = computed(() => {
    if (props.attempt.score >= 80) return 'text-green-600 dark:text-green-400';
    if (props.attempt.score >= 50) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-red-600 dark:text-red-400';
});
</script>

<template>
    <AppLayout>
        <Head :title="`Quiz Results: ${quiz.title}`" />

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 md:p-8 dark:text-gray-100">
                        <h2 class="mb-4 text-2xl font-semibold md:text-3xl">Results for: {{ quiz.title }}</h2>

                        <!-- Score Display -->
                        <div class="mb-8 rounded-lg bg-gray-50 p-4 text-center dark:bg-gray-700/50">
                            <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Your Score:</p>
                            <p class="text-5xl font-bold" :class="scoreColorClass">{{ attempt.score }}%</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Completed on: {{ new Date(attempt.completed_at).toLocaleString() }}
                            </p>
                            <p v-if="attempt.type === 'random'" class="mt-1 text-xs text-gray-400 dark:text-gray-500">(Random Review)</p>
                        </div>
                        <div
                            v-if="deepReviewSuggestions.length > 0"
                            class="mb-8 rounded-lg border border-orange-300 bg-orange-50 p-4 dark:border-orange-700 dark:bg-orange-900/30"
                        >
                            <h3 class="mb-2 text-lg font-semibold text-orange-800 dark:text-orange-300">Areas for Deeper Review</h3>
                            <p class="mb-3 text-sm text-orange-700 dark:text-orange-400">
                                You might want to revisit these topics and explore the additional resources:
                            </p>
                            <div
                                v-for="suggestion in deepReviewSuggestions"
                                :key="suggestion.id"
                                class="mb-4 border-b pb-3 last:border-b-0 dark:border-gray-700"
                            >
                                <h4 class="font-medium">
                                    <Link :href="suggestion.url" class="text-blue-600 hover:underline dark:text-blue-400">
                                        Lesson: {{ suggestion.title }}
                                    </Link>
                                </h4>
                                <div v-if="suggestion.external_resources.length > 0" class="mt-2 ml-4">
                                    <p class="mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">Additional Resources:</p>
                                    <ul class="list-inside list-disc space-y-1">
                                        <li v-for="resource in suggestion.external_resources" :key="resource.url" class="text-xs">
                                            <a
                                                :href="resource.url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-purple-600 hover:underline dark:text-purple-400"
                                            >
                                                {{ resource.title }} ({{ resource.type }})
                                            </a>
                                            <p v-if="resource.description" class="ml-4 text-gray-500 dark:text-gray-500">
                                                {{ resource.description }}
                                            </p>
                                        </li>
                                    </ul>
                                </div>
                                <p v-else class="ml-4 text-xs text-gray-500 italic dark:text-gray-400">
                                    No additional resources currently linked for this lesson.
                                </p>
                            </div>
                        </div>

                        <!-- Review Suggestions -->
                        <div
                            v-if="reviewSuggestions.length > 0 && deepReviewSuggestions.length === 0"
                            class="mb-8 rounded-lg border border-yellow-300 bg-yellow-50 p-4 dark:border-yellow-700 dark:bg-yellow-900/30"
                        >
                            <h3 class="mb-2 text-lg font-semibold text-yellow-800 dark:text-yellow-300">Lessons to Review</h3>
                            <p class="mb-3 text-sm text-yellow-700 dark:text-yellow-400">
                                Based on your answers, you might want to review the following topics:
                            </p>
                            <ul class="list-inside list-disc space-y-1">
                                <li v-for="suggestion in reviewSuggestions" :key="suggestion.id">
                                    <Link :href="suggestion.url" class="font-medium text-blue-600 hover:underline dark:text-blue-400">
                                        {{ suggestion.title }}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-8 flex justify-between border-t pt-6">
                            <Link v-if="false" href="#" class="text-blue-600 hover:underline dark:text-blue-400">« Back to Module</Link>
                            <Link :href="route('dashboard')" class="text-blue-600 hover:underline dark:text-blue-400">Go to Dashboard »</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
