<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    score: Number,
    recommendationMessage: String,
    recommendedLessonSlug: String,
    firstLessonSlug: String,
});

const lessonUrl = (lessonSlug) => {
    if (!lessonSlug) return '#'; // Fallback
    return route('lessons.show', { course: props.course.slug, lesson: lessonSlug });
};

console.log(props.recommendedLessonSlug);
</script>

<template>
    <Head :title="`Assessment Results: ${course.title}`" />

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 md:p-8 dark:text-gray-100">
                    <h2 class="mb-4 text-2xl font-semibold md:text-3xl">Assessment Results: {{ course.title }}</h2>

                    <!-- Score Display -->
                    <div class="mb-6 rounded-lg bg-gray-50 p-4 text-center dark:bg-gray-700/50">
                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Your Score:</p>
                        <!-- Basic score color, adjust if needed -->
                        <p
                            class="text-5xl font-bold"
                            :class="{
                                'text-green-600 dark:text-green-400': score >= 80,
                                'text-yellow-600 dark:text-yellow-400': score >= 50 && score < 80,
                                'text-red-600 dark:text-red-400': score < 50,
                            }"
                        >
                            {{ score }}%
                        </p>
                    </div>

                    <!-- Recommendation -->
                    <div class="mb-8 rounded-lg border border-blue-300 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-900/30">
                        <h3 class="mb-2 text-lg font-semibold text-blue-800 dark:text-blue-300">Recommendation</h3>
                        <p class="text-blue-700 dark:text-blue-400">{{ recommendationMessage }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap justify-center gap-4 border-t pt-6">
                        <Link
                            v-if="recommendedLessonSlug"
                            :href="lessonUrl(recommendedLessonSlug)"
                            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase ring-blue-300 transition duration-150 ease-in-out hover:bg-blue-700 focus:border-blue-900 focus:ring focus:outline-none active:bg-blue-800"
                        >
                            Start at Recommended Lesson
                        </Link>
                        <Link
                            v-if="firstLessonSlug && recommendedLessonSlug !== firstLessonSlug"
                            :href="lessonUrl(firstLessonSlug)"
                            class="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase ring-gray-300 transition duration-150 ease-in-out hover:bg-gray-700 focus:border-gray-900 focus:ring focus:outline-none active:bg-gray-800"
                        >
                            Start from Beginning
                        </Link>
                        <Link
                            :href="route('courses.show', { course: course.slug })"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
                        >
                            Back to Course Overview
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
