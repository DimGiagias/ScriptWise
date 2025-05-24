<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps({
    quizAttempts: Array,
    learningPaths: Array,
    nextSuggestedCourse: Object,
});

const page = usePage();

const preferencesForm = useForm({
    preferred_learning_style: page.props.auth.user?.preferred_learning_style || 'reading',
    learning_path_id: page.props.auth.user?.learning_path_id || null,
});

const successMessage = computed(() => page.props.flash?.success);
const errorMessage = computed(() => page.props.flash?.error);
const submitPreferences = () => {
    preferencesForm.patch(route('user.preferences.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // TODO: Maybe show a success notification
            // OR preferencesForm.reset(); // if want to clear form after successful save
        },
        onError: (errors) => {
            console.error("Preference update errors:", errors);
        }
    });
};
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
}

const learningStyleOptions = [
    { value: 'reading', label: 'Reading Preferred' },
    { value: 'visual', label: 'Visuals Preferred (e.g., Videos)' },
];

const currentLearningPathName = computed(() => {
    if (page.props.auth.user?.learning_path_id && props.learningPaths) {
        const path = props.learningPaths.find(p => p.id === page.props.auth.user.learning_path_id);
        return path ? path.name : 'Not Set';
    }
    return 'Not Set';
});

</script>

<template>
    <Head title="Dashboard" />

    <div v-if="successMessage" class="fixed top-4 right-4 z-50 mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-lg">
        {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="fixed top-4 right-4 z-50 mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded shadow-lg">
        {{ errorMessage }}
    </div>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">You're logged in!</div>
                </div>

                <!-- Learning Preferences Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4 border-b pb-2">My Learning Preferences</h3>
                        <form @submit.prevent="submitPreferences" class="space-y-6">
                            <!-- Learning Path Selection -->
                            <div>
                                <label for="learning_path_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    My Learning Path:
                                </label>
                                <select
                                    id="learning_path_id"
                                    v-model="preferencesForm.learning_path_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                >
                                    <option :value="null">-- Select a Path --</option>
                                    <option v-for="path in learningPaths" :key="path.id" :value="path.id">
                                        {{ path.name }}
                                    </option>
                                </select>
                                <p v-if="preferencesForm.errors.learning_path_id" class="mt-2 text-sm text-red-600">{{ preferencesForm.errors.learning_path_id }}</p>
                                <p v-if="preferencesForm.learning_path_id && learningPaths.find(p => p.id === preferencesForm.learning_path_id)?.description" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ learningPaths.find(p => p.id === preferencesForm.learning_path_id).description }}
                                </p>
                            </div>

                            <!-- Learning Style Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Preferred Learning Style:
                                </label>
                                <fieldset class="mt-2">
                                    <legend class="sr-only">Learning Style</legend>
                                    <div class="space-y-2">
                                        <div v-for="style in learningStyleOptions" :key="style.value" class="flex items-center">
                                            <input
                                                :id="`style_${style.value}`"
                                                type="radio"
                                                :value="style.value"
                                                v-model="preferencesForm.preferred_learning_style"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                            />
                                            <label :for="`style_${style.value}`" class="ml-3 block text-sm text-gray-700 dark:text-gray-300">
                                                {{ style.label }}
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>
                                <p v-if="preferencesForm.errors.preferred_learning_style" class="mt-2 text-sm text-red-600">{{ preferencesForm.errors.preferred_learning_style }}</p>
                            </div>

                            <div>
                                <button type="submit"
                                        :disabled="preferencesForm.processing"
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                    Save Preferences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Next Suggested Course -->
                <div v-if="nextSuggestedCourse" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-2">Next in Your Path: {{ currentLearningPathName }}</h3>
                        <div class="border p-4 rounded-md bg-gray-50 dark:bg-gray-700/50">
                            <h4 class="text-lg font-medium text-blue-700 dark:text-blue-400">{{ nextSuggestedCourse.title }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 mb-3">{{ nextSuggestedCourse.description }}</p>
                            <Link :href="route('courses.show', { course: nextSuggestedCourse.slug })"
                                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Go to Course
                            </Link>
                        </div>
                    </div>
                </div>
                <div v-else-if="page.props.auth.user?.learning_path_id" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-2">Your Learning Path: {{ currentLearningPathName }}</h3>
                        <p class="text-green-600 dark:text-green-400 font-medium">Congratulations! You've completed all available courses in this path.</p>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Check back later for new courses or explore other learning paths!</p>
                    </div>
                </div>


                <!-- Quiz History Section -->
                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4">Recent Quiz Attempts</h3>
                        <div v-if="props.quizAttempts && props.quizAttempts.length > 0">
                            <ul class="space-y-3">
                                <li v-for="attempt in quizAttempts" :key="attempt.id" class="flex justify-between items-center border-b pb-2">
                                    <div>
                                        <span class="font-medium">{{ attempt.quiz?.title ?? 'Random Review Quiz' }}</span>
                                        <!-- Add type indicator -->
                                        <span v-if="attempt.type === 'random'" class="ml-1 text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 px-1.5 py-0.5 rounded">Random</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                            on {{ formatDate(attempt.completed_at) }}
                                        </span>
                                    </div>
                                    <span class="text-lg font-semibold"
                                          :class="{
                                        'text-green-600 dark:text-green-400': attempt.score >= 80,
                                        'text-yellow-600 dark:text-yellow-400': attempt.score >= 50 && attempt.score < 80,
                                        'text-red-600 dark:text-red-400': attempt.score < 50
                                      }">
                                        {{ attempt.score ?? 'N/A' }}%
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div v-else>
                            <p>You haven't attempted any quizzes yet.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

