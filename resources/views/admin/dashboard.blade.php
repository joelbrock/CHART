<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Admin Panel</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Staff Management -->
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-blue-900 mb-2">Staff Management</h4>
                            <p class="text-blue-700 mb-4">Manage staff members and their permissions</p>
                            <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Manage Staff
                            </a>
                        </div>

                        <!-- Client Management -->
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-green-900 mb-2">Client Management</h4>
                            <p class="text-green-700 mb-4">Manage clients and their assignments</p>
                            <a href="#" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Manage Clients
                            </a>
                        </div>

                        <!-- System Settings -->
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-purple-900 mb-2">System Settings</h4>
                            <p class="text-purple-700 mb-4">Configure system-wide settings</p>
                            <a href="#" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                                Settings
                            </a>
                        </div>

                        <!-- Reports -->
                        <div class="bg-orange-50 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-orange-900 mb-2">Reports</h4>
                            <p class="text-orange-700 mb-4">Generate and manage reports</p>
                            <a href="{{ route('reports.batch') }}" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
                                Generate Reports
                            </a>
                        </div>

                        <!-- User Activity -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">User Activity</h4>
                            <p class="text-gray-700 mb-4">Monitor user activity and logs</p>
                            <a href="#" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                                View Activity
                            </a>
                        </div>

                        <!-- Database Tools -->
                        <div class="bg-red-50 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-red-900 mb-2">Database Tools</h4>
                            <p class="text-red-700 mb-4">Database maintenance and tools</p>
                            <a href="#" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                Database Tools
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
