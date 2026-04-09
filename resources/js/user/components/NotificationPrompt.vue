<template>
  <transition name="slide-up">
    <div v-if="store.canPrompt" class="notification-banner glass-panel">
      <div>
        <p class="notification-banner__title">Enable push notifications</p>
        <p class="notification-banner__copy">
          Get toast alerts the moment orders or messages update. You can change this later in your browser settings.
        </p>
      </div>
      <div class="notification-banner__actions">
        <button class="btn-primary" @click="store.enableNotifications">Enable</button>
        <button class="ghost" @click="store.dismissPrompt">Not now</button>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { useNotificationStore } from '@/stores/notifications';

const store = useNotificationStore();
</script>
