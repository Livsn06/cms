<script setup lang="ts">
defineProps<{
    title: string;
    value: string | number;
    trend?: string;
    trendType?: 'up' | 'down';
    label?: string;
    description?: string;
}>();
</script>

<template>
    <div class="bg-[#121212] border border-zinc-800 p-6 rounded-2xl flex flex-col justify-between min-h-[160px]">
        <div class="flex justify-between items-start">
            <span class="text-zinc-400 text-lg font-medium">{{ title }}</span>
            <div v-if="trend" :class="[
                'px-2 py-1 rounded-full text-xs font-bold flex items-center gap-1',
                trendType === 'down' ? 'bg-zinc-800 text-white' : 'bg-emerald-500/10 text-emerald-500'
            ]">
                <svg v-if="trendType === 'down'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                </svg>
                <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                {{ trend }}
            </div>
        </div>

        <div class="mt-4">
            <h2 class="text-4xl font-bold text-white">{{ value }}</h2>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <span class="text-white text-sm font-semibold">{{ label }}</span>
            <svg v-if="trendType === 'down'" class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
            </svg>
            <span class="text-zinc-500 text-sm ml-auto">{{ description }}</span>
        </div>
    </div>
</template>