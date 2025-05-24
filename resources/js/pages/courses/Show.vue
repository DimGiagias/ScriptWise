<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    course: Object,
});
</script>

<template>
    <AppLayout>
        <Head :title="course.title" />

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-4">
                            <Link :href="route('courses.index')" class="text-blue-600 hover:underline dark:text-blue-400"> « Back to Courses </Link>
                        </div>

                        <h2 class="mb-2 text-3xl font-semibold">{{ course.title }}</h2>
                        <p class="mb-6 text-gray-600 dark:text-gray-400">{{ course.description }}</p>

                        <!-- Assessment Link -->
                        <div
                            v-if="course.assessment_quiz_id"
                            class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50 p-4 text-center dark:border-indigo-800 dark:bg-indigo-900/30"
                        >
                            <p class="mb-2 font-semibold text-indigo-800 dark:text-indigo-300">Know this already?</p>
                            <Link
                                :href="route('course.assessment.show', { course: course.slug })"
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase ring-indigo-300 transition duration-150 ease-in-out hover:bg-indigo-700 focus:border-indigo-900 focus:ring focus:outline-none active:bg-indigo-800"
                            >
                                Take Placement Assessment
                            </Link>
                        </div>

                        <div v-if="course.modules.length > 0" class="space-y-8">
                            <div v-for="module in course.modules" :key="module.id" class="border-t pt-6">
                                <h3 class="mb-3 text-xl font-semibold">{{ module.title }}</h3>

                                <!-- Lesson List -->
                                <div class="mb-4">
                                    <h4 class="mb-2 text-sm text-gray-500 uppercase dark:text-gray-400">Lessons</h4>
                                    <ul v-if="module.lessons.length > 0" class="ml-4 list-inside list-disc space-y-2">
                                        <li v-for="lesson in module.lessons" :key="lesson.id">
                                            <Link
                                                :href="route('lessons.show', { course: course.slug, lesson: lesson.slug })"
                                                class="text-blue-600 hover:underline dark:text-blue-400"
                                                :class="{ 'font-semibold text-gray-800 dark:text-gray-200': lesson.is_completed }"
                                            >
                                                <span v-if="lesson.is_completed" class="mr-1 text-green-500">✓</span>
                                                {{ lesson.title }}
                                            </Link>
                                        </li>
                                    </ul>
                                    <p v-else class="ml-4 text-sm text-gray-500 italic">No lessons in this module yet.</p>
                                </div>

                                <!-- Quiz List -->
                                <div v-if="module.quizzes && module.quizzes.length > 0">
                                    <h4 class="mt-4 mb-2 text-sm text-gray-500 uppercase dark:text-gray-400">Quizzes</h4>
                                    <ul class="ml-4 list-inside list-disc space-y-2">
                                        <li v-for="quiz in module.quizzes" :key="quiz.id">
                                            <Link
                                                :href="route('quizzes.show', { quiz: quiz.id })"
                                                class="text-purple-600 hover:underline dark:text-purple-400"
                                            >
                                                {{ quiz.title }}
                                                <span v-if="quiz.description" class="text-xs text-gray-500 dark:text-gray-400">
                                                    - {{ quiz.description }}</span
                                                >
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div v-if="course.final_review_quiz_id" class="mt-10 border-t pt-6 text-center dark:border-gray-700">
                            <h3 class="mb-3 text-xl font-semibold">Ready for a Final Challenge?</h3>
                            <Link
                                :href="route('course.review.generate', { course: course.slug })"
                                class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-6 py-3 text-sm font-semibold tracking-widest text-white uppercase ring-green-300 transition duration-150 ease-in-out hover:bg-green-700 focus:border-green-900 focus:ring focus:outline-none active:bg-green-800"
                            >
                                Take Final Review Quiz
                            </Link>
                        </div>
                        <div v-else>
                            <p>This course doesn't have any modules yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
