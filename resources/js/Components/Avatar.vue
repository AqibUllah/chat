<script setup>
import UserPng from "@/assets/images/user.png";
import {computed} from "vue";

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

const avatarSize = computed(() => {
    switch (props.size) {
        case "sm":
            return "w-8 h-8"; // Small size
        case "md":
            return "w-12 h-12"; // Medium size
        case "lg":
            return "w-16 h-auto"; // Large size
        case "xl":
            return "w-20 h-20"; // Extra large size
        default:
            return "w-12 h-12"; // Default to medium
    }
})
</script>

<template>
    <div class="relative">
        <img class="border-2 border-white rounded-full dark:border-gray-800" :src="avatar ? avatar : UserPng"
             :title="name" :class="avatarSize">
        <span v-if="online" class="bg-green-500 absolute bottom-[2px] left-[3px] h-2 w-2 rounded-full"></span>
    </div>
</template>
