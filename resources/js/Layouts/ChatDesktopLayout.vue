<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import {ref} from "vue";

import ConversationList from '@/Components/Chat/ConversationList.vue';
import ChatWindow from '@/Components/Chat/ChatWindow.vue';

const selectedConversation = ref(null);

// Dummy current user data
const currentUser = ref({
    name: 'John Doe',
    email: 'john.doe@example.com',
    avatar: 'https://randomuser.me/api/portraits/men/1.jpg', // Placeholder avatar
});

// Method to select a conversation
const conversationSelected = (conversation) => {
    selectedConversation.value = conversation;
};

// Method to send a message
const sendMessage = (message) => {
    if (selectedConversation.value) {
        selectedConversation.value.messages.push({
            id: Date.now(),
            content: message,
            isMine: true,
        });
    }
};

</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-screen">
        <!-- Sidebar: ConversationRequest List -->
        <ConversationList
            :conversations="$page.props.conversations"
            :currentUser="currentUser"
            @conversationSelected="conversationSelected"
        />

        <!-- Main Area: Chat Window -->
        <div class="flex-1">
            <ChatWindow
                v-if="$page.props.conversation"
                :selectedConversation="$page.props.conversation"
                :createdBy="$page.props.currentUser"
                @sendMessage="sendMessage"
            />
        </div>
    </div>
</template>
<style scoped>
/* Main Chat Container Styling */
.flex-1 {
    flex-grow: 1;
    overflow-y: hidden;
}
</style>
