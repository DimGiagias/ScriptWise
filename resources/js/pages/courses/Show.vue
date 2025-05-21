<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    course: Object,
});

</script>

<template>
    <Head :title="course.title" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <Link :href="route('courses.index')" class="text-blue-600 hover:underline dark:text-blue-400">
                            « Back to Courses
                        </Link>
                    </div>

                    <h2 class="text-3xl font-semibold mb-2">{{ course.title }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ course.description }}</p>

                    <div v-if="course.modules.length > 0" class="space-y-6">
                        <div v-for="module in course.modules" :key="module.id" class="border-t pt-4">
                            <h3 class="text-xl font-semibold mb-3">{{ module.title }}</h3>
                            <ul v-if="module.lessons.length > 0" class="list-disc list-inside space-y-2 ml-4">
                                <li v-for="lesson in module.lessons" :key="lesson.id">
                                    <Link
                                        :href="route('lessons.show', { course: course.slug, lesson: lesson.slug })"
                                        class="text-blue-600 hover:underline dark:text-blue-400"
                                        :class="{ 'font-semibold text-gray-800 dark:text-gray-200': lesson.is_completed }"
                                    >
                                        <!-- Add a checkmark if completed -->
                                        <span v-if="lesson.is_completed" class="text-green-500 mr-1">✓</span>
                                        {{ lesson.title }}
                                    </Link>
                                </li>
                            </ul>
                            <p v-else class="text-sm text-gray-500 italic ml-4">No lessons in this module yet.</p>
                        </div>
                    </div>
                    <div v-else>
                        <p>This course doesn't have any modules yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

