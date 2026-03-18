<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { EllipsisVertical, ListCollapse, Pencil, Trash, Search, Image, Calendar, Users, Phone, Plus } from 'lucide-vue-next';
import { Copy, Check } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';



import CustomConfirmModal from '@/components/CustomConfirmModal.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
    PaginationEllipsis
} from '@/components/ui/pagination'
import {
    Table,
    TableBody,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDate, formatPriceWithCurrency } from '@/lib/formatters';
import type { BreadcrumbItem } from '@/types';
import { create, destroy } from '@/routes/admin/bookings';
import { confirm } from '@/routes/two-factor';

// Updated breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bookings',
        href: '#', // Replace with your booking index route
    },
];

interface Package {
    id: number;
    name: string;
    description: string;
    price: number;
    image: string | null;
}

interface Booking {
    id: number;
    package: Package;
    phone: string;
    event_date: string;
    guest_count: number;
    total_price: number;
    status: string;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    bookingData: Booking[];
}>();

// --- State ---
const currentPage = ref(1)
const itemsPerPage = 10
const searchQuery = ref('')

// --- Search Logic ---
const filteredBookings = computed(() => {
    if (!searchQuery.value) {
        return props.bookingData;
    }

    const query = searchQuery.value.toLowerCase();

    return props.bookingData.filter(bkng =>
        bkng.package.name.toLowerCase().includes(query) ||
        bkng.phone.toLowerCase().includes(query) ||
        bkng.status.toLowerCase().includes(query) ||
        bkng.event_date.toLowerCase().includes(query) ||
        bkng.guest_count.toString().includes(query) ||
        bkng.id.toString().includes(query)
    );
});

watch(searchQuery, () => {
    currentPage.value = 1;
});

// --- Pagination Logic ---
const paginatedBookings = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage

    return filteredBookings.value.slice(start, end)
});

// const totalPages = computed(() => Math.ceil(filteredBookings.value.length / itemsPerPage));

// --- Actions ---
const isDeleteModalOpen = ref(false)
const selectedBookingId = ref<number | null>(null)

const promptDelete = (id: number) => {
    selectedBookingId.value = id
    isDeleteModalOpen.value = true
}

const isDeleting = ref(false);

const confirmDelete = () => {
    if (!selectedBookingId.value) {
        return;
    }

    // Replace with your actual delete route
    router.delete(destroy(selectedBookingId.value), {
        onStart: () => { isDeleting.value = true; },
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            selectedBookingId.value = null;
        },
        onFinish: () => { isDeleting.value = false; },
        preserveScroll: true,
    });
};

const editBooking = (id: number) => {
    // router.get(route('admin.bookings.edit', id));
    console.log(id);
};

const viewBooking = (id: number) => {
    // router.get(route('admin.bookings.show', id));
    console.log(id);
};

// Helper for status badge colors
const getStatusVariant = (status: string) => {
    switch (status.toLowerCase()) {
        case 'pending': return 'outline';
        case 'confirmed': return 'default';
        case 'completed': return 'secondary';
        case 'cancelled': return 'destructive';
        default: return 'outline';
    }
}





const copiedId = ref<number | null>(null);

const copyToClipboard = (text: string, id: number) => {
    navigator.clipboard.writeText(text);
    copiedId.value = id;
    setTimeout(() => {
        copiedId.value = null;
    }, 2000);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Bookings" />

        <div class="p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <Heading title="Event Bookings" description="Manage and track all catering reservations" />
                <div class="flex gap-4">
                    <div class="relative w-full md:w-72">
                        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input v-model="searchQuery" placeholder="Search bookings..." class="pl-9" />
                    </div>
                    <Link :href="create()">
                        <Button>
                            <Plus class="mr-1 h-4 w-4" />
                            New Booking
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="w-full rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[80px]">ID</TableHead>
                            <TableHead>Package & Client</TableHead>
                            <TableHead>Event Details</TableHead>
                            <TableHead>Total Price</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Requested On</TableHead>
                            <TableHead class="w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="booking in paginatedBookings" :key="booking.id">
                            <TableCell class="font-medium text-muted-foreground">#{{ booking.id }}</TableCell>
                            <TableCell>
                                <div class="flex items-center">
                                    <div class="mr-4 shrink-0">
                                        <img v-if="booking.package.image" :src="booking.package.image"
                                            alt="Package Image" class="size-8 rounded-md object-cover" />
                                        <div v-else
                                            class="size-8 rounded-md flex items-center justify-center bg-muted-foreground">
                                            <Image class="size-6 text-white" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 max-w-[200px] md:max-w-[300px] lg:max-w-[400px]">
                                        <div class="font-bold text-slate-200 truncate">{{ booking.package.name }}</div>
                                        <div class="text-xs text-muted-foreground flex items-center gap-1 group">
                                            <Phone class="size-3" />
                                            <span class="font-medium">{{ booking.phone }}</span>

                                            <button @click="copyToClipboard(booking.phone, booking.id)"
                                                class="ml-1 p-1 rounded-md hover:bg-slate-200 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100"
                                                title="Copy Phone Number">
                                                <component :is="copiedId === booking.id ? Check : Copy"
                                                    :class="['size-3 transition-all', copiedId === booking.id ? 'text-green-600' : 'text-slate-400']" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <div class="space-y-1">
                                    <div class="text-sm font-medium flex items-center gap-1.5">
                                        <Calendar class="size-3.5 text-orange-500" />
                                        {{ formatDate(booking.event_date) }}
                                    </div>
                                    <div class="text-xs text-muted-foreground flex items-center gap-1.5">
                                        <Users class="size-3.5" />
                                        {{ booking.guest_count }} Guests
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                {{ formatPriceWithCurrency(booking.total_price) }}
                            </TableCell>
                            <TableCell>
                                <Badge :variant="getStatusVariant(booking.status)" class="capitalize">
                                    {{ booking.status }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right text-xs text-muted-foreground">
                                {{ formatDate(booking.created_at) }}
                            </TableCell>

                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button class="w-8 h-8 p-0" variant="ghost">
                                            <EllipsisVertical class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="w-56">
                                        <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuGroup>
                                            <DropdownMenuItem @click="viewBooking(booking.id)">
                                                <ListCollapse class="mr-2 h-4 w-4" />
                                                <span>View Details</span>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem @click="editBooking(booking.id)">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                <span>Update Details</span>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                v-if="booking.status === 'cancelled' || booking.status === 'completed'"
                                                @click="promptDelete(booking.id)"
                                                class="text-red-600 focus:text-red-600">
                                                <Trash class="mr-2 h-4 w-4" />
                                                <span>Delete Booking</span>
                                            </DropdownMenuItem>
                                        </DropdownMenuGroup>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="paginatedBookings.length === 0">
                            <TableCell colspan="6" class="h-24 text-center text-muted-foreground">
                                No bookings found.
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter v-if="filteredBookings.length > 0">
                        <TableRow>
                            <TableCell colspan="7">
                                <div class="flex items-center justify-between px-2 py-2">
                                    <span class="text-sm text-muted-foreground">
                                        Showing {{ paginatedBookings.length }} of {{ filteredBookings.length }} bookings
                                    </span>

                                    <Pagination v-model:page="currentPage" :total="filteredBookings.length"
                                        :items-per-page="itemsPerPage" :sibling-count="1" show-edges>
                                        <PaginationContent v-slot="{ items }">
                                            <PaginationPrevious />
                                            <template v-for="(item, index) in items">
                                                <PaginationItem v-if="item.type === 'page'" :key="index"
                                                    :value="item.value" as-child>
                                                    <Button class="w-9 h-9"
                                                        :variant="item.value === currentPage ? 'default' : 'outline'"
                                                        size="icon">
                                                        {{ item.value }}
                                                    </Button>
                                                </PaginationItem>
                                                <PaginationEllipsis v-else :key="item.type" :index="index" />
                                            </template>
                                            <PaginationNext />
                                        </PaginationContent>
                                    </Pagination>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableFooter>
                </Table>
            </div>
        </div>

        <CustomConfirmModal v-model:open="isDeleteModalOpen" :loading="isDeleting" title="Delete Booking?"
            description="This will permanently delete the booking record. This action cannot be undone."
            confirmText="Delete" variant="destructive" @confirm="confirmDelete" />
    </AppLayout>
</template>