<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    course: Object, // { id, title, slug }
    score: Number,
    recommendationMessage: String,
    recommendedLessonSlug: String, // Slug of the recommended lesson
    firstLessonSlug: String, // Slug of the very first lesson
});

// Helper to generate lesson URLs
const lessonUrl = (lessonSlug) => {
    if (!lessonSlug) return '#'; // Fallback
    return route('lessons.show', { course: props.course.slug, lesson: lessonSlug });
}

console.log(props.recommendedLessonSlug)
</script>

<template>
    <Head :title="`Assessment Results: ${course.title}`" />

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <h2 class="text-2xl md:text-3xl font-semibold mb-4">Assessment Results: {{ course.title }}</h2>

                    <!-- Score Display -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-center">
                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Your Score:</p>
                        <!-- Basic score color, adjust if needed -->
                        <p class="text-5xl font-bold" :class="{'text-green-600 dark:text-green-400': score >= 80, 'text-yellow-600 dark:text-yellow-400': score >= 50 && score < 80, 'text-red-600 dark:text-red-400': score < 50 }">{{ score }}%</p>
                    </div>

                    <!-- Recommendation -->
                    <div class="mb-8 p-4 border border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-2">Recommendation</h3>
                        <p class="text-blue-700 dark:text-blue-400">{{ recommendationMessage }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4 justify-center border-t pt-6">
                        <Link v-if="recommendedLessonSlug" :href="lessonUrl(recommendedLessonSlug)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 transition ease-in-out duration-150">
                            Start at Recommended Lesson
                        </Link>
                        <Link v-if="firstLessonSlug && recommendedLessonSlug !== firstLessonSlug" :href="lessonUrl(firstLessonSlug)" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 transition ease-in-out duration-150">
                            Start from Beginning
                        </Link>
                        <Link :href="route('courses.show', { course: course.slug })" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            Back to Course Overview
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
