<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CustomStatCard from '@/components/CustomStatCard.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatPriceWithCurrency } from '@/lib/formatters';
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
        packageTrend: string;
    }
}>();
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-3">

                <CustomStatCard title="Total Bookings" :value="stats.totalBookings" :trend="stats.bookingTrend"
                    :trendType="stats.bookingTrend.includes('-') ? 'down' : 'up'"
                    :label="stats.bookingTrend.includes('-') ? 'Down this period' : 'Up this period'"
                    :description="stats.bookingTrend.includes('-') ? 'Acquisition needs attention' : 'Performing well'" />

                <CustomStatCard title="Active Packages" :value="stats.activePackages" :trend="stats.packageTrend"
                    :trendType="stats.packageTrend.includes('-') ? 'down' : 'up'" label="Library Growth"
                    :description="parseFloat(stats.packageTrend) > 0 ? 'Adding new variety' : 'Inventory stable'" />

                <CustomStatCard title="Expected Revenue"
                    :value="formatPriceWithCurrency(parseFloat(stats.totalRevenue))" label="Total value"
                    description="Excludes cancelled bookings" />

            </div>

            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
