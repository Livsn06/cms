<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { EllipsisVertical, ListCollapse, Pencil, Trash, Search, Plus, Image } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input'; // Ensure you have an Input component
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuShortcut,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
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
import { packages } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import { create, destroy, edit, show } from '@/routes/admin/packages';
import CustomConfirmModal from '@/components/CustomConfirmModal.vue';
import CustomToast from '@/components/CustomToast.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Packages',
        href: packages(),
    },
];

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
    packagesData: Package[];
}>();

// --- State ---
const currentPage = ref(1)
const itemsPerPage = 10
const searchQuery = ref('')

// --- Search Logic ---
const filteredPackages = computed(() => {
    if (!searchQuery.value) return props.packagesData;

    const query = searchQuery.value.toLowerCase();
    return props.packagesData.filter(pkg =>
        pkg.name.toLowerCase().includes(query) ||
        pkg.description.toLowerCase().includes(query) ||
        pkg.id.toString().includes(query)
    );
});

// Reset page to 1 when searching
watch(searchQuery, () => {
    currentPage.value = 1;
});

// --- Pagination Logic (Slices the FILTERED results) ---
const paginatedPackages = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return filteredPackages.value.slice(start, end)
});

const totalPages = computed(() => Math.ceil(filteredPackages.value.length / itemsPerPage));



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
    if (!selectedPackageId.value) return;

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


const viewPackage = (id: number) => {
    router.get(show(id));
};

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Packages" />

        <div class="p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <Heading title="Service Packages" description="Manage, create and view all your packages" />
                <div class="flex gap-4">
                    <div class="relative w-full md:w-72">
                        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input v-model="searchQuery" placeholder="Search packages..." class="pl-9" />
                    </div>
                    <Link :href="create()">
                        <Button>
                            <Plus class="mr-1 h-4 w-4" />
                            New Package
                        </Button>
                    </Link>
                </div>

            </div>

            <div class="w-full rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]">ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Price</TableHead>
                            <TableHead class="text-right">Date Created</TableHead>
                            <TableHead class="w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow v-for="pkg in paginatedPackages" :key="pkg.id">
                            <TableCell class="font-medium">#{{ pkg.id }}</TableCell>
                            <TableCell>
                                <div class="flex items-center">
                                    <div class="mr-4">
                                        <img v-if="pkg.image" :src="pkg.image" alt="Package Image"
                                            class="size-8 rounded-md" />
                                        <div v-else
                                            class="size-8 rounded-md flex items-center justify-center bg-muted-foreground">
                                            <Image class="size-6" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ pkg.name }}</div>
                                        <div class="text-xs text-muted-foreground line-clamp-1">{{ pkg.description }}
                                        </div>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>{{ formatPriceWithCurrency(pkg.price) }}</TableCell>
                            <TableCell class="text-right">{{ formatDate(pkg.created_at) }}</TableCell>

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
                                            <DropdownMenuItem @click="viewPackage(pkg.id)">
                                                <ListCollapse class="mr-2 h-4 w-4" />
                                                <span>View Details</span>
                                                <DropdownMenuShortcut>⇧⌘P</DropdownMenuShortcut>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem @click="editPackage(pkg.id)">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                <span>Edit</span>
                                                <DropdownMenuShortcut>⌘B</DropdownMenuShortcut>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem @click="promptDelete(pkg.id)"
                                                class="text-red-600 focus:text-red-600">
                                                <Trash class="mr-2 h-4 w-4" />
                                                <span>Delete</span>
                                                <DropdownMenuShortcut>⌘S</DropdownMenuShortcut>
                                            </DropdownMenuItem>
                                        </DropdownMenuGroup>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="paginatedPackages.length === 0">
                            <TableCell colspan="5" class="h-24 text-center">
                                No packages found.
                            </TableCell>
                        </TableRow>
                    </TableBody>

                    <TableFooter v-if="filteredPackages.length > 0">
                        <TableRow>
                            <TableCell colspan="5">
                                <div class="flex items-center justify-between px-2 py-2">
                                    <span class="text-sm text-muted-foreground">
                                        Showing {{ paginatedPackages.length }} of {{ filteredPackages.length }} results
                                        (Page {{ currentPage }} of {{ totalPages }})
                                    </span>

                                    <Pagination v-model:page="currentPage" :total="filteredPackages.length"
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



        <CustomConfirmModal v-model:open="isDeleteModalOpen" :loading="isDeleting" title="Delete Package?"
            description="This will permanently delete the service package. This action cannot be undone."
            confirmText="Delete" variant="destructive" @confirm="confirmDelete" />
    </AppLayout>
</template>