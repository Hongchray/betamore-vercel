import { initializeApp } from 'firebase/app';
import { getMessaging } from 'firebase/messaging';

const firebaseConfig = {
    apiKey: 'AIzaSyCg-ajvaiqL5Emca9ZkTcsRvrqrNryAg5M',
    authDomain: 'beta-more.firebaseapp.com',
    projectId: 'beta-more',
    storageBucket: 'beta-more.firebasestorage.app',
    messagingSenderId: '226595860330',
    appId: '1:226595860330:web:3e0d434e7e836151e33f13',
    measurementId: 'G-NFYL3BX4TS',
};

export const app = initializeApp(firebaseConfig);
export const messaging = getMessaging(app);
