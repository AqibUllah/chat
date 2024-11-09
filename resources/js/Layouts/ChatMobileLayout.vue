<script setup>
import { Head } from '@inertiajs/vue3';


import BottomTabs from '@/Components/BottomTabs.vue';


</script>

<template>
    <Head title="Dashboard" />

    <div class="h-screen flex flex-col">
        <!-- Main content area -->
        <slot class="flex-1 overflow-y-auto p-4" />

        <!-- Bottom Navigation -->
<!--        <BottomTabs />-->
    </div>
</template>
<style scoped>
/* Main Chat Container Styling */
.flex-1 {
    flex-grow: 1;
    overflow-y: hidden;
}
</style>
