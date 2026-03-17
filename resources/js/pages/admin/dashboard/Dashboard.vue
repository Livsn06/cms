<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CustomStatCard from '@/components/CustomStatCard.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];


defineProps<{
    stats: {
        totalBookings: number;
        activePackages: number;
        totalRevenue: string;
        bookingTrend: string;
    }
}>();
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-3">

                <CustomStatCard title="Total Bookings" :value="stats.totalBookings" trend="-20%" trendType="down"
                    label="Down 20% this period" description="Acquisition needs attention" />

                <CustomStatCard title="Active Packages" :value="stats.activePackages" trend="+5%" trendType="up"
                    label="Growing library" description="Up from last month" />

                <CustomStatCard title="Expected Revenue" :value="'$' + stats.totalRevenue" label="Total value"
                    description="Pending + Confirmed" />

            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
