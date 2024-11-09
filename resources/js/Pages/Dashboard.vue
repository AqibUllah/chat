<script setup>
import ChatMobileLayout from "@/Layouts/ChatMobileLayout.vue"
import ChatDesktopLayout from "@/Layouts/ChatDesktopLayout.vue";
import {onBeforeUnmount, onMounted, ref} from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {useForm,Link} from "@inertiajs/vue3";
import UserPng from "@/assets/images/user.png";
const isMobile = ref(window.innerWidth <= 768);
const currentLayout = ref(isMobile.value ? ChatMobileLayout : ChatDesktopLayout);
import { FaBandsRocketchat } from '@kalimahapps/vue-icons';
import { FaRegTrashCan } from '@kalimahapps/vue-icons';
import { FaMartiniGlassEmpty } from '@kalimahapps/vue-icons';
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
const props = defineProps(['users','conversations','currentUser'])
const handleResize = () => {
    isMobile.value = window.innerWidth <= 768;
    currentLayout.value = isMobile.value ? ChatMobileLayout : ChatDesktopLayout;
};

onMounted(() => {
    window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});

const form = useForm({
    type: 'private',
    participant_ids: [],
})

const startConversation = (userId) => {
    form.participant_ids.push(userId)
    form.post('/conversations', {
        onSuccess:() => {

        },
        onFinish:() =>{

        }
    });
}

</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 space-y-8">
            <h1 class="text-3xl font-semibold mb-4">Dashboard</h1>

            <!-- Conversations Section -->
            <section class="min-h-[200px]">
                <h2 class="text-2xl font-semibold mb-4">Your Conversations</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-1" v-if="conversations.length">
                    <div v-for="conversation in conversations" :key="conversation.id" class="max-w-md w-full">
                        <div class="bg-white border rounded-lg shadow-md p-4">
                            <h3 class="font-bold text-lg mb-2">{{ conversation.name || 'Private Chat' }}</h3>
                            <ul class="flex justify-between items-center gap-2">
                                <li>
                                    <p class="text-sm text-gray-500">Participants: {{ conversation.participants.length }}</p>
                                    <div class="flex -space-x-4 rtl:space-x-reverse" >
                                        <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800"
                                             v-for="user in conversation.participants.slice(0,5)"
                                             :key="user.id" :src="user.avatar ? user.avatar : UserPng"
                                             :title="user.id === currentUser.id ? 'Me' : user.name">
                                        <a v-if="conversation.participants.length > 5" class="flex items-center justify-center w-10 h-10 text-xs font-medium text-white bg-gray-700 border-2 border-white rounded-full hover:bg-gray-600 dark:border-gray-800" href="#">+{{conversation.participants.length}}</a>
                                    </div>
                                </li>
                                <li>
                                    <Link :href="`/conversations/${conversation.id}`"
                                          method="delete" as="button"
                                    class="text-gray-50 font-bold bg-red-500 w-full text-xs rounded-lg p-2 flex gap-3 justify-between items-center mb-1">
                                        Delete Conversation
                                        <component class="text-sm" :is="FaRegTrashCan"></component>
                                    </Link>
                                    <Link :href="`/conversations/${conversation.id}`"
                                    class="text-gray-50 font-bold bg-green-500 w-full text-xs rounded-lg p-2 flex gap-3 justify-between items-center">
                                        Start Chat
                                        <component class="text-sm" :is="FaBandsRocketchat"></component>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div v-else class="flex justify-center flex-col items-center">
                    <component class="text-6xl bg-gray-300 p-4 rounded-lg" :is="FaMartiniGlassEmpty" />
                    <h4 class="text-xs text-gray-500 mt-3">No Conversations</h4>
                </div>
            </section>

            <!-- Other Users Section -->
            <section>
                <h2 class="text-2xl font-semibold mb-4">Other Users</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-1" v-if="users.length">
                    <div v-for="user in users" :key="user.id" class="max-w-md w-full">
                        <div class="bg-white border rounded-lg shadow-md p-4 flex items-center">
<!--                            <div class="w-12 h-12 rounded-full bg-gray-200 flex-shrink-0 mr-4"></div>-->
                            <img :src="user.avatar ? user.avatar : UserPng" width="100%" height="100%" class="w-12 h-12 rounded-full ring-1 ring-blue-300 mr-3">
                            <div class="flex flex-col">
                                <h3 class="font-bold text-lg">{{ user.name }}</h3>
                                <p class="text-sm text-gray-500">{{ user.email }}</p>
                            </div>
                            <button @click="startConversation(user.id)" title="create conversation" class="mt-2 px-3 py-1 bg-blue-500 self-end ml-auto text-white rounded">
                                <component class="text-xl" :is="FaBandsRocketchat"></component>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="flex justify-center flex-col items-center">
                    <component class="text-6xl bg-gray-300 p-4 rounded-lg" :is="FaMartiniGlassEmpty" />
                    <h4 class="text-xs text-gray-500 mt-3">No New Users</h4>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
