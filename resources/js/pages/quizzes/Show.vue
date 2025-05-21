<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    quiz: Object,
});

const form = useForm({
    answers: {},
});

props.quiz.questions.forEach(q => {
    form.answers[q.id] = null;
});

console.log(props.quiz.questions)

const submitQuiz = () => {
    // Ensure all questions are answered
    const unanswered = props.quiz.questions.some(q => form.answers[q.id] === null);
    if (unanswered && !confirm('You have unanswered questions. Submit anyway?')) {
        return;
    }

    form.post(route('quizzes.submit', { quiz: props.quiz.id }), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Submission errors:', errors);
            alert('There was an error submitting your answers. Please check the console.');
        },
    });
};

const allAnswered = computed(() => {
    if (!props.quiz.questions || props.quiz.questions.length === 0) return true;
    return props.quiz.questions.every(q => form.answers[q.id] !== null);
});

</script>

<template>
    <Head :title="`Quiz: ${quiz.title}`" />

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <h2 class="text-2xl md:text-3xl font-semibold mb-2">{{ quiz.title }}</h2>
                    <p v-if="quiz.description" class="text-gray-600 dark:text-gray-400 mb-6">{{ quiz.description }}</p>

                    <form @submit.prevent="submitQuiz">
                        <div v-if="quiz.questions.length > 0" class="space-y-6">
                            <div v-for="(question, index) in quiz.questions" :key="question.id" class="border-t pt-6">
                                <p class="font-semibold mb-3">
                                    {{ index + 1 }}. {{ question.text }}
                                </p>

                                <!-- Multiple Choice Options -->
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
                            </div>

                            <!-- Submission Button -->
                            <div class="mt-8 pt-6 border-t">
                                <button
                                    type="submit"
                                    :disabled="form.processing || !allAnswered"
                                    class="w-full md:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ form.processing ? 'Submitting...' : 'Submit Answers' }}</span>
                                </button>
                                <p v-if="!allAnswered && !form.processing" class="text-sm text-red-600 mt-2">Please answer all questions.</p>
                            </div>

                        </div>
                        <div v-else>
                            <p>This quiz doesn't have any questions yet.</p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</template>
