<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onBeforeUnmount, shallowRef } from 'vue';
import loader from '@monaco-editor/loader';

const props = defineProps({
    lesson: Object,
    course: Object,
});

const page = usePage();
const successMessage = computed(() => page.props.flash?.success);

// --- Monaco Editor Setup ---
const editorContainer = ref(null);
const editorInstance = shallowRef(null);
const editorCode = ref(props.lesson.initial_code || '');

onMounted(() => {
    loader.init().then((monaco) => {
        if (editorContainer.value) {
            const editor = monaco.editor.create(editorContainer.value, {
                value: editorCode.value,
                language: 'javascript',
                theme: 'vs-dark',
                automaticLayout: true,
                minimap: { enabled: false },
                fontSize: 14,
                tabSize: 2,
            });

            editor.onDidChangeModelContent(() => {
                editorCode.value = editor.getValue();
            });

            editorInstance.value = editor;
        }
    }).catch(error => console.error('Monaco Editor loading failed:', error));
});

onBeforeUnmount(() => {
    editorInstance.value?.dispose();
});

const terminalOutput = ref('');
const terminalError = ref('');
const isExecuting = ref(false);
const checkResult = ref(null);

const runCode = () => {
    if (isExecuting.value) return;

    isExecuting.value = true;
    terminalOutput.value = '';
    terminalError.value = '';
    checkResult.value = null;
    const codeToRun = editorCode.value;

    const originalConsoleLog = console.log;
    let capturedOutput = '';

    console.log = (...args) => {
        capturedOutput += args.map(arg => {
            if (typeof arg === 'object' && arg !== null) {
                try { return JSON.stringify(arg); } catch (e) { return 'Unserializable Object'; }
            }
            return String(arg);
        }).join(' ') + '\n';
    };

    try {
        new Function(codeToRun)();

        terminalOutput.value = capturedOutput.trim();

        if (props.lesson.expected_output !== null && props.lesson.expected_output !== undefined) {
            if (terminalOutput.value.trim() === props.lesson.expected_output.trim()) {
                checkResult.value = 'correct';
                if (!props.lesson.is_completed) {
                    console.log('Code correct, marking lesson complete...');
                    router.post(route('lessons.complete', { lesson: props.lesson.id }), {}, {
                        preserveScroll: true,
                        onSuccess: () => {
                            console.log('Lesson marked complete successfully via Inertia POST.');
                        },
                        onError: (errors) => {
                            console.error('Error marking lesson complete:', errors);
                            alert('Could not mark lesson as complete. Please try running the code again or contact support.');
                        }
                    });
                }
            } else {
                checkResult.value = 'incorrect';
            }
        }

    } catch (error) {
        console.error("Execution Error:", error);
        terminalError.value = error.toString();
        checkResult.value = 'error';
    } finally {
        console.log = originalConsoleLog;
        isExecuting.value = false;
    }
};

const userStyle = computed(() => page.props.auth.user?.preferred_learning_style || 'balanced');

</script>

<template>
<AppLayout>
    <Head :title="lesson.title" />

    <div v-if="successMessage" class="fixed top-4 right-4 z-50 mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-lg">
        {{ successMessage }}
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Left Pane: Lesson Content & Assignment -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col h-full">
                        <div class="mb-4">
                            <Link :href="route('courses.show', { course: course.slug })" class="text-blue-600 hover:underline dark:text-blue-400">
                                « Back to {{ course.title }}
                            </Link>
                        </div>

                        <h2 class="text-3xl font-semibold mb-4">{{ lesson.title }}</h2>

                        <!-- Lesson Content -->
                        <!-- Video Content (if preferred visual AND video exists) -->
                        <div v-if="userStyle === 'visual' && lesson.video_embed_html" class="mb-6">
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Video Explanation:</h3>
                            <div class="aspect-w-16 aspect-h-9 rounded-md overflow-hidden shadow-lg" v-html="lesson.video_embed_html">
                            </div>
                        </div>

                        <!-- Text Content (always show, but position varies) -->
                        <div class="prose dark:prose-invert max-w-none mb-6" v-html="lesson.content"></div>

                        <!-- Video Content (if style is NOT visual-first AND video exists) -->
                        <div v-if="userStyle !== 'visual' && lesson.video_embed_html" class="mt-6 pt-6 border-t dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Video Explanation (Optional):</h3>
                            <div class="aspect-w-16 aspect-h-9 rounded-md overflow-hidden shadow-lg" v-html="lesson.video_embed_html">
                            </div>
                        </div>

                        <!-- Assignment Section -->
                        <div v-if="lesson.assignment" class="mt-auto border-t pt-6">
                            <h3 class="text-xl font-semibold mb-3">Assignment</h3>
                            <div class="prose dark:prose-invert max-w-none text-sm" v-html="lesson.assignment"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Pane: Editor, Terminal, Actions -->
                <div class="bg-gray-900 text-gray-100 overflow-hidden shadow-sm sm:rounded-lg flex flex-col" style="height: 75vh;">
                    <!-- Editor Area -->
                    <div ref="editorContainer" class="flex-grow h-1/2">
                        <!-- Monaco Editor will be mounted here -->
                    </div>

                    <!-- Action Bar -->
                    <div class="flex-shrink-0 px-4 py-2 border-t border-gray-700 flex justify-between items-center">
                        <button @click="runCode" :disabled="isExecuting" class="px-4 py-1 bg-blue-600 hover:bg-blue-700 rounded text-sm font-medium disabled:opacity-50">
                            <span v-if="isExecuting">Running...</span>
                            <span v-else>Run Code</span>
                        </button>
                        <div v-if="lesson.is_completed" class="flex items-center p-1 bg-green-900/30 border border-green-700 rounded-md text-green-300 text-xs font-semibold">
                            <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                            <span>Completed</span>
                        </div>
                    </div>

                    <!-- Terminal Output Area -->
                    <div class="flex-shrink-0 bg-black p-3 text-sm font-mono overflow-auto h-1/3 border-t border-gray-700">
                        <div v-if="checkResult === 'correct'" class="text-green-400 mb-2">✓ Output matches expected result.</div>
                        <div v-if="checkResult === 'incorrect'" class="text-yellow-400 mb-2">! Output does not match expected result.</div>
                        <div v-if="checkResult === 'error'" class="text-red-400 mb-2">! Code execution failed.</div>

                        <pre v-if="terminalOutput" class="whitespace-pre-wrap text-gray-200">{{ terminalOutput }}</pre>
                        <pre v-if="terminalError" class="whitespace-pre-wrap text-red-400">{{ terminalError }}</pre>
                        <span v-if="!terminalOutput && !terminalError && !isExecuting && checkResult === null" class="text-gray-500">Output will appear here...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AppLayout>
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

[ref="editorContainer"] {
    min-height: 200px;
    height: 100%;
}
</style>
