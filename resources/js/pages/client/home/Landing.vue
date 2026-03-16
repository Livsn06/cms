<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2 } from "lucide-vue-next";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardFooter } from "@/components/ui/card";
import ClientLayout from '@/layouts/ClientLayout.vue';
import { formatPriceWithCurrency } from '@/lib/formatters';
import { booking } from '@/routes';

interface Package {
    id: number;
    name: string;
    description: string;
    price: number;
    image: string | null;
    created_at?: string;
    updated_at?: string;
}

// Define props with TypeScript
defineProps<{
    packages: Package[];
}>();

const scrollToPackages = (): void => {
    const element = document.getElementById('packages');
    element?.scrollIntoView({ behavior: 'smooth' });
};


</script>

<template>
    <ClientLayout>

        <Head title="Modern Catering | Premium Events" />

        <div class="min-h-screen bg-white">
            <header class="relative h-[90vh] flex items-center bg-slate-900">
                <div class="absolute inset-0 opacity-50">
                    <img src="https://images.unsplash.com/photo-1555244162-803834f70033?auto=format&fit=crop&q=80&w=2000"
                        alt="Catering Backdrop" class="w-full h-full object-cover" />
                </div>

                <div class="container relative z-10 mx-auto px-6">
                    <div class="max-w-3xl">
                        <Badge variant="secondary" class="mb-6 bg-orange-500 text-white border-none px-4 py-1">
                            Professional Catering Services
                        </Badge>
                        <h1 class="text-6xl md:text-8xl font-black text-white mb-8 tracking-tighter">
                            EVENTS <br /> <span class="text-orange-500 text-outline">REDEFINED.</span>
                        </h1>
                        <p class="text-xl text-slate-300 mb-10 max-w-xl leading-relaxed">
                            From high-stakes corporate galas to intimate garden weddings. We bring the kitchen, the
                            talent,
                            and the magic.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <Button @click="scrollToPackages" size="lg"
                                class="bg-orange-600 hover:bg-orange-700 h-14 px-8 text-lg">
                                View Packages
                            </Button>
                            <Button variant="outline" size="lg"
                                class="text-white border-white/20 hover:bg-white/10 h-14 px-8 text-lg backdrop-blur-sm">
                                Our Process
                            </Button>
                        </div>
                    </div>
                </div>
            </header>

            <section class="py-32 container mx-auto px-6 grid lg:grid-cols-2 gap-20 items-center">
                <div class="grid grid-cols-2 gap-4">
                    <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&q=80&w=800"
                        class="rounded-2xl mt-12 shadow-lg" alt="Chef" />
                    <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&q=80&w=800"
                        class="rounded-2xl shadow-lg" alt="Fine dining" />
                </div>
                <div class="space-y-8">
                    <h2 class="text-4xl font-bold text-slate-900">Why Choose Our Catering?</h2>
                    <p class="text-lg text-slate-600 leading-relaxed">
                        We believe every event tells a story. Since 2012, we've helped over 500 clients tell theirs
                        through
                        bespoke menus and flawless execution.
                    </p>
                    <div class="space-y-4">
                        <div v-for="feat in ['Farm-to-Table Sourcing', 'Custom Mixology Bars', 'Certified Executive Chefs']"
                            :key="feat" class="flex items-center gap-3">
                            <div
                                class="h-6 w-6 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                                <CheckCircle2 />
                            </div>
                            <span class="font-semibold text-slate-800">{{ feat }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-orange-600 py-16">
                <div class="container mx-auto px-6 flex flex-wrap justify-around gap-12 text-white text-center">
                    <div v-for="stat in [{ v: '250+', l: 'Weddings' }, { v: '50+', l: 'Corporate Partners' }, { v: '15', l: 'Award Titles' }]"
                        :key="stat.l">
                        <p class="text-5xl font-black mb-2">{{ stat.v }}</p>
                        <p class="text-orange-200 uppercase tracking-widest text-sm font-bold">{{ stat.l }}</p>
                    </div>
                </div>
            </section>

            <section id="packages" class="py-32 bg-slate-50">
                <div class="container mx-auto px-6">
                    <div class="text-center mb-20">
                        <h2 class="text-5xl font-extrabold text-slate-900 mb-6">Service Packages</h2>
                        <div class="h-1 w-20 bg-orange-600 mx-auto"></div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-10">
                        <Card v-for="pkg in packages" :key="pkg.id"
                            class="border-none shadow-none bg-white group overflow-hidden hover:ring-2 hover:ring-orange-500 transition-all duration-300">
                            <div class="h-60 relative overflow-hidden">
                                <img :src="pkg.image ?? 'https://via.placeholder.com/800x600?text=Premium+Catering'"
                                    :alt="pkg.name"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 text-white">
                                    <p class="text-2xl font-bold">{{ formatPriceWithCurrency(pkg.price) }} <span
                                            class="text-sm font-normal text-slate-200">/ guest</span></p>
                                </div>
                            </div>
                            <CardHeader class="pt-8">
                                <CardTitle class="text-2xl font-bold text-slate-900 mb-2">{{ pkg.name }}</CardTitle>
                                <CardDescription class="text-slate-500 text-md leading-relaxed  whitespace-pre-line ">
                                    {{ pkg.description }}
                                </CardDescription>
                            </CardHeader>
                            <CardFooter class="pb-8">
                                <Link :href="booking(pkg.id)" class="w-full block">
                                    <Button
                                        class="w-full bg-slate-900 hover:bg-orange-600 text-white transition-colors py-6">
                                        Book Now
                                    </Button>
                                </Link>
                            </CardFooter>
                        </Card>
                    </div>
                </div>
            </section>
        </div>
    </ClientLayout>
</template>

<style scoped>
.text-outline {
    -webkit-text-stroke: 1px #f97316;
    color: transparent;
}
</style>