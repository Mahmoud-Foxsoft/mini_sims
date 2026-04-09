import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, isSupported, onMessage } from 'firebase/messaging';

let firebaseApp;
let messagingInstance;
let serviceWorkerRegistration;

const firebaseConfig = {
  //   apiKey: "AIzaSyAw7VkzA7RxM01cqR2a-55ze6jdVQvJRAg",
  // authDomain: "lilsims-6e83e.firebaseapp.com",
  // projectId: "lilsims-6e83e",
  // storageBucket: "lilsims-6e83e.firebasestorage.app",
  // messagingSenderId: "207888424046",
  // appId: "1:207888424046:web:30c449cc2c720af7569a7f"
};

export const notificationsSupported = () => typeof window !== 'undefined' && 'Notification' in window;

const ensureFirebaseApp = () => {
  if (firebaseApp) {
    return firebaseApp;
  }
  if (!firebaseConfig.apiKey || !firebaseConfig.messagingSenderId) {
    console.warn('[Notifications] Missing Firebase configuration.');
    return null;
  }
  firebaseApp = initializeApp(firebaseConfig);
  return firebaseApp;
};

export const getMessagingClient = async () => {
  if (!notificationsSupported()) {
    return null;
  }
  const supported = await isSupported().catch(() => false);
  if (!supported) {
    console.warn('[Notifications] Firebase messaging not supported in this browser.');
    return null;
  }
  if (!messagingInstance) {
    const app = ensureFirebaseApp();
    if (!app) {
      return null;
    }
    messagingInstance = getMessaging(app);
  }
  return messagingInstance;
};

export const requestFirebaseToken = async () => {
  const messaging = await getMessagingClient();
  if (!messaging) {
    return null;
  }
  const vapidKey = import.meta.env.VITE_FIREBASE_VAPID_KEY;
  const registration = await getServiceWorkerRegistration();
  const options = {};
  if (registration) {
    options.serviceWorkerRegistration = registration;
  }
  if (vapidKey) {
    options.vapidKey = vapidKey;
  }
  return await getToken(messaging, options);
};

const getServiceWorkerRegistration = async () => {
  if (serviceWorkerRegistration || typeof navigator === 'undefined' || !('serviceWorker' in navigator)) {
    return serviceWorkerRegistration;
  }
  try {
    serviceWorkerRegistration = await navigator.serviceWorker.ready;
    return serviceWorkerRegistration;
  } catch (error) {
    console.warn('Service worker not ready', error);
    return null;
  }
};

export const subscribeToForegroundMessages = async (callback) => {
  const messaging = await getMessagingClient();
  if (!messaging) {
    return () => {};
  }
  return onMessage(messaging, callback);
};

export const notificationHelpers = {
  notificationsSupported,
  requestFirebaseToken,
  subscribeToForegroundMessages,
  getMessagingClient,
};
