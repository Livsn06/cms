<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { packages } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
    PaginationEllipsis
} from '@/components/ui/pagination'


import { DropdownMenu, DropdownMenuContent, DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuShortcut, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { EllipsisVertical, ListCollapse, Pencil, Trash } from 'lucide-vue-next';
import { ref } from 'vue';
import { computed } from 'vue';
import { formatDate, formatPriceWithCurrency } from '@/lib/formatters';

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
    created_at: string;
    updated_at: string;
}


const props = defineProps<{
    packages: Package[];
}>();


// --- Pagination State ---
const currentPage = ref(1)
const itemsPerPage = 10

// --- Logic to "Slice" the Table ---
const paginatedPackages = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return props.packages.slice(start, end)
})



</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Dashboard" />

        <div class="p-8">
            <Heading title="Service Packages" description="Manage, create and view all your packages" />
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
                                <div class="font-medium">{{ pkg.name }}</div>
                                <div class="text-xs text-muted-foreground line-clamp-1">{{ pkg.description }}</div>
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
                                            <DropdownMenuItem>
                                                <ListCollapse class="mr-2 h-4 w-4" />
                                                <span>View Details</span>
                                                <DropdownMenuShortcut>⇧⌘P</DropdownMenuShortcut>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem>
                                                <Pencil class="mr-2 h-4 w-4" />
                                                <span>Edit</span>
                                                <DropdownMenuShortcut>⌘B</DropdownMenuShortcut>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="text-red-600 focus:text-red-600">
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

                    <TableFooter>
                        <TableRow>
                            <TableCell colspan="5">
                                <div class="flex items-center justify-between px-2 py-2">
                                    <span class="text-sm text-muted-foreground">
                                        Page {{ currentPage }} of {{ Math.ceil(props.packages.length / itemsPerPage) }}
                                    </span>

                                    <Pagination v-model:page="currentPage" :total="props.packages.length"
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
    </AppLayout>
</template>
