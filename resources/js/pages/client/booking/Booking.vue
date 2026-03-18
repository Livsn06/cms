<script setup lang="ts">
import { useForm, Head, Link } from '@inertiajs/vue3';
import { ChevronRight, UtensilsCrossed, Users, Calendar, Phone } from "lucide-vue-next";
import { computed } from 'vue';
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { formatPriceWithCurrency } from '@/lib/formatters';
import { store } from '@/routes/booking';

// Types matching your Schema
interface Package {
    id: number;
    name: string;
    description: string;
    price: number;
    image: string | null;
};


interface Booking {
    package_id: number;
    phone: string;
    event_date: string;
    guest_count: number;
    total_price: number;
}

const props = defineProps<{
    package: Package;
}>();

const form = useForm<Booking>({
    package_id: props.package.id,
    phone: '',
    event_date: '',
    guest_count: 20,
    total_price: 0
});

// Calculate total dynamically
const estimatedTotal = computed(() => {
    return form.guest_count * props.package.price;
});




const submit = () => {
    const routeData = store();
    form.total_price = estimatedTotal.value;

    form.post(routeData.url, {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Book Your Event" />

    <div class="min-h-screen bg-slate-50 pb-20">
        <div class="bg-white border-b mb-12">
            <div class="container mx-auto px-6 py-8">
                <nav class="flex items-center gap-2 text-sm text-slate-500 mb-4">
                    <Link href="/" class="hover:text-orange-600 transition-colors">Landing</Link>
                    <ChevronRight class="w-4 h-4" />
                    <span class="text-slate-900 font-medium">Booking</span>
                </nav>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 uppercase">
                    SECURE YOUR <span class="text-outline">DATE</span>
                </h1>
            </div>
        </div>

        <div class="container mx-auto px-6 grid lg:grid-cols-12 gap-12">
            <div class="lg:col-span-7 space-y-6">

                <Card class="border-none shadow-md overflow-hidden bg-white">
                    <div class="flex flex-col sm:flex-row">
                        <div class="sm:w-48 h-32 sm:h-auto overflow-hidden">
                            <img :src="package.image ?? '/api/placeholder/400/300'" :alt="package.name"
                                class="w-full h-full object-cover" />
                        </div>
                        <div class="p-6 flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs font-bold text-orange-600 uppercase tracking-widest mb-1">Selected
                                        Package</p>
                                    <h2 class="text-2xl font-bold text-slate-900">{{ package.name }}</h2>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black text-slate-900">{{
                                        formatPriceWithCurrency(package.price) }}</p>
                                    <p class="text-xs text-slate-500">per guest</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>

                <Card class="border-none shadow-xl shadow-slate-200/60">
                    <CardHeader class="pb-8 border-b mb-6">
                        <CardTitle class="flex items-center gap-2">
                            <UtensilsCrossed class="text-orange-600 w-5 h-5" />
                            Reservation Details
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-8">

                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label class="flex items-center gap-2 mb-2">
                                        <Calendar class="w-4 h-4 text-slate-400" /> Event Date
                                    </Label>
                                    <Input type="date" v-model="form.event_date"
                                        :class="{ 'border-red-500 shadow-sm': form.errors.event_date }" />
                                    <p v-if="form.errors.event_date" class="text-xs text-red-500 mt-1">{{
                                        form.errors.event_date }}</p>
                                </div>

                                <div class="space-y-2">
                                    <Label class="flex items-center gap-2 mb-2">
                                        <Phone class="w-4 h-4 text-slate-400" /> Phone Number
                                    </Label>
                                    <Input type="tel" placeholder="0912 345 6789" v-model="form.phone"
                                        :class="{ 'border-red-500 shadow-sm': form.errors.phone }" />
                                    <p v-if="form.errors.phone" class="text-xs text-red-500 mt-1">{{ form.errors.phone
                                        }}</p>
                                </div>
                            </div>

                            <div class="space-y-6 pt-4">
                                <div class="flex justify-between items-end">
                                    <Label class="flex items-center gap-2">
                                        <Users class="w-4 h-4 text-slate-400" /> Expected Guests
                                    </Label>
                                    <span class="text-3xl font-black text-orange-600 leading-none">
                                        {{ form.guest_count }}
                                    </span>
                                </div>
                                <input type="range" min="10" max="500" v-model="form.guest_count"
                                    class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-orange-600" />
                                <div
                                    class="flex justify-between text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                                    <span>Min: 10</span>
                                    <span>Max: 500</span>
                                </div>
                                <p v-if="form.errors.guest_count" class="text-xs text-red-500 mt-1">{{
                                    form.errors.guest_count }}</p>
                            </div>

                            <Button type="submit"
                                class="w-full bg-orange-900 text-white hover:bg-orange-600 h-16 text-lg font-bold transition-all shadow-lg hover:shadow-orange-200"
                                :disabled="form.processing">
                                <span v-if="!form.processing">Confirm Reservation Request</span>
                                <span v-else class="flex items-center gap-2">
                                    <span class="animate-spin text-xl">◌</span> Processing...
                                </span>
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <Card class="bg-slate-900 text-white border-none shadow-2xl p-8 rounded-3xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <UtensilsCrossed :size="120" />
                    </div>
                    <h3 class="text-xl font-bold mb-6 text-orange-500">Cost Estimate</h3>
                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between text-slate-400 text-sm">
                            <span>{{ package.name }} (x{{ form.guest_count }})</span>
                            <span>{{ formatPriceWithCurrency(estimatedTotal) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400 text-sm">
                            <span>Service Fee</span>
                            <span>Included</span>
                        </div>
                        <div class="border-t border-slate-700 pt-4 mt-4 flex justify-between items-end">
                            <span class="font-bold text-lg">Total Estimate</span>
                            <span class="text-3xl font-black text-white">{{ formatPriceWithCurrency(estimatedTotal)
                            }}</span>
                        </div>
                    </div>
                </Card>

                <div class="p-8 border-2 border-dashed border-slate-200 rounded-3xl bg-white/50">
                    <p class="text-slate-500 italic text-sm leading-relaxed">
                        <strong>Important:</strong> This is a booking request. Our team will review your date and guest
                        count before changing the status from
                        <span class="text-orange-600 font-bold underline decoration-2 underline-offset-4">Pending</span>
                        to Confirmed.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.text-outline {
    -webkit-text-stroke: 1.5px #f97316;
    color: transparent;
}

input[type='range']::-webkit-slider-thumb {
    appearance: none;
    height: 24px;
    width: 24px;
    border-radius: 50%;
    background: #f97316;
    cursor: pointer;
    border: 4px solid white;
    box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3);
}

input[type='range']::-moz-range-thumb {
    height: 24px;
    width: 24px;
    border-radius: 50%;
    background: #f97316;
    cursor: pointer;
    border: 4px solid white;
    box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3);
}
</style>