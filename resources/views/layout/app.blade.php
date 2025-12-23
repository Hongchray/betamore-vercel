<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-authenticated" content="true">
    @endauth
</head>
<body>
    <!-- Your content -->
    
    @auth
    <button id="enable-notifications-btn" class="btn btn-primary">
        Enable Notifications
    </button>
    @endauth
    
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>