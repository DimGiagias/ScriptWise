<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue'; // Adjust if your layout name differs
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    course: Object, // Contains 'id', 'title', 'slug', 'description', 'modules' (array)
                    // Each module contains 'id', 'title', 'order', 'lessons' (array)
                    // Each lesson contains 'id', 'module_id', 'title', 'slug', 'order'
});

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head :title="course.title" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Breadcrumb/Back Link -->
                    <div class="mb-4">
                        <Link :href="route('courses.index')" class="text-blue-600 hover:underline dark:text-blue-400">
                            « Back to Courses
                        </Link>
                    </div>

                    <h2 class="text-3xl font-semibold mb-2">{{ course.title }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ course.description }}</p>

                    <!-- Assessment Link -->
                    <div v-if="course.assessment_quiz_id" class="mb-6 p-4 border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-center">
                        <p class="font-semibold text-indigo-800 dark:text-indigo-300 mb-2">Know this already?</p>
                        <Link :href="route('course.assessment.show', { course: course.slug })" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 transition ease-in-out duration-150">
                            Take Placement Assessment
                        </Link>
                    </div>


                    <div v-if="course.modules.length > 0" class="space-y-8">
                        <div v-for="module in course.modules" :key="module.id" class="border-t pt-6">
                            <h3 class="text-xl font-semibold mb-3">{{ module.title }}</h3>

                            <!-- Lesson List -->
                            <div class="mb-4">
                                <h4 class="text-sm uppercase text-gray-500 dark:text-gray-400 mb-2">Lessons</h4>
                                <ul v-if="module.lessons.length > 0" class="list-disc list-inside space-y-2 ml-4">
                                    <li v-for="lesson in module.lessons" :key="lesson.id">
                                        <Link
                                            :href="route('lessons.show', { course: course.slug, lesson: lesson.slug })"
                                            class="text-blue-600 hover:underline dark:text-blue-400"
                                            :class="{ 'font-semibold text-gray-800 dark:text-gray-200': lesson.is_completed }"
                                        >
                                            <span v-if="lesson.is_completed" class="text-green-500 mr-1">✓</span>
                                            {{ lesson.title }}
                                        </Link>
                                    </li>
                                </ul>
                                <p v-else class="text-sm text-gray-500 italic ml-4">No lessons in this module yet.</p>
                            </div>

                            <!-- Quiz List -->
                            <div v-if="module.quizzes && module.quizzes.length > 0">
                                <h4 class="text-sm uppercase text-gray-500 dark:text-gray-400 mb-2 mt-4">Quizzes</h4>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li v-for="quiz in module.quizzes" :key="quiz.id">
                                        <Link
                                            :href="route('quizzes.show', { quiz: quiz.id })"
                                            class="text-purple-600 hover:underline dark:text-purple-400"
                                        >
                                            {{ quiz.title }}
                                            <span v-if="quiz.description" class="text-xs text-gray-500 dark:text-gray-400"> - {{ quiz.description }}</span>
                                        </Link>
                                        <!-- We could add quiz completion status later -->
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                    <div v-if="course.final_review_quiz_id" class="mt-10 pt-6 border-t dark:border-gray-700 text-center">
                        <h3 class="text-xl font-semibold mb-3">Ready for a Final Challenge?</h3>
                        <Link :href="route('course.review.generate', { course: course.slug })" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-900 focus:ring ring-green-300 transition ease-in-out duration-150">
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
</template>

<style scoped>

</style>
