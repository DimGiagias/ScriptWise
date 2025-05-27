<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    lesson: Object,
    course: Object,
});

const page = usePage();
const successMessage = computed(() => page.props.flash?.success);

</script>

<template>
    <Head :title="lesson.title" />

    <div v-if="successMessage" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ successMessage }}
    </div>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
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

                        <div class="mt-8 border-t pt-6">
                            <!-- Show completed message -->
                            <div v-if="lesson.is_completed" class="flex items-center p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-md text-green-700 dark:text-green-300 font-semibold">
                                <svg class="w-6 h-6 mr-2 fill-current" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                <span>Lesson Completed!</span>
                            </div>
                            <!-- Show button if not completed -->
                            <Link v-else
                                  :href="route('lessons.complete', { lesson: lesson.id })"
                                  method="post"
                                  as="button"
                                  type="button"
                                  preserve-scroll
                                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Mark as Complete
                            </Link>
                        </div>
                    </div>
                </div>

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
