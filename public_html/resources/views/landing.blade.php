<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solar Monitoring System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 min-h-screen text-white">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="text-center mb-12">
            <div class="flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
                </svg>
                <h1 class="text-4xl font-bold">Solar Monitoring System</h1>
            </div>
            <p class="text-xl text-blue-200">Monitor solar installations, track performance, and manage multiple vendor systems</p>
        </header>

        <!-- Current Status -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-center">System Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-green-500/20 border border-green-500/30 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-green-400">{{ $dashboard_data['total_systems'] ?? 0 }}</div>
                    <div class="text-sm text-green-200">Total Systems</div>
                </div>
                <div class="bg-blue-500/20 border border-blue-500/30 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-blue-400">{{ $dashboard_data['active_systems'] ?? 0 }}</div>
                    <div class="text-sm text-blue-200">Active Systems</div>
                </div>
                <div class="bg-yellow-500/20 border border-yellow-500/30 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-yellow-400">{{ number_format($dashboard_data['total_energy_today'] ?? 0, 1) }}</div>
                    <div class="text-sm text-yellow-200">kWh Today</div>
                </div>
                <div class="bg-orange-500/20 border border-orange-500/30 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-orange-400">{{ number_format($dashboard_data['total_power_current'] ?? 0, 1) }}</div>
                    <div class="text-sm text-orange-200">kW Current</div>
                </div>
            </div>
        </div>

        <!-- API Status -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-center">API Integration Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-500/20 border border-green-500/30 rounded-lg">
                        <span class="flex items-center">
                            <span class="text-2xl mr-2">🌤️</span>
                            Weather API
                        </span>
                        <span class="text-green-400 font-semibold">✅ Connected</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-500/20 border border-red-500/30 rounded-lg">
                        <span class="flex items-center">
                            <span class="text-2xl mr-2">⚡</span>
                            SolarEdge API
                        </span>
                        <span class="text-red-400 font-semibold">❌ Needs API Key</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-red-500/20 border border-red-500/30 rounded-lg">
                        <span class="flex items-center">
                            <span class="text-2xl mr-2">🔆</span>
                            Enphase API
                        </span>
                        <span class="text-red-400 font-semibold">❌ Needs API Key</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-500/20 border border-red-500/30 rounded-lg">
                        <span class="flex items-center">
                            <span class="text-2xl mr-2">🚗</span>
                            Tesla API
                        </span>
                        <span class="text-red-400 font-semibold">❌ Needs OAuth</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-center">Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-4xl mb-3">📊</div>
                    <h3 class="text-lg font-semibold mb-2">Real-time Monitoring</h3>
                    <p class="text-blue-200 text-sm">Track energy production, power output, and system efficiency in real-time</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl mb-3">🌦️</div>
                    <h3 class="text-lg font-semibold mb-2">Weather Integration</h3>
                    <p class="text-blue-200 text-sm">Monitor weather conditions that affect solar panel performance</p>
                </div>
                <div class="text-center">
                    <div class="text-4xl mb-3">⚠️</div>
                    <h3 class="text-lg font-semibold mb-2">Downtime Detection</h3>
                    <p class="text-blue-200 text-sm">Automatic alerts for system failures and performance issues</p>
                </div>
            </div>
        </div>

        <!-- Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-200 transform hover:scale-105">
                Access Dashboard
            </a>
        </div>

        <!-- Footer -->
        <footer class="text-center mt-12 text-blue-300 text-sm">
            <p>&copy; {{ date('Y') }} Solar Monitoring System. Built with Laravel.</p>
        </footer>
    </div>
</body>
</html>