<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue'; // Adjust if your layout name differs
import { Head, Link } from '@inertiajs/vue3';

// The 'courses' data is automatically passed as a prop by Inertia
defineProps({
    courses: Array,
});

// Optional: Define layout for this page
defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="Courses" />

    <!-- Content within the AuthenticatedLayout slot -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-2xl font-semibold mb-6">Available Courses</h2>

                    <div v-if="courses.length > 0" class="space-y-4">
                        <div v-for="course in courses" :key="course.id" class="border p-4 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <h3 class="text-xl font-medium mb-2">
                                <!-- Use Inertia's Link component for SPA navigation -->
                                <Link :href="route('courses.show', { course: course.slug })" class="hover:underline">
                                    {{ course.title }}
                                </Link>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ course.description }}</p>
                        </div>
                    </div>
                    <div v-else>
                        <p>No courses available at the moment.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
