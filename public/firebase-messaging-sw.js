importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js');

firebase.initializeApp({
  //   apiKey: "AIzaSyAw7VkzA7RxM01cqR2a-55ze6jdVQvJRAg",
  // authDomain: "lilsims-6e83e.firebaseapp.com",
  // projectId: "lilsims-6e83e",
  // storageBucket: "lilsims-6e83e.firebasestorage.app",
  // messagingSenderId: "207888424046",
  // appId: "1:207888424046:web:30c449cc2c720af7569a7f"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  const notification = payload.notification || {};
  const title = notification.title || 'Lil Sims Portal';
  const options = {
    body: notification.body,
    data: payload.data || {},
    icon: '/favicon.ico',
  };

  self.registration.showNotification(title, options);
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const url = event.notification.data?.url || '/portal';
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then((clientList) => {
      for (const client of clientList) {
        if (client.url.includes(url) && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(url);
      }
      return null;
    })
  );
});
