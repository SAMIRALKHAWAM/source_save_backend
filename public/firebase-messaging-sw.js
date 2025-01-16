importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyAomavWmzEEkSAnAmVMxXVx_Cpkz5dUnF0",
      authDomain: "sourcesave-31966.firebaseapp.com",
      projectId: "sourcesave-31966",
      storageBucket: "sourcesave-31966.firebasestorage.app",
      messagingSenderId: "452046149500",
      appId: "1:452046149500:web:4dbf88044dac13031ff6f9"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.icon,
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
