<script setup>
import ConversationList from '@/Components/Chat/ConversationList.vue';
import { initFlowbite,initDropdowns } from 'flowbite'
import ChatMobileLayout from "@/Layouts/ChatMobileLayout.vue"
import ChatDesktopLayout from "@/Layouts/ChatDesktopLayout.vue";
import {onBeforeUnmount, onMounted, ref} from "vue";
import BottomTabs from "@/Components/BottomTabs.vue";
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
            <ConversationList
                :w_full="true"
                v-if="isMobile"
                :conversations="$page.props.conversations"
                :currentUser="$page.props.currentUser"
                @conversationSelected="conversationSelected"
            />
            <slot />
                <BottomTabs />
        </component>
</template>

<style scoped>

</style>
