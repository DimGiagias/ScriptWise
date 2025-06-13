<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    course: Object,
    quizData: Object,
    message: String,
});

const form = useForm({
    answers: {},
    quizId: props.quizData?.id || null,
});

if (props.quizData && props.quizData.questions) {
    props.quizData.questions.forEach((q) => {
        form.answers[q.id] = null;
    });
}

const submitQuiz = () => {
    if (!props.quizData || !props.course) return;

    form.post(route('course.review.submit', { course: props.course.slug }), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Submission errors:', errors);
            alert('Error submitting review quiz. Check console or try again.');
            form.processing = false;
        },
    });
};

const allAnswered = computed(() => {
    if (!props.quizData?.questions || props.quizData.questions.length === 0) return true;
    return props.quizData.questions.every((q) => {
        const answer = form.answers[q.id];
        return answer !== null && answer !== undefined && (typeof answer === 'string' ? answer.trim() !== '' : true);
    });
});

const quizPageTitle = computed(() => {
    return props.quizData?.title || `Review: ${props.course?.title || 'Course'}`;
});
const quizMainTitle = computed(() => {
    return props.quizData?.title || `Final Review: ${props.course?.title || 'Course'}`;
});
const quizDescription = computed(() => {
    return props.quizData?.description || `This quiz will test your knowledge on topics covered in the '${props.course?.title || 'Course'}'.`;
});
</script>

<template>
    <AppLayout>
        <Head :title="quizPageTitle" />

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 md:p-8 dark:text-gray-100">
                        <div class="mb-4">
                            <Link
                                :href="route('courses.show', { course: course.slug })"
                                class="text-sm text-blue-600 hover:underline dark:text-blue-400"
                            >
                                « Back to {{ course.title }}
                            </Link>
                        </div>

                        <h2 class="mb-2 text-2xl font-semibold md:text-3xl">{{ quizMainTitle }}</h2>

                        <div
                            v-if="message"
                            class="mb-4 rounded-lg bg-yellow-100 p-4 text-sm text-yellow-700 dark:bg-yellow-200 dark:text-yellow-800"
                            role="alert"
                        >
                            {{ message }}
                            <div v-if="!quizData" class="mt-4">
                                <Link :href="route('courses.show', { course: course.slug })" class="font-medium text-blue-600 hover:underline">
                                    Return to Course
                                </Link>
                            </div>
                        </div>

                        <!-- Display Quiz Form if generated -->
                        <div v-if="quizData">
                            <p class="mb-6 text-gray-600 dark:text-gray-400">{{ quizDescription }}</p>

                            <form @submit.prevent="submitQuiz">
                                <div v-if="quizData.questions && quizData.questions.length > 0" class="space-y-6">
                                    <div
                                        v-for="(question, index) in quizData.questions"
                                        :key="question.id"
                                        class="border-t pt-6 dark:border-gray-700"
                                    >
                                        <p class="mb-3 font-semibold">{{ index + 1 }}. {{ question.text }}</p>
                                        <!-- Multiple Choice -->
                                        <div v-if="question.type === 'multiple_choice'" class="ml-4 space-y-2">
                                            <label
                                                v-for="option in question.options"
                                                :key="option.id"
                                                class="flex cursor-pointer items-center rounded-md border p-3 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700"
                                                :class="{
                                                    'border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/30':
                                                        form.answers[question.id] === option.id,
                                                }"
                                            >
                                                <input
                                                    type="radio"
                                                    :name="`question_${question.id}`"
                                                    :value="option.id"
                                                    v-model="form.answers[question.id]"
                                                    class="mr-3 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                                />
                                                <span>{{ option.text }}</span>
                                            </label>
                                        </div>

                                        <!-- True/False -->
                                        <div v-else-if="question.type === 'true_false'" class="ml-4 space-y-2">
                                            <label
                                                class="flex cursor-pointer items-center rounded-md border p-3 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700"
                                                :class="{
                                                    'border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/30':
                                                        form.answers[question.id] === 'true',
                                                }"
                                            >
                                                <input
                                                    type="radio"
                                                    :name="`question_${question.id}`"
                                                    value="true"
                                                    v-model="form.answers[question.id]"
                                                    class="mr-3 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                                />
                                                <span>True</span>
                                            </label>
                                            <label
                                                class="flex cursor-pointer items-center rounded-md border p-3 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700"
                                                :class="{
                                                    'border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/30':
                                                        form.answers[question.id] === 'false',
                                                }"
                                            >
                                                <input
                                                    type="radio"
                                                    :name="`question_${question.id}`"
                                                    value="false"
                                                    v-model="form.answers[question.id]"
                                                    class="mr-3 h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600"
                                                />
                                                <span>False</span>
                                            </label>
                                        </div>

                                        <div v-else-if="question.type === 'fill_blank'" class="mt-2 ml-4">
                                            <input
                                                type="text"
                                                v-model="form.answers[question.id]"
                                                :name="`question_${question.id}`"
                                                placeholder="Type your answer here"
                                                class="focus:ring-opacity-50 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 md:w-1/2 dark:border-gray-600 dark:bg-gray-700 dark:placeholder-gray-400"
                                            />
                                        </div>
                                    </div>

                                    <div class="mt-8 border-t pt-6 dark:border-gray-700">
                                        <button
                                            type="submit"
                                            :disabled="form.processing || !allAnswered"
                                            class="inline-flex w-full items-center justify-center rounded-md border border-transparent bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:w-auto"
                                        >
                                            <svg
                                                v-if="form.processing"
                                                class="mr-3 -ml-1 h-5 w-5 animate-spin text-white"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                            <span>{{ form.processing ? 'Submitting...' : 'Submit Review Answers' }}</span>
                                        </button>
                                        <p v-if="!allAnswered && !form.processing" class="mt-2 text-sm text-red-600">Please answer all questions.</p>
                                    </div>
                                </div>
                                <div v-else class="mt-6">
                                    <p class="text-gray-500 italic">No questions available for this review quiz at the moment.</p>
                                </div>
                            </form>
                        </div>
                        <div v-else-if="!message" class="mt-6">
                            <p class="text-gray-500 italic">Loading review quiz or no review available...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Add any specific styles if needed */
</style>
