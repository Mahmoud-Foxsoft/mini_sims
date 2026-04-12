<template>
  <Dialog 
    v-model:visible="showPrompt" 
    position="bottomright" 
    :modal="false" 
    :closable="false" 
    :draggable="false"
    :showHeader="false"
    class="w-[90%] sm:w-[400px] overflow-hidden rounded-2xl border border-white/20 bg-white/70 shadow-2xl backdrop-blur-md dark:border-gray-700/50 dark:bg-gray-900/70"
    :pt="{ content: { class: '!p-5 bg-transparent' } }"
  >
    <div class="flex flex-col gap-4">
      <div class="text-left">
        <h3 class="m-0 mb-1 text-base font-semibold text-gray-900 dark:text-white">
          Enable push notifications
        </h3>
        <p class="m-0 text-sm text-gray-600 dark:text-gray-300">
          Get toast alerts the moment orders or messages update. You can change this later in your browser settings.
        </p>
      </div>
      
      <div class="flex w-full items-center justify-end gap-2">
        <Button 
          label="Not now" 
          text 
          severity="secondary" 
          @click="handleDismiss" 
        />
        <Button 
          label="Enable" 
          @click="handleEnable" 
        />
      </div>
    </div>
  </Dialog>
</template>

<script setup>
import { computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useNotificationStore } from '@/stores/notifications';

const store = useNotificationStore();
const toast = useToast();

const showPrompt = computed({
  get: () => store.canPrompt,
  set: (value) => {
    if (!value) {
      store.dismissPrompt();
    }
  }
});

const handleDismiss = () => {
  store.dismissPrompt();
};

const handleEnable = async () => {
  await store.enableNotifications(toast);
};
</script>