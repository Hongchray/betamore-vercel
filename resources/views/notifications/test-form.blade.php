<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Firebase Notifications</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .token-display {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            color: #333;
        }
        .status {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .permission-btn {
            background: #28a745;
            margin-bottom: 20px;
        }
        .permission-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 Firebase Notification Tester</h1>
        <p class="subtitle">Test push notifications for Chrome browser</p>

        <div id="status"></div>

        <button class="permission-btn" onclick="requestPermissionAndGetToken()">
            Grant Permission & Get FCM Token
        </button>

        <div id="token-container" style="display: none;">
            <label>Your FCM Token:</label>
            <div class="token-display" id="fcm-token-display"></div>
        </div>

        <form id="notification-form">
            <div class="form-group">
                <label>FCM Token *</label>
                <input type="text" id="fcm_token" name="fcm_token" required 
                       placeholder="Token will appear here after granting permission">
            </div>

            <div class="form-group">
                <label>Notification Title *</label>
                <input type="text" name="title" required 
                       placeholder="e.g., Hello from Laravel!" value="Test Notification">
            </div>

            <div class="form-group">
                <label>Notification Body *</label>
                <textarea name="body" required 
                          placeholder="Enter your notification message...">This is a test notification from Laravel + Firebase!</textarea>
            </div>

            <button type="submit">Send Test Notification</button>
        </form>
    </div>

    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>
    
    <script>
        // Firebase configuration - REPLACE WITH YOUR CONFIG
        const firebaseConfig = {
           apiKey: "AIzaSyAlSsrILqH0jp-o0O9DoBPtoW0c8AQ7UMc",
            authDomain: "beta-more-limited.firebaseapp.com",
            projectId: "beta-more-limited",
            storageBucket: "beta-more-limited.firebasestorage.app",
            messagingSenderId: "996581314656",
            appId: "1:996581314656:web:a916f0ead7c48a3af3b305",
            measurementId: "G-M3H5EGB3G4"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        function showStatus(message, type = 'info') {
            const statusDiv = document.getElementById('status');
            statusDiv.className = `status ${type}`;
            statusDiv.innerHTML = message;
            statusDiv.style.display = 'block';
        }

        async function requestPermissionAndGetToken() {
            try {
                const permission = await Notification.requestPermission();
                
                if (permission === 'granted') {
                    const token = await messaging.getToken({
                        vapidKey: 'BKreHvzFUsbymuLGjXCA91SqP_IhZc5DRMs9g78E0e1sQwyMSgM3wOtDpqA8Tykh4j9vrHqSwOBCNFYPdvm_yrk' // Get from Firebase Console
                    });
                    
                    document.getElementById('fcm_token').value = token;
                    document.getElementById('fcm-token-display').textContent = token;
                    document.getElementById('token-container').style.display = 'block';
                    
                    showStatus('✓ Permission granted! FCM Token generated successfully.', 'success');
                    
                    // Copy token to clipboard
                    navigator.clipboard.writeText(token);
                    console.log('FCM Token:', token);
                } else {
                    showStatus('❌ Permission denied. Please enable notifications in your browser settings.', 'error');
                }
            } catch (error) {
                showStatus('❌ Error: ' + error.message, 'error');
                console.error('Error getting token:', error);
            }
        }

        // Handle foreground messages
        messaging.onMessage((payload) => {
            console.log('Message received:', payload);
            showStatus(`📬 Notification received: ${payload.notification.title}`, 'success');
            
            // Show notification
            new Notification(payload.notification.title, {
                body: payload.notification.body,
                icon: payload.notification.icon
            });
        });

        // Form submission
        document.getElementById('notification-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('/api/api/notifications/send-test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showStatus('✓ Notification sent successfully! Check your browser.', 'success');
                } else {
                    showStatus('❌ Failed to send: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                showStatus('❌ Error: ' + error.message, 'error');
            }
        });
    </script>
</body>
</html>