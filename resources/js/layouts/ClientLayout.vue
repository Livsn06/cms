<script setup lang="ts">
import CustomToast from '@/components/CustomToast.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div>
        <slot />
        <CustomToast />
    </div>
</template>
