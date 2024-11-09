<script setup>
import UserPng from "@/assets/images/user.png";

const props = defineProps({
    avatar: {
        type: String,
        default: UserPng
    },
    name: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: 'sm'
    },
    online: {
        type: Boolean,
        default: false
    },
});
</script>

<template>
    <div class="flex space-x-1 bg-gray-100 p-3 w-max rounded-lg">
        <div class="w-2 h-2 bg-gray-800 rounded-full animate-bounce dot1"></div>
        <div class="w-2 h-2 bg-gray-800 rounded-full animate-bounce dot2"></div>
        <div class="w-2 h-2 bg-gray-800 rounded-full animate-bounce dot3"></div>
    </div>
</template>

<style scoped>

.dot1 {
    animation-delay: 0s;
}
.dot2 {
    animation-delay: 0.4s;
}
.dot3 {
    animation-delay: 0.6s;
}

</style>
