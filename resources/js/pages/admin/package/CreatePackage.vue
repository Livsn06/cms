<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { XCircleIcon } from 'lucide-vue-next';
import { ref, useTemplateRef } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { packages } from '@/routes/admin';
import { create, store } from '@/routes/admin/packages';
import type { BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Packages', href: packages() },
    { title: 'Create', href: create() },
];

interface PackageForm {
    name: string;
    price: string;
    description: string;
    image: File | null;
}

const form = useForm<PackageForm>({
    name: '',
    price: '',
    description: '',
    image: null,
});

// References
const imagePreview = ref<string | null>(null);
const fileInput = useTemplateRef<HTMLInputElement>('fileInput');

/**
 * Handle file selection and generate preview URL
 */
const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (target.files && target.files[0]) {
        if (imagePreview.value) {
            URL.revokeObjectURL(imagePreview.value);
        }

        const file = target.files[0];
        imagePreview.value = URL.createObjectURL(file);
        form.image = file; // Assign file to useForm
    }
};

/**
 * Reset both the visual preview and the actual input element
 */
const clearImage = () => {
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
        imagePreview.value = null;
    }

    form.image = null;

    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const submit = () => {
    const routeData = store();
    form.post(routeData.url, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            clearImage();
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Create Package" />

        <div class="flex flex-col space-y-6 w-full max-w-2xl p-8 mx-auto">
            <Heading variant="small" title="Create Package" description="What would you like to offer?" />

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid gap-2">
                    <Label for="name" class="dark:text-zinc-300">Package Name</Label>
                    <Input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                        placeholder="e.g. Buffet Package" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="price" class="dark:text-zinc-300">
                        Price <span class="text-zinc-400 font-normal">(Per Person)</span>
                    </Label>
                    <Input id="price" type="number" step="0.01" min="0" class="mt-1 block w-full" v-model="form.price"
                        required placeholder="0.00" />
                    <InputError :message="form.errors.price" />
                </div>

                <div class="grid gap-2">
                    <Label for="description" class="dark:text-zinc-300">Description</Label>
                    <Textarea id="description" class="mt-1 block w-full min-h-[100px]" v-model="form.description"
                        required placeholder="Describe the inclusions..." />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="image" class="dark:text-zinc-300">Package Image</Label>
                    <div class="flex items-center gap-4">
                        <Input ref="fileInput" id="image" type="file" accept="image/*"
                            class="mt-1 block w-full cursor-pointer dark:bg-zinc-900 dark:border-zinc-700"
                            @change="handleImageChange" />
                    </div>
                    <InputError :message="form.errors.image" />
                </div>

                <div v-if="imagePreview" class="relative mt-2 w-full sm:w-64 group">
                    <p class="text-xs text-zinc-500 mb-2 font-medium">Image Preview:</p>
                    <div
                        class="relative overflow-hidden rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 aspect-square">
                        <img :src="imagePreview"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        <button type="button" @click="clearImage"
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition-colors">
                            <XCircleIcon class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-4 border-t dark:border-zinc-800">
                    <Button type="submit" :disabled="form.processing" class="w-full sm:w-auto">
                        <Spinner v-if="form.processing" class="mr-2" />
                        {{ form.processing ? 'Creating...' : 'Create Package' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>