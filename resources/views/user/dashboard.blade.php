<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} | Dashboard</title>
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
    @vite(['resources/css/app.css', 'resources/js/user/main.js'])
</head>
<body class="bg-surface-50 dark:bg-surface-950">
    <div id="user-dashboard"></div>
</body>
</html>
