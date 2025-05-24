<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    quizData: Object,
    message: String,
});

console.log(props.quizData)
const form = useForm({
    answers: {},
    quizId: props.quizData?.id || null,
});

if (props.quizData) {
    props.quizData.questions.forEach(q => {
        form.answers[q.id] = null;
    });
}

const submitQuiz = () => {
    if (!props.quizData) return;

    form.post(route('random-quiz.submit'), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Submission errors:', errors);
            alert('Error submitting quiz. Check console or try again.');
            form.processing = false;
        }
    });
};

const allAnswered = computed(() => {
    if (!props.quizData?.questions || props.quizData.questions.length === 0) return true;
    return props.quizData.questions.every(q => form.answers[q.id] !== null);
});

</script>

<template>
<AppLayout>
    <Head title="Random Review Quiz" />

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <h2 class="text-2xl md:text-3xl font-semibold mb-2">Random Review Quiz</h2>

                    <!-- Display message if quiz couldn't generate -->
                    <div v-if="message" class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg dark:bg-yellow-200 dark:text-yellow-800" role="alert">
                        {{ message }}
                        <div v-if="!quizData" class="mt-4">
                            <Link :href="route('dashboard')" class="font-medium text-blue-600 hover:underline">
                                Go to Dashboard
                            </Link>
                        </div>
                    </div>

                    <!-- Display Quiz Form if generated -->
                    <div v-if="quizData">
                        <p v-if="quizData.description" class="text-gray-600 dark:text-gray-400 mb-6">{{ quizData.description }}</p>

                        <form @submit.prevent="submitQuiz">
                            <div v-if="quizData.questions.length > 0" class="space-y-6">
                                <!-- Question Loop -->
                                <div v-for="(question, index) in quizData.questions" :key="question.id" class="border-t pt-6">
                                    <p class="font-semibold mb-3">
                                        {{ index + 1 }}. {{ question.text }}
                                    </p>
                                    <div v-if="question.type === 'multiple_choice'" class="space-y-2 ml-4">
                                        <label v-for="option in question.options" :key="option.id"
                                               class="flex items-center p-3 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer"
                                               :class="{ 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700': form.answers[question.id] === option.id }">
                                            <input
                                                type="radio"
                                                :name="`question_${question.id}`"
                                                :value="option.id"
                                                v-model="form.answers[question.id]"
                                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 mr-3"
                                            />
                                            <span>{{ option.text }}</span>
                                        </label>
                                    </div>

                                    <!-- True/False -->
                                    <div v-else-if="question.type === 'true_false'" class="space-y-2 ml-4">
                                        <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer"
                                               :class="{ 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700': form.answers[question.id] === 'true' }">
                                            <input
                                                type="radio"
                                                :name="`question_${question.id}`"
                                                value="true"
                                                v-model="form.answers[question.id]"
                                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 mr-3"
                                            />
                                            <span>True</span>
                                        </label>
                                        <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer"
                                               :class="{ 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700': form.answers[question.id] === 'false' }">
                                            <input
                                                type="radio"
                                                :name="`question_${question.id}`"
                                                value="false"
                                                v-model="form.answers[question.id]"
                                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 mr-3"
                                            />
                                            <span>False</span>
                                        </label>
                                    </div>

                                    <!-- Fill in the Blank -->
                                    <div v-else-if="question.type === 'fill_blank'" class="ml-4 mt-2">
                                        <input
                                            type="text"
                                            v-model="form.answers[question.id]"
                                            :name="`question_${question.id}`"
                                            placeholder="Type your answer here"
                                            class="mt-1 block w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                        />
                                    </div>
                                </div>

                                <!-- Submission Button -->
                                <div class="mt-8 pt-6 border-t">
                                    <button
                                        type="submit"
                                        :disabled="form.processing || !allAnswered"
                                        class="w-full md:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>{{ form.processing ? 'Submitting...' : 'Submit Answers' }}</span>
                                    </button>
                                    <p v-if="!allAnswered && !form.processing" class="text-sm text-red-600 mt-2">Please answer all questions.</p>
                                </div>

                            </div>
                            <div v-else>
                                <p>Could not load questions for this quiz.</p>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</AppLayout>
</template>

<style scoped>

</style>
