<template>
    <div class="fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t flex justify-around py-3 z-50">
        <NavLink v-for="tab in tabs" :key="tab.name" :href="tab.route" class="flex flex-col items-center" :active="route().current(tab.name)">
            <component :is="tab.icon" class="text-xl mb-1" />
            <span :class="{'text-blue-500': $page.component === tab.name, 'text-gray-500': $page.component !== tab.name}">
        {{ tab.name }}
      </span>
        </NavLink>
    </div>
</template>

<script setup>
    import NavLink from '@/Components/NavLink.vue';

    // Import icons (You can use any icon library)
    import { AkChatBubble , FaRegUser, FaUsersLine, FaPhoneFlip,FaCamera } from '@kalimahapps/vue-icons';

    const tabs = [
        { name: 'Chats', route: '/chats', icon: AkChatBubble },
        { name: 'Status', route: '/status', icon: FaCamera },
        { name: 'Account', route: '/account', icon: FaRegUser },
        { name: 'Groups', route: '/groups', icon: FaUsersLine },
        { name: 'Calls', route: '/calls', icon: FaPhoneFlip },
    ];

</script>

<style scoped>
button {
    flex: 1;
}
</style>
