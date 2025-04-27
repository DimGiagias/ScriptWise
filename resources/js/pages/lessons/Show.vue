<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    lesson: Object,
    course: Object,
});

</script>

<template>
    <Head :title="lesson.title" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-4">
                            <Link :href="route('courses.show', { course: course.slug })" class="text-blue-600 hover:underline dark:text-blue-400">
                                « Back to {{ course.title }}
                            </Link>
                        </div>

                        <h2 class="text-3xl font-semibold mb-4">{{ lesson.title }}</h2>

                        <div class="prose dark:prose-invert max-w-none" v-html="lesson.content"></div>

                        <div class="mt-8 border-t pt-4">
                            <button disabled class="bg-gray-400 text-white font-bold py-2 px-4 rounded opacity-50 cursor-not-allowed">
                                Mark as Complete (Coming Soon)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Pane: Placeholder for Code Editor/Terminal -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4">Code Area</h3>
                        <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded h-64 flex items-center justify-center">
                            <p class="text-gray-500">Code Editor & Terminal will appear here.</p>
                        </div>
                        <!-- Placeholder for Assignment Text -->
                        <div class="mt-4">
                            <h4 class="font-semibold">Assignment:</h4>
                            <p class="text-gray-600 dark:text-gray-400 italic">Assignment details will appear here.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
.prose code {
    background-color: #f3f4f6;
    padding: 0.2em 0.4em;
    margin: 0;
    font-size: 85%;
    border-radius: 3px;
}
.dark .prose code {
    background-color: #374151;
    color: #e5e7eb;
}
.prose p {
    margin-bottom: 1em;
}
</style>
