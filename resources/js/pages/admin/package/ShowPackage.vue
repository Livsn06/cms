<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Trash2, ChevronLeft, Package as PackageIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import CustomConfirmModal from '@/components/CustomConfirmModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDate, formatPriceWithCurrency } from '@/lib/formatters';
import { packages } from '@/routes/admin';
import { edit, destroy } from '@/routes/admin/packages';
import type { BreadcrumbItem } from '@/types';

interface Package {
    id: number;
    name: string;
    description: string;
    price: number;
    image: string | null;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    package: Package;
}>();



const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Packages', href: packages() },
    { title: props.package.name, href: '#' },
];




///====================

// State for the modal
const isDeleteModalOpen = ref(false)
const selectedPackageId = ref<number | null>(null)

// Open modal and store the ID
const promptDelete = (id: number) => {
    selectedPackageId.value = id
    isDeleteModalOpen.value = true
}

// Logic when user clicks "Continue"
const isDeleting = ref(false);

const confirmDelete = () => {
    if (!selectedPackageId.value) {
return;
}

    router.delete(destroy(selectedPackageId.value), {
        onStart: () => {
            isDeleting.value = true;
        },
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            selectedPackageId.value = null;
        },
        onFinish: () => {
            isDeleting.value = false;
        },
        preserveScroll: true,
    });
};

const editPackage = (id: number) => {
    router.get(edit(id));
};


</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head :title="`Package: ${package.name}`" />

        <div class="max-w-5xl mx-auto p-4 sm:p-8 space-y-6">
            <div class="flex items-center justify-between">
                <Button variant="ghost" as-child size="sm" class="-ml-2">
                    <Link :href="packages()">
                        <ChevronLeft class="w-4 h-4 mr-1" />
                        Back to list
                    </Link>
                </Button>

                <div class="flex items-center gap-2">
                    <Button @click="editPackage(package.id)" variant="outline" size="sm" as-child
                        class="cursor-default">
                        <div>
                            <Edit class="w-4 h-4 mr-2" />
                            Edit
                        </div>
                    </Button>

                    <Button @click="promptDelete(package.id)" variant="destructive" size="sm" as-child
                        class="cursor-default">
                        <div>
                            <Trash2 class="w-4 h-4 mr-2" />
                            Delete
                        </div>
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                <div
                    class="relative aspect-square overflow-hidden rounded-xl border bg-zinc-100 dark:bg-zinc-900 shadow-sm">
                    <img v-if="package.image" :src="package.image" :alt="package.name"
                        class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center text-zinc-400">
                        <div class="text-center">
                            <PackageIcon class="mx-auto h-12 w-12 opacity-20" />
                            <p class="mt-2 text-sm">No image available</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <Badge variant="secondary" class="mb-2">Service Package</Badge>
                        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                            {{ package.name }}
                        </h1>
                        <p class="text-2xl font-semibold mt-2 text-primary">
                            {{ formatPriceWithCurrency(package.price) }}
                            <span class="text-sm font-normal text-zinc-500">/ per person</span>
                        </p>
                    </div>

                    <Card class="bg-zinc-50/50 dark:bg-zinc-900/50">
                        <CardContent class="px-6">
                            <h3 class="text-sm font-medium text-zinc-500 uppercase tracking-wider mb-3">
                                Description & Inclusions
                            </h3>
                            <div class="whitespace-pre-wrap text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                {{ package.description }}
                            </div>
                        </CardContent>
                    </Card>

                    <div class="text-xs text-zinc-400">
                        Last updated: {{ formatDate(package.updated_at) }}
                    </div>
                </div>
            </div>
        </div>
        <CustomConfirmModal v-model:open="isDeleteModalOpen" :loading="isDeleting" title="Delete Package?"
            description="This will permanently delete the service package. This action cannot be undone."
            confirmText="Delete" variant="destructive" @confirm="confirmDelete" />
    </AppLayout>
</template>