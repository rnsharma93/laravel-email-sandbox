<!DOCTYPE html>
<html lang="en" class="bg-gray-50 antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Sandbox</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3b82f6',
                    }
                }
            }
        }
    </script>
</head>

<body class="text-gray-900 flex h-screen overflow-hidden bg-gray-900" x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" x-cloak @click="sidebarOpen = false"
        x-transition.opacity></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed lg:static lg:translate-x-0 inset-y-0 left-0 z-50 w-64 bg-gray-900 text-gray-300 flex flex-col shrink-0 transition-transform duration-300 ease-in-out">
        <div class="h-16 flex items-center justify-between px-6 border-b border-gray-800">
            <div class="flex items-center gap-3 font-bold text-lg tracking-tight text-white">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                Email Sandbox
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <nav class="flex-1 py-4 space-y-1 relative">
            <a href="{{ route('email-sandbox.index') }}"
                class="flex items-center px-6 py-2.5 bg-gray-800 text-white border-l-2 border-blue-500 font-medium transition-colors">
                <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                    </path>
                </svg>
                Inbox
            </a>

            <div class="absolute bottom-4 left-0 w-full px-4">
                <form action="{{ route('email-sandbox.destroy-all') }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete ALL emails? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2.5 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Delete All
                    </button>
                </form>
            </div>
        </nav>
        <div class="p-4 flex items-center justify-center border-t border-gray-800 text-xs text-gray-600">
            <a href="/" class="hover:text-gray-300 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to App
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main
        class="flex-1 flex flex-col h-screen overflow-hidden bg-white sm:rounded-l-xl shadow-2xl relative border-l border-gray-800">
        @yield('content')
    </main>
</body>

</html>