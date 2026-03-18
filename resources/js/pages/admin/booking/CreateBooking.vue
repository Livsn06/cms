<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Calendar, Phone, Users, DollarSign, CheckCircle } from 'lucide-vue-next';
import { computed, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatPriceWithCurrency } from '@/lib/formatters';
import { store } from '@/routes/admin/bookings';

// Define the Package type for the dropdown
interface Package {
    id: number;
    name: string;
    price: number;
}

const props = defineProps<{
    packages: Package[];
}>();

const breadcrumbItems = [
    { title: 'Bookings', href: '/admin/bookings' },
    { title: 'Create', href: '/admin/bookings/create' },
];

interface BookingForm {
    event_date: string;
    phone: string;
    guest_count: number;
    total_price: number;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    package_id: number | null;
}

const form = useForm<BookingForm>({
    event_date: '',
    phone: '',
    guest_count: 20,
    total_price: 0,
    status: 'pending',
    package_id: null,
});

// Calculate total price based on selected package and guest count
const calculatedTotal = computed(() => {
    const selectedPackage = props.packages.find(p => p.id === form.package_id);
    if (!selectedPackage) return 0;
    return selectedPackage.price * form.guest_count;
});

// Update form.total_price whenever the calculation changes
watch(calculatedTotal, (newTotal) => {
    form.total_price = newTotal;
});

const submit = () => {
    form.post(store().url, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Create Booking" />

        <div class="flex flex-col space-y-6 w-full max-w-2xl p-8 mx-auto">
            <Heading variant="small" title="New Reservation" description="Manually add a booking to the system." />

            <form @submit.prevent="submit" class="space-y-6">

                <div class="grid gap-2">
                    <Label for="package" class="dark:text-zinc-300">Select Package</Label>
                    <select id="package" v-model="form.package_id"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-zinc-900 dark:border-zinc-700">
                        <option :value="null" disabled>Choose a catering package...</option>
                        <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
                            {{ pkg.name }} ({{ formatPriceWithCurrency(pkg.price) }} / guest)
                        </option>
                    </select>
                    <InputError :message="form.errors.package_id" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="event_date" class="dark:text-zinc-300">Event Date</Label>
                        <Input id="event_date" type="date" v-model="form.event_date" required />
                        <InputError :message="form.errors.event_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="phone" class="dark:text-zinc-300">Customer Phone</Label>
                        <Input id="phone" type="tel" v-model="form.phone" placeholder="+63xxx xxx xxxx" required />
                        <InputError :message="form.errors.phone" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="guest_count" class="dark:text-zinc-300">Guest Count</Label>
                        <Input id="guest_count" type="number" min="1" v-model="form.guest_count" required />
                        <InputError :message="form.errors.guest_count" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="status" class="dark:text-zinc-300">Booking Status</Label>
                        <select id="status" v-model="form.status"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm dark:bg-zinc-900 dark:border-zinc-700">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <InputError :message="form.errors.status" />
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-zinc-500">Calculated Total:</span>
                        <span class="text-2xl font-bold text-orange-600">
                            {{ formatPriceWithCurrency(calculatedTotal) }}
                        </span>
                    </div>
                    <input type="hidden" v-model="form.total_price" />
                </div>

                <div class="flex items-center gap-4 pt-4 border-t dark:border-zinc-800">
                    <Button type="submit" :disabled="form.processing || !form.package_id" class="w-full sm:w-auto">
                        <Spinner v-if="form.processing" class="mr-2" />
                        {{ form.processing ? 'Saving...' : 'Create Booking' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>