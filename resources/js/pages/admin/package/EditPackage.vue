<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import { ref, useTemplateRef, computed, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { packages } from '@/routes/admin';
import { update } from '@/routes/admin/packages';
import { Textarea } from '@/components/ui/textarea';
import { Spinner } from '@/components/ui/spinner';
import { XCircleIcon, RotateCcw } from 'lucide-vue-next';

interface Package {
    id: number;
    name: string;
    description: string;
    price: number | string;
    old_image: string | null; // Passed from controller
    updated_at: string;
}

const props = defineProps<{
    breadcrumbItems: Array<{ title: string; href: string }>;
    package: Package;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Packages', href: packages() },
    { title: 'Edit', href: '#' },
];

// 1. Initialize Form State
const form = useForm({
    _method: 'PUT',
    name: props.package.name,
    price: props.package.price,
    description: props.package.description,
    image: null as File | null,
    old_image: props.package.old_image,
});

// 2. Local UI State
const imagePreview = ref<string | null>(props.package.old_image);
const fileInput = useTemplateRef<HTMLInputElement>('fileInput');

const isLocalBlob = computed(() => imagePreview.value?.startsWith('blob:'));

// Logic to check if any field (including the image state) has changed from the original prop
const hasChanges = computed(() => {
    return form.isDirty || imagePreview.value !== props.package.old_image;
});

/**
 * Handle new file selection
 */
const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.image = file;
        form.old_image = null; // Clear old_image because we are replacing it with a new file

        if (isLocalBlob.value) {
            URL.revokeObjectURL(imagePreview.value!);
        }
        imagePreview.value = URL.createObjectURL(file);
    }
};

/**
 * Completely clear the image (Sets intent to NULL on server)
 */
const clearImage = () => {
    if (isLocalBlob.value) {
        URL.revokeObjectURL(imagePreview.value!);
    }
    imagePreview.value = null;
    form.image = null;
    form.old_image = null; // Tells the controller to delete the image entirely
    if (fileInput.value) fileInput.value.value = '';
};

/**
 * Revert everything to the original props
 */
const resetForm = () => {
    form.reset(); // Reverts name, price, description based on defaults
    if (isLocalBlob.value) {
        URL.revokeObjectURL(imagePreview.value!);
    }
    imagePreview.value = props.package.old_image;
    form.old_image = props.package.old_image;
    form.image = null;
    if (fileInput.value) fileInput.value.value = '';
};

const submit = () => {
    const routeData = update(props.package.id);

    // Using form.post with _method: 'PUT' for multipart/form-data support in PHP
    form.post(routeData.url, {
        preserveScroll: true,
        onSuccess: () => {
            if (fileInput.value) fileInput.value.value = '';
        },
    });
};

/**
 * THE REFRESH ENGINE:
 * Syncs the form defaults when props change (after a successful update)
 */
watch(() => props.package, (newPkg) => {
    form.defaults({
        name: newPkg.name,
        price: newPkg.price,
        description: newPkg.description,
        old_image: newPkg.old_image,
    });
    // Resetting here ensures the UI state (isDirty) clears and reflects the fresh data
    resetForm();
}, { deep: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Edit Package" />

        <div class="flex flex-col space-y-6 w-full max-w-2xl p-8 mx-auto">
            <Heading variant="small" title="Edit Package" description="Update the details of this offer." />

            <form @submit.prevent="submit" class="space-y-6">
                <input type="hidden" v-model="form.old_image" />

                <div class="grid gap-2">
                    <Label for="name">Package Name</Label>
                    <Input id="name" v-model="form.name" type="text" required />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="price">Price</Label>
                    <Input id="price" v-model="form.price" type="number" step="0.01" required />
                    <InputError :message="form.errors.price" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Textarea id="description" v-model="form.description" class="min-h-[100px]" required />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="image">Change Image</Label>
                    <Input ref="fileInput" id="image" type="file" accept="image/*" @change="handleImageChange" />
                    <InputError :message="form.errors.image" />
                </div>

                <div v-if="imagePreview" class="relative mt-2 w-full sm:w-64 group">
                    <p class="text-xs text-zinc-500 mb-2 font-medium uppercase tracking-tighter">
                        {{ isLocalBlob ? 'New Selection' : 'Current Image' }}
                    </p>
                    <div
                        class="relative overflow-hidden rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 aspect-square">
                        <img :src="imagePreview"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />

                        <button type="button" @click="clearImage"
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600 transition-colors">
                            <XCircleIcon class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div v-else-if="props.package.old_image && !imagePreview"
                    class="p-4 border-2 border-dashed rounded-lg text-center text-sm text-zinc-500 bg-zinc-50 dark:bg-zinc-900/50">
                    Image marked for deletion. Save to confirm or click reset to restore.
                </div>

                <div class="flex items-center gap-4 pt-4 border-t dark:border-zinc-800">
                    <Button type="submit" :disabled="form.processing">
                        <Spinner v-if="form.processing" class="mr-2" />
                        Update Package
                    </Button>

                    <Button v-if="hasChanges" type="button" variant="ghost" @click="resetForm"
                        :disabled="form.processing">
                        <RotateCcw class="w-4 h-4 mr-2" />
                        Reset
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>