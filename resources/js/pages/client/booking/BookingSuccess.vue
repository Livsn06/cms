<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, Users, Phone, ArrowLeft, Download, PartyPopper } from "lucide-vue-next";
import { computed } from 'vue';
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardFooter } from "@/components/ui/card";
import { formatPriceWithCurrency } from '@/lib/formatters';

interface Package {
    id: number;
    name: string;
    price: number;
    image: string | null;
    description: string;
}

interface Booking {
    id: number;
    phone: string;
    event_date: string;
    guest_count: number;
    total_price: number;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    package: Package;
}

const props = defineProps<{
    booking: Booking;
}>();



const formattedDate = computed(() => {
    return new Date(props.booking.event_date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});



const handlePrint = () => {
    window.print();
};
</script>

<template>

    <Head title="Booking Successful" />

    <div class="min-h-screen bg-slate-50 flex flex-col items-center justify-center p-6">

        <div class="mb-8 relative">
            <div class="absolute inset-0 animate-ping bg-orange-200 rounded-full scale-150 opacity-20"></div>
            <div class="bg-orange-600 p-6 rounded-full relative z-10 shadow-xl shadow-orange-200">
                <PartyPopper class="w-12 h-12 text-white" />
            </div>
        </div>

        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 uppercase">
                REQUEST <span class="text-outline">RECEIVED!</span>
            </h1>
            <p class="text-slate-500 max-w-md mx-auto">
                We've received your catering request. Our event coordinator will review the details and contact you
                shortly.
            </p>
        </div>

        <div class="w-full max-w-2xl">
            <Card class="border-none shadow-2xl shadow-slate-200 overflow-hidden">
                <div class="bg-slate-900 px-6 py-3 flex justify-between items-center">
                    <span class="text-slate-400 text-xs font-bold tracking-widest uppercase">Booking Reference: #{{
                        booking.id }}</span>
                    <Badge class="bg-orange-500 hover:bg-orange-500 text-white uppercase px-3">
                        {{ booking.status }}
                    </Badge>
                </div>

                <CardContent class="p-0">
                    <div class="p-8 border-b bg-white flex items-start gap-6">
                        <img :src="booking.package.image ?? '/api/placeholder/400/300'"
                            class="w-24 h-24 rounded-xl object-cover shadow-md" />
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-slate-900">{{ booking.package.name }}</h3>
                            <p class="text-slate-500 text-sm line-clamp-2 mt-1 italic">{{ booking.package.description }}
                            </p>
                            <p class="mt-2 font-bold text-orange-600">{{ formatPriceWithCurrency(booking.package.price)
                                }}
                                / guest</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-0 bg-slate-50/60">
                        <div
                            class="p-6 border-r border-b md:border-b-0 flex flex-col items-center text-center space-y-2">
                            <Calendar class="w-5 h-5 text-slate-200" />
                            <span class="text-[10px] uppercase font-bold text-slate-200 tracking-tighter">Event
                                Date</span>
                            <span class="text-sm font-semibold text-slate-800">{{ formattedDate }}</span>
                        </div>
                        <div
                            class="p-6 border-r border-b md:border-b-0 flex flex-col items-center text-center space-y-2">
                            <Users class="w-5 h-5 text-slate-200" />
                            <span class="text-[10px] uppercase font-bold text-slate-200 tracking-tighter">Guest
                                Count</span>
                            <span class="text-sm font-semibold text-slate-800">{{ booking.guest_count }} People</span>
                        </div>
                        <div class="p-6 flex flex-col items-center text-center space-y-2">
                            <Phone class="w-5 h-5 text-slate-200" />
                            <span class="text-[10px] uppercase font-bold text-slate-200 tracking-tighter">Contact</span>
                            <span class="text-sm font-semibold text-slate-800">{{ booking.phone }}</span>
                        </div>
                    </div>

                    <div class="p-8 bg-white border-t flex justify-between items-center">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Estimated Total</p>
                            <p class="text-xs text-slate-400">Final price subject to menu changes</p>
                        </div>
                        <div class="text-3xl font-black text-slate-900">
                            {{ formatPriceWithCurrency(booking.total_price) }}
                        </div>
                    </div>
                </CardContent>

                <CardFooter class="bg-slate-50 p-6 flex flex-col sm:flex-row gap-4">
                    <Button variant="outline" class="flex-1 h-12 border-slate-200 hover:text-orange-600 text-slate-600"
                        @click="handlePrint">
                        <Download class="w-4 h-4 mr-2" /> Save as PDF
                    </Button>
                    <Link href="/" class="flex-1">
                        <Button class="w-full h-12 bg-slate-900 hover:bg-orange-600 text-white transition-colors">
                            <ArrowLeft class="w-4 h-4 mr-2" /> Back to Home
                        </Button>
                    </Link>
                </CardFooter>
            </Card>
        </div>

        <p class="mt-8 text-slate-400 text-sm"><span class="font-bold">Note:</span> We will contact you via phone number
            you provided to confirm your
            request. Thank you!</p>
    </div>
</template>

<style scoped>
.text-outline {
    -webkit-text-stroke: 1.5px #f97316;
    color: transparent;
}

@media print {

    .bg-slate-50,
    .bg-slate-900,
    .bg-orange-600 {
        -webkit-print-color-adjust: exact;
    }

    .flex-1,
    button,
    a {
        display: none !important;
    }
}
</style>