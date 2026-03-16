<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, X } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

type SharedProps = {
    auth: { user: any };
    flash: {
        success: string | null;
        error?: string | null;
    };
    name: string;
    sidebarOpen: boolean;
}

interface Toast {
    id: number;
    message: string;
}

const page = usePage<SharedProps>();
const toasts = ref<Toast[]>([]);

// Get the message from Inertia Flash props
const successMessage = computed(() => page.props.flash?.success);

const removeToast = (id: number) => {
    toasts.value = toasts.value.filter(t => t.id !== id);
};

watch(successMessage, (newMsg) => {
    if (newMsg) {
        const id = Date.now();

        // 1. Add new toast to the stack
        toasts.value.push({
            id,
            message: newMsg
        });

        // 2. Clear the flash prop immediately so the same message can trigger again
        page.props.flash.success = null;

        // 3. Auto-remove this specific toast after 4 seconds
        setTimeout(() => {
            removeToast(id);
        }, 4000);
    }
}, { deep: true });
</script>

<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 left-4 z-[100] flex flex-col gap-2 pointer-events-none 
                    sm:top-auto sm:left-auto sm:bottom-5 sm:right-5 sm:w-full sm:max-w-sm">

            <TransitionGroup enter-active-class="transition duration-300 ease-out"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-10"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition duration-200 ease-in absolute" leave-from-class="opacity-100"
                leave-to-class="opacity-0" move-class="transition duration-300 ease-in-out">
                <div v-for="toast in toasts" :key="toast.id" class="pointer-events-auto flex w-full items-center gap-3 rounded-lg border 
                           bg-background text-foreground border-border p-4 shadow-lg" role="alert">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10">
                        <CheckCircle2 class="h-5 w-5 text-primary" />
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-semibold tracking-tight">Success</p>
                        <p class="text-sm text-muted-foreground">{{ toast.message }}</p>
                    </div>

                    <button @click="removeToast(toast.id)" type="button"
                        class="rounded-md p-1 opacity-50 transition-opacity hover:opacity-100 focus:outline-none">
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
/* Ensures that when an item is removed, the ones below it slide up smoothly */
.v-move {
    transition: transform 0.3s ease;
}
</style>