<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - OpenCom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#4F46E5', secondary: '#111827' }
                }
            }
        }
    </script>
</head>
<body class="bg-secondary h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border-t-4 border-primary">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Administrator</h1>
            <p class="text-gray-500">Panel Manajemen Toko</p>
        </div>

        <!-- Alert Error -->
        @if ($errors->has('username'))
            <div class="mb-4 p-3 bg-red-100 border border-red-200 text-red-600 rounded-lg text-sm">
                {{ $errors->first('username') }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Input Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username Admin</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autofocus
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" 
                    placeholder="username"
                >
            </div>

            <!-- Input Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" 
                    placeholder="password"
                >
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-primary focus:ring-primary mr-2">
                    Ingat saya
                </label>
            </div>
            
            <button type="submit" class="w-full block text-center bg-gray-900 text-white font-semibold py-2.5 rounded-lg hover:bg-black transition">
                Masuk ke Dashboard
            </button>
        </form>
    </div>

</body>
</html>