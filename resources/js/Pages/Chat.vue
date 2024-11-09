<script setup>
import { initFlowbite,initDropdowns } from 'flowbite'
import ChatMobileLayout from "@/Layouts/ChatMobileLayout.vue"
import ChatDesktopLayout from "@/Layouts/ChatDesktopLayout.vue";
import {onBeforeUnmount, onMounted, ref} from "vue";
import ChatWindow from "@/Components/Chat/ChatWindow.vue";
const isMobile = ref(window.innerWidth <= 768);
const currentLayout = ref(isMobile.value ? ChatMobileLayout : ChatDesktopLayout);

const handleResize = () => {
    isMobile.value = window.innerWidth <= 768;
    currentLayout.value = isMobile.value ? ChatMobileLayout : ChatDesktopLayout;
};

onMounted(() => {
    initFlowbite();
    initDropdowns()
    window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});

const selectedConversation = ref(null);

// Method to select a conversation
const conversationSelected = (conversation) => {
    selectedConversation.value = conversation;
};

</script>

<template>
    <component :is="currentLayout">
        <!-- Main Area: Chat Window -->
        <div class="h-screen flex flex-col">
            <ChatWindow
                v-if="$page.props.conversation"
                :selectedConversation="$page.props.conversation"
                :createdBy="$page.props.auth.user"
            />
        </div>
        </component>
</template>

<style scoped>

</style>
