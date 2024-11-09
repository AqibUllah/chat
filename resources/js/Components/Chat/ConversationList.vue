<script setup>
import {toRefs, ref, defineEmits, onMounted, onUnmounted, onBeforeUnmount, computed} from 'vue';
import {initDropdowns, initFlowbite} from "flowbite";
const emit = defineEmits(['conversationSelected'])
import { AkChatBubble } from '@kalimahapps/vue-icons';
import { FaEllipsisVertical } from '@kalimahapps/vue-icons';
import ChatMobileLayout from "@/Layouts/ChatMobileLayout.vue";
import ChatDesktopLayout from "@/Layouts/ChatDesktopLayout.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Avatar from "@/Components/Avatar.vue";
import {Link, useForm} from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import UserPng from '@/assets/images/user.png'
const props = defineProps({
    conversations:Array,
    currentUser: Object, // Add current user data from parent
    w_full:Boolean
})
import { AkPlus } from '@kalimahapps/vue-icons';
import moment from "moment";

const { conversations, currentUser } = toRefs(props);

const showNewConversationForm = ref(false);
const selectedParticipants = ref([]);
const searchTerm = ref('');

// Computed property to filter conversations based on the search term
const filteredConversations = computed(() => {
    if (!searchTerm.value) {
        return props.conversations;
    }
    return props.conversations.filter(conversation =>
        (conversation.name || 'Private Chat')
            .toLowerCase()
            .includes(searchTerm.value.toLowerCase())
    );
});

const form = useForm({
    participant_ids : ref([]),
    type:'private'
})

const createConversation = () => {
    // const participants = participantIds.value.split(',').map(id => id.trim());
    form.post('/conversations', {
        onFinish: () => form.reset(),
        onSuccess: () => {
            showNewConversationForm.value = false;
            selectedParticipants.value = [];
        },
    });
};


const selectConversation = (conversation) => {
     emit('conversationSelected', conversation);
};

const formatMessageTime = (timestamp) => {
    const date = moment(timestamp);
    if (date.isSame(moment(), 'day')) {
        return `${date.format('h:mm A')}`;
    } else if (date.isSame(moment().subtract(1, 'day'), 'day')) {
        // return `Yesterday at ${date.format('h:mm A')}`;
        return `Yesterday`;
    } else if (date.isSame(moment(), 'week')) {
        return `${date.format('dddd')}`;
    } else {
        return date.format('MMM D, YYYY [at] h:mm A');
    }
}

onMounted(() => {
    initDropdowns()
    window.addEventListener("resize", handleResize);
})

const isMobile = ref(window.innerWidth <= 768);

const handleResize = () => {
    isMobile.value = window.innerWidth <= 768;
};

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});

</script>

<template>

    <div class="h-full bg-gray-100 border-r fixed md:relative z-50 w-full" :style="{maxWidth: w_full ? '100%' : '400px'}">
        <!-- ConversationRequest list container with responsive classes -->
        <div class="h-full bg-gray-100 border-r fixed md:relative z-50 w-full" :style="{maxWidth: w_full ? '100%' : '400px'}">

            <!-- User Info at the top -->
            <div class="p-3 bg-white border-b flex items-center" v-if="!isMobile">
                <Avatar size="lg" :name="$page.props.auth.user.name" :avatar="$page.props.auth.user.avatar ? $page.props.auth.user.avatar : UserPng" />
                <div class="ml-2">
                    <div class="font-semibold">{{ $page.props.auth.user.name }}</div>
                    <div class="text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                </div>
                <button id="dropdownHoverButton" data-dropdown-toggle="dropdownHover"
                        data-dropdown-trigger="hover"
                        class="bg-gray-200 absolute right-2 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-3 " type="button">
                    <component :is="FaEllipsisVertical" />
                </button>

                <!-- Dropdown menu -->
                <div id="dropdownHover" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton">
                        <li>
                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </DropdownLink>
<!--                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Sign out</a>-->
                        </li>
                    </ul>
                </div>
            </div>

            <!-- New Conversation Button -->

            <form class="flex items-center max-w-sm mx-auto my-2">
                <label for="simple-search" class="sr-only">Search</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5v10M3 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 0V6a3 3 0 0 0-3-3H9m1.5-2-2 2 2 2"/>
                        </svg>
                    </div>
                    <input type="text" id="simple-search"
                           v-model="searchTerm"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-full
                           focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5
                           dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white
                           dark:focus:ring-blue-500 dark:focus:border-blue-500"
                           placeholder="Search conversations..." required />
                </div>
<!--                <button @click="showNewConversationForm = true" type="button" title="New Conversation"-->
<!--                    class="p-2.5 ms-2 text-sm font-medium text-white bg-blue-700 rounded-full-->
<!--                    border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none-->
<!--                    focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">-->
<!--                    <component :is="AkPlus" />-->
<!--                </button>-->
            </form>



            <!-- New Conversation Modal -->
            <div v-if="showNewConversationForm" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded w-full max-w-lg">
                    <h2 class="text-xl font-semibold mb-4">Start New Conversation</h2>

                    <form @submit.prevent="createConversation">
                        <label class="block mb-2">Select Participants:</label>

                        <!-- Flowbite Multi-Select Dropdown -->
                        <div class="relative">
                            <select v-model="form.participant_ids" multiple class="block w-full p-2 border rounded">
                                <option v-for="user in $page.props.users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Create</button>
                        <button @click="showNewConversationForm = false" class="px-4 py-2 ml-2 bg-gray-300 rounded">Cancel</button>
                    </form>
                </div>
            </div>

            <div class="space-y-4">
                    <Link v-for="conversation in filteredConversations" :key="conversation.id" :href="`/conversations/${conversation.id}`" class="block p-4 border rounded hover:bg-gray-200">
                        <ul class="flex items-center justify-between">
                            <li class="flex gap-3 items-center">
<!--                                <h2 class="font-semibold">{{ conversation.name || 'Private Chat' }}</h2>-->
<!--                                <div class="flex -space-x-4 rtl:space-x-reverse" >-->
<!--                                    <Avatar v-for="user in conversation.participants.slice(0,7)" size="md" :name="user.name" :avatar="user.avatar ? user.avatar : UserPng" />-->
<!--                                    <a v-if="conversation.participants.length > 7" class="flex items-center justify-center w-10 h-10 text-xs font-medium text-white bg-gray-700 border-2 border-white rounded-full hover:bg-gray-600 dark:border-gray-800" href="#">+{{conversation.participants.length}}</a>-->
<!--                                </div>-->
                                <Avatar size="md" :name="conversation.participants[0].name" :avatar="conversation.participants[0].avatar ? conversation.participants[0].avatar : UserPng" />
                                <div>
                                    <h2 class="font-semibold">{{ conversation.participants[0].name || 'Private Chat' }}</h2>
                                    <p class="text-sm text-gray-500  truncate text-ellipsis">
                                        <span v-if="conversation.messages.length">
                                            {{ conversation.messages[0].message }}
                                        </span>
                                    </p>
                                </div>
                            </li>
                            <li>
<!--                                <span class="px-3 py-1 w-6 h-6 ms-2 text-sm font-semibold text-blue-800 bg-blue-200 rounded-full">-->
<!--                                {{ conversation.participants.length }} Users-->
<!--                                </span>-->
                                <span class="text-sm text-gray-500" v-if="conversation.messages[0]">{{ formatMessageTime(conversation.messages[0].created_at) }}</span>
                            </li>
                        </ul>
                    </Link>
            </div>

            <!-- ConversationRequest List -->
<!--            <div class="p-4 text-lg font-semibold text-sm text-gray-400">Conversations</div>-->
<!--            <div-->
<!--                v-for="conversation in conversations"-->
<!--                :key="conversation.id"-->
<!--                @click="selectConversation(conversation)"-->
<!--                class="p-4 border-b cursor-pointer hover:bg-gray-200"-->
<!--            >-->

<!--                <div class="flex items-center gap-3">-->
<!--                    <div v-if="!conversation.avatar" class="relative w-11 h-11 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">-->
<!--                        <svg class="absolute w-12 h-12 text-gray-400 -left-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">-->
<!--                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>-->
<!--                        </svg>-->
<!--                    </div>-->
<!--                    <img v-else width="100%" height="100%" class="w-11 h-11 rounded-full ring-1 ring-blue-300" :src="conversation.avatar" alt="avatar">-->

<!--                    &lt;!&ndash; Left Content &ndash;&gt;-->
<!--                    <ul class="flex-grow">-->
<!--                        <li class="font-semibold text-sm">{{ conversation.name }}</li>-->
<!--&lt;!&ndash;                        <li class="text-sm text-gray-600 text-xs">{{ conversation.lastMessage }}</li>&ndash;&gt;-->
<!--                    </ul>-->

<!--                    &lt;!&ndash; Right Content &ndash;&gt;-->
<!--                    <ul class="flex flex-col items-end gap-1 ml-auto">-->
<!--                        <li>-->
<!--                            <span class="inline-flex items-center justify-center w-4 h-4 p-3 ms-2 text-xs font-semibold text-gray-800 bg-blue-100 rounded-full">2</span>-->
<!--                        </li>-->
<!--                        <li class="text-[10px] text-gray-500"><span>12 minutes ago</span></li>-->
<!--                    </ul>-->
<!--                </div>-->

<!--            </div>-->
        </div>
    </div>
</template>

<style scoped>

/* Style for responsive handling */
@media (max-width: 768px) {
    .fixed {
        position: fixed;
        width: 100%;
        max-width: 00px;
    }
}

</style>
