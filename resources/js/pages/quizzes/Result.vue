<script setup lang="ts">
//import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    quiz: Object,       // { id, title }
    attempt: Object,    // { id, score, completed_at }
    results: Array,     // [{ question_id, question_text, options, user_answer, correct_answer, is_correct, explanation, lesson_id }, ...]
    reviewSuggestions: Array, // [{ id, title, url }, ...]
    deepReviewSuggestions: { // New prop for review quiz
        type: Array,
        default: () => [],
    },
});

//defineOptions({ layout: AppLayout });

console.log(props.quiz)
console.log(props.attempt)
console.log(props.results)
console.log(props.reviewSuggestions)
// Helper to get option text by ID
// const getOptionText = (options, optionId) => {
//     const option = options.find(o => o.id === optionId);
//     return option ? option.text : 'N/A';
// };

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

                    <!-- Score Display (uses attempt.score) -->
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-center">
                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Your Score:</p>
                        <p class="text-5xl font-bold" :class="scoreColorClass">{{ attempt.score }}%</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Completed on: {{ new Date(attempt.completed_at).toLocaleString() }}</p>
                        <!-- Optional: Display attempt type -->
                        <p v-if="attempt.type === 'random'" class="text-xs text-gray-400 dark:text-gray-500 mt-1">(Random Review)</p>
                    </div>

                    <!-- Score Display -->
<!--                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-center">-->
<!--                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Your Score:</p>-->
<!--                        <p class="text-5xl font-bold" :class="scoreColorClass">{{ attempt.score }}%</p>-->
<!--                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Completed on: {{ new Date(attempt.completed_at).toLocaleString() }}</p>-->
<!--                    </div>-->

                    <div v-if="deepReviewSuggestions.length > 0" class="mb-8 p-4 border border-orange-300 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/30 rounded-lg">
                        <h3 class="text-lg font-semibold text-orange-800 dark:text-orange-300 mb-2">Areas for Deeper Review</h3>
                        <p class="text-sm text-orange-700 dark:text-orange-400 mb-3">
                            You might want to revisit these topics and explore the additional resources:
                        </p>
                        <div v-for="suggestion in deepReviewSuggestions" :key="suggestion.id" class="mb-4 pb-3 border-b last:border-b-0 dark:border-gray-700">
                            <h4 class="font-medium">
                                <Link :href="suggestion.url" class="text-blue-600 hover:underline dark:text-blue-400">
                                    Lesson: {{ suggestion.title }}
                                </Link>
                            </h4>
                            <div v-if="suggestion.external_resources.length > 0" class="mt-2 ml-4">
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Additional Resources:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li v-for="resource in suggestion.external_resources" :key="resource.url" class="text-xs">
                                        <a :href="resource.url" target="_blank" rel="noopener noreferrer" class="text-purple-600 hover:underline dark:text-purple-400">
                                            {{ resource.title }} ({{ resource.type }})
                                        </a>
                                        <p v-if="resource.description" class="text-gray-500 dark:text-gray-500 ml-4">{{ resource.description }}</p>
                                    </li>
                                </ul>
                            </div>
                            <p v-else class="text-xs text-gray-500 dark:text-gray-400 ml-4 italic">No additional resources currently linked for this lesson.</p>
                        </div>
                    </div>

                    <!-- Review Suggestions -->
                    <div v-if="reviewSuggestions.length > 0 && deepReviewSuggestions.length === 0" class="mb-8 p-4 border border-yellow-300 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
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

                    <!-- Detailed Results Breakdown
                    <h3 class="text-xl font-semibold mb-4 border-t pt-6">Detailed Results</h3>
                    <div class="space-y-6">
                        <div v-for="(result, index) in results" :key="result.question_id" class="border p-4 rounded-md" :class="{ 'border-green-300 dark:border-green-700 bg-green-50/50 dark:bg-green-900/10': result.is_correct, 'border-red-300 dark:border-red-700 bg-red-50/50 dark:bg-red-900/10': !result.is_correct }">
                            <p class="font-semibold mb-2">{{ index + 1 }}. {{ result.question_text }}</p>
                            <div class="text-sm space-y-1">
                                <p>Your answer: <span class="font-medium" :class="{ 'text-green-700 dark:text-green-300': result.is_correct, 'text-red-700 dark:text-red-300': !result.is_correct }">
                                        {{ getOptionText(result.options, result.user_answer) }}
                                     <span v-if="result.is_correct"> (Correct)</span>
                                     <span v-else> (Incorrect)</span>
                                 </span></p>
                                <p v-if="!result.is_correct">Correct answer: <span class="font-medium text-green-700 dark:text-green-300">{{ getOptionText(result.options, result.correct_answer) }}</span></p>
                                <p v-if="result.explanation" class="mt-2 pt-2 border-t border-dashed text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Explanation:</span> {{ result.explanation }}
                                </p>
                            </div>
                        </div>
                        <p v-if="result.explanation" class="mt-2 pt-2 border-t border-dashed text-gray-600 dark:text-gray-400">
             <span class="font-medium">Explanation:</span> {{ result.explanation }}
         </p>
                    </div>-->

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

<style scoped>

</style>
