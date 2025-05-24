<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ArcElement, BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip } from 'chart.js';
import { Bar, Pie } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale, BarElement);

const props = defineProps({
    totalQuizzesAttempted: Number,
    learningStreak: Number,
    quizScoreDistribution: Object,
    lessonContributionData: Array,
});

// --- Pie Chart Data & Options ---
const pieChartData = {
    labels: ['Excellent (80-100%)', 'Good (50-79%)', 'Needs Improvement (<50%)'],
    datasets: [
        {
            backgroundColor: ['#4CAF50', '#FFC107', '#F44336'], // Green, Yellow, Red
            data: [props.quizScoreDistribution.green || 0, props.quizScoreDistribution.yellow || 0, props.quizScoreDistribution.red || 0],
        },
    ],
};

const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Quiz Score Distribution',
        },
    },
};

// --- Contribution Graph Data & Options (Bar Chart for now) ---
const processContributionDataForBarChart = () => {
    const monthlyCompletions = {};
    props.lessonContributionData?.forEach((item) => {
        const monthYear = item.date.substring(0, 7); // YYYY-MM
        if (!monthlyCompletions[monthYear]) {
            monthlyCompletions[monthYear] = 0;
        }
        monthlyCompletions[monthYear] += item.count;
    });

    const labels = Object.keys(monthlyCompletions).sort();
    const data = labels.map((label) => monthlyCompletions[label]);

    return {
        labels: labels.map((label) => new Date(label + '-01').toLocaleString('default', { month: 'short', year: 'numeric' })), // Format for display
        datasets: [
            {
                label: 'Lessons Completed',
                backgroundColor: '#3B82F6',
                data: data,
            },
        ],
    };
};

const contributionBarChartData = processContributionDataForBarChart();

const contributionBarChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        title: {
            display: true,
            text: 'Monthly Lesson Completions (Last Year)',
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                precision: 0,
            },
        },
    },
};
</script>

<template>
    <Head title="My Statistics" />
    <AppLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-semibold">My Learning Statistics</h2>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Learning Streak</h3>
                        <p class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ learningStreak }} {{ learningStreak === 1 ? 'Day' : 'Days' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Consecutive days with lesson completions.</p>
                    </div>
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Quizzes Attempted</h3>
                        <p class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ totalQuizzesAttempted }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total number of quizzes you've started.</p>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Pie Chart -->
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="h-80 md:h-96">
                            <!-- Set a fixed height for the chart container -->
                            <Pie
                                v-if="quizScoreDistribution.green || quizScoreDistribution.yellow || quizScoreDistribution.red"
                                :data="pieChartData"
                                :options="pieChartOptions"
                            />
                            <div v-else class="flex h-full items-center justify-center text-gray-500 dark:text-gray-400">
                                No quiz data yet to display distribution.
                            </div>
                        </div>
                    </div>

                    <!-- Contribution Bar Chart -->
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="h-80 md:h-96">
                            <!-- Set a fixed height for the chart container -->
                            <Bar v-if="lessonContributionData.length > 0" :data="contributionBarChartData" :options="contributionBarChartOptions" />
                            <div v-else class="flex h-full items-center justify-center text-gray-500 dark:text-gray-400">
                                No lesson completion data yet.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
