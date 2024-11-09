<script setup>

import {defineComponent, toRefs, defineEmits, onMounted, ref, watch, nextTick, onBeforeUnmount} from 'vue';
import MessageInput from './MessageInput.vue';
import Avatar from '@/Components/Avatar.vue';
import TypingIndicator from '@/Components/TypingIndicator.vue';
import UserPng from "@/assets/images/user.png";
import {Link, useForm} from "@inertiajs/vue3";
import { FaArrowLeftLong } from '@kalimahapps/vue-icons';
import { AnOutlinedArrowLeft } from '@kalimahapps/vue-icons';
const emit = defineEmits(['sendMessage'])
const props = defineProps({
    selectedConversation:Object,
    createdBy:Object
})

const isMobile = ref(window.innerWidth <= 768);
const messageContainer = ref(null)
const isTyping = ref(false); // Track if the other user is typing
const typingUser = ref(false); // Track if the other user is typing
const isTypingTimer = ref(null);
const isUserOnline = ref(false);
const { selectedConversation } = toRefs(props);
const online_users = ref([])
// Store last scroll position for each conversation
const scrollPositions = ref({});

// Method to scroll to a specific position or bottom
const scrollToPosition = (position = 'bottom') => {
    if (selectedConversation.value.messages) {
        if (position === 'bottom') {
            // messageContainer.value.scrollTo({
            //     top: messageContainer.value.scrollHeight,
            //     behavior: "smooth",
            // });
            messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
        } else {
            messageContainer.value.scrollTop = position;
        }
    }
};

const sendMessage = (message) => {
    if (selectedConversation.value) {
        selectedConversation.value.messages.push({
            id: Date.now(),
            message: message,
            isMine: true,
            created_at:Date.now()
        });
    }
};

// Method to scroll to the bottom of the message list
const scrollToBottom = () => {
    if (selectedConversation.value.messages) {
        messageContainer.value.scrollTo({
            top: messageContainer.value.scrollHeight,
            behavior: "smooth",
        });
    }
};

const sendTypingEvent = () => {
    // useForm({}).post(`/conversations/${props.selectedConversation.id}/typing`);
    Echo.private(`chat.${props.selectedConversation.id}`).whisper("typing", {
        userID: props.createdBy.id,
        Name: props.createdBy.name,
    });
}

const formatTime = (datetime) => {
    const options = { hour: '2-digit', minute: '2-digit' };
    return new Date(datetime).toLocaleTimeString([], options);
};

// Watch for changes in messages and scroll to bottom if it's a new message
watch(selectedConversation.value.messages, (newMessages, oldMessages) => {
    if (newMessages.length !== oldMessages.length) {
        scrollToPosition('bottom');
    }
});

watch(
    selectedConversation.value.messages,
    () => {
        nextTick(() => {
            messageContainer.value.scrollTo({
                top: messageContainer.value.scrollHeight,
                behavior: "smooth",
            });
        });
    },
    { deep: true }
);

onMounted(() => {

    // Listen for typing events in the conversation
    // window.Echo.private(`chat.${props.selectedConversation.id}`)
    //     .listen('UserTyping', (e) => {
    //         if (e.userId !== $page.props.currentUser.id) {
    //             isTyping.value = true;
    //             setTimeout(() => {
    //                 isTyping.value = false;
    //             }, 2000); // Remove typing indicator after 2 seconds of inactivity
    //         }
    //     });
    const lastPosition = scrollPositions.value[props.selectedConversation.id] || 'bottom'
    scrollToPosition(lastPosition)
    Echo.join(`presence.chat.${props.selectedConversation.id}`)
        .here(users => {
            console.log('here:',users)
            const online_user_ids = users.map(user => user.id)
            online_users.value.push(...online_user_ids)
        })
        .joining(user => {
            console.log('user joining:',user)
            if (!online_users.value.includes(user.id)) {
                online_users.value.push(user.id);
            }
            // let online_user = props.selectedConversation.participants.find(u => u.id === user.id)
            // online_user = online_user.is_online = true
            // props.selectedConversation.participants = props.selectedConversation.participants.map(joining_user =>
            //     joining_user.id === user.id ? {...joining_user,...online_user } : joining_user
            // )
            // if (user.id === props.createdBy.id) isUserOnline.value = true;
        })
        .leaving(user => {
            console.log('user leaving:',user)
            online_users.value = online_users.value.filter(id => id !== user.id);
            // let online_user = props.selectedConversation.participants.find(u => u.id === user.id)
            // online_user = online_user.is_online = false
            // props.selectedConversation.participants = props.selectedConversation.participants.map(leaving_user =>
            //     leaving_user.id === user.id ? {...leaving_user,...online_user } : leaving_user
            // )
        });

    // Listen for new messages in the conversation
    window.Echo.private(`chat.${props.selectedConversation.id}`)
        .listen('NewMessage', (e) => {
            selectedConversation.value.messages.push(e.message);
            nextTick(() => {
                messageContainer.value?.scrollTo({
                    top: messageContainer.value.scrollHeight,
                    behavior: "smooth",
                });
            });
        }).listenForWhisper('typing', (response) => {
            isTyping.value = true;
            typingUser.value = response.Name;
            if (isTypingTimer.value) {
                clearTimeout(isTypingTimer.value);
            }
            isTypingTimer.value = setTimeout(() => {
                isTyping.value = false;
            }, 1000);

        });
});

const isOnline = () => {
    online_users.value.find(o_u => o_u === props.page.auth.user.id)
}

// Save the current scroll position before leaving
onBeforeUnmount(() => {
    scrollPositions.value[props.selectedConversation.id] = messageContainer.value.scrollTop;
    window.Echo.leave(`presence.chat.${props.selectedConversation.id}`);
});


</script>

<template>

    <div class="h-full w-full flex flex-col">
        <!-- Header with Avatar and Name -->
        <div class="flex p-4 gap-5 bg-white border-b w-100 text-center justify-start items-center">
<!--            <img width="100%" height="100%" class="w-[60px] h-[60px] rounded-full ring-1 ring-blue-300" :src="createdBy.avatar ? createdBy.avatar : UserPng" alt="avatar">-->

            <ul class="m-0 p-0">
                <li>
                    <div class="flex items-center gap-3">
                        <Link :href="route('conversations.index')" v-if="isMobile">
                            <component :is="AnOutlinedArrowLeft" />
                        </Link>
                        <Avatar :online="isOnline" size="md" :name="selectedConversation.participants[0].name" :avatar="selectedConversation.participants[0].avatar ? selectedConversation.participants[0].avatar : UserPng" />

                        <div class="text-left">

                        <span class="font-semibold m-0 p-0">{{ selectedConversation.participants[0].name || 'Private Chat' }}</span>
                        <p v-if="isTyping" class="text-gray-600 text-xs">
                            typing...
                        </p>
                        </div>
                    </div>

                </li>
            </ul>
<!--            <ul class="flex -space-x-4 rtl:space-x-reverse ml-auto">-->
<!--                <li class="" v-for="user in selectedConversation.participants.slice(0,20)">-->
<!--                    <Avatar v-if="online_users.includes(user.id)" size="md" :online="true" :name="user.name" :avatar="user.avatar ? user.avatar : UserPng" />-->
<!--                    <a v-if="selectedConversation.participants.length > 20" class="flex items-center justify-center w-10 h-10 text-xs font-medium text-white bg-gray-700 border-2 border-white rounded-full hover:bg-gray-600 dark:border-gray-800" href="#">+{{selectedConversation.participants.length}}</a>-->
<!--                </li>-->
<!--            </ul>-->
        </div>

        <!-- Messages Area -->
        <div class="flex-1 p-4  overflow-y-auto bg-[url('@/assets/images/chat-bg.png')] bg-cover bg-center bg-opacity-0" ref="messageContainer">
            <div v-for="message in selectedConversation.messages" :key="message.id"
                 class="mb-4 flex"
                 :class="{'text-end' : message.user_id ===  $page.props.currentUser.id}">
                    <Avatar v-if="message.user.id !== createdBy.id" size="md" :name="message.user.name" class="self-end mr-1" :avatar="message.user.avatar ? message.user.avatar : UserPng" />
                <div
                    :class="[
                    'inline-block p-2 rounded-lg max-w-[75%]',
                    message.user_id ===  $page.props.currentUser.id ? 'bg-blue-500 text-white self-end ml-auto' : 'bg-gray-200'
                ]">
                    <b class="text-xs">{{ message.user.name }}</b>
                    <div class="flex gap-2 items-center relative">
                        <div>
                            <p class="text-sm self-start ">{{ message.message }}</p>
                        </div>
                        <span class="text-[10px] self-end ml-auto">{{ formatTime(message.created_at) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative">

            <TypingIndicator v-if="isTyping" class="absolute bottom-[90px] left-3" />
            <!-- Message Input -->
            <MessageInput @onInput="sendTypingEvent()" :conversation="selectedConversation" :currentuser="createdBy" />
        </div>

    </div>

</template>

<style scoped>

</style>
