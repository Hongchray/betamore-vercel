// Service Worker for Firebase Cloud Messaging
// Place this file in: public/firebase-messaging-sw.js

importScripts('https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js');

// Firebase configuration - REPLACE WITH YOUR CONFIG
const firebaseConfig = {
    apiKey: 'AIzaSyAlSsrILqH0jp-o0O9DoBPtoW0c8AQ7UMc',
    authDomain: 'beta-more-limited.firebaseapp.com',
    projectId: 'beta-more-limited',
    storageBucket: 'beta-more-limited.firebasestorage.app',
    messagingSenderId: '996581314656',
    appId: '1:996581314656:web:a916f0ead7c48a3af3b305',
    measurementId: 'G-M3H5EGB3G4',
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('Background message received:', payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon || '/icon.png',
        badge: '/badge.png',
        data: payload.data,
        requireInteraction: false,
        vibrate: [200, 100, 200],
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);

    event.notification.close();

    // Open the app or focus existing window
    event.waitUntil(clients.openWindow(event.notification.data?.url || '/'));
});
