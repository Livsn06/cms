<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog'

interface Props {
    open: boolean
    title?: string
    description?: string
    cancelText?: string
    confirmText?: string
    variant?: 'default' | 'destructive'
    loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Are you absolutely sure?',
    description: 'This action cannot be undone.',
    cancelText: 'Cancel',
    confirmText: 'Continue',
    variant: 'default'
})

const emit = defineEmits(['update:open', 'confirm', 'cancel'])

const handleConfirm = () => {
    emit('confirm')
    emit('update:open', false)
}

const handleCancel = () => {
    emit('cancel')
    emit('update:open', false)
}
</script>

<template>
    <AlertDialog :open="open" @update:open="emit('update:open', $event)">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ title }}</AlertDialogTitle>
                <AlertDialogDescription>
                    {{ description }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="handleCancel">{{ cancelText }}</AlertDialogCancel>
                <AlertDialogAction @click="handleConfirm"
                    :class="{ 'bg-destructive text-destructive-foreground hover:bg-destructive/90': variant === 'destructive' }">
                    <span v-if="loading">Wait...</span>
                    <span v-else>{{ confirmText }}</span>
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>