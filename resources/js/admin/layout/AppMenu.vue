<script setup>
import { ref } from 'vue';
import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    {
        label: 'Overview',
        items: [
            {
                label: 'Dashboard',
                icon: 'pi pi-home',
                to: '/'
            }
        ]
    },
    {
        label: 'Users',
        items: [
            {
                label: 'Users',
                icon: 'pi pi-users',
                to: '/users'
            },
            {
                label: 'Admins',
                icon: 'pi pi-user-plus',
                to: '/admins'
            }
        ]
    },
    {
        label: 'Services',
        items: [
            {
                label: 'Services',
                icon: 'pi pi-globe',
                to: '/services'
            },
            {
                label: 'Orders',
                icon: 'pi pi-shopping-cart',
                to: '/orders'
            },
            {
                label: 'Phone Numbers',
                icon: 'pi pi-phone',
                to: '/phone-numbers'
            }
        ]
    },
    {
        label: 'Billing',
        items: [
            {
                label: 'Transactions',
                icon: 'pi pi-credit-card',
                to: '/transactions'
            },
            {
                label: 'Payments',
                icon: 'pi pi-wallet',
                to: '/payments'
            }
        ]
    },
    {
        label: 'Account',
        items: [
            {
                label: 'Settings',
                icon: 'pi pi-cog',
                to: '/settings'
            },
            {
                label: 'Profile',
                icon: 'pi pi-user',
                to: '/profile'
            }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <AppMenuItem v-for="(item, index) in model" :key="item.label + '_' + index" :item="item" :root="true" />
    </ul>
</template>
