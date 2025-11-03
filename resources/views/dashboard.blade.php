<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold">Your Simulations</h3>
                        <a href="{{ route('simulations.create') }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            + New Simulation
                        </a>
                    </div>

                    @if($simulations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($simulations as $simulation)
                                <div class="bg-white/5 backdrop-blur-sm rounded-lg p-4 hover:bg-white/10 transition">
                                    <h4 class="text-lg font-semibold mb-2">{{ $simulation->name }}</h4>
                                    <p class="text-sm text-gray-300 mb-2">
                                        Initial: €{{ number_format($simulation->settings['initialInvestment'], 2) }}
                                    </p>
                                    <p class="text-sm text-gray-300 mb-4">
                                        Created: {{ $simulation->created_at->diffForHumans() }}
                                    </p>
                                    <div class="flex gap-2">
                                        <a href="{{ route('simulations.show', $simulation) }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-1 px-3 rounded">
                                            View
                                        </a>
                                        <a href="{{ route('simulations.edit', $simulation) }}" 
                                           class="bg-yellow-600 hover:bg-yellow-700 text-white text-sm py-1 px-3 rounded">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-300 mb-4">You haven't created any simulations yet.</p>
                            <a href="{{ route('simulations.create') }}" 
                               class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                                Create Your First Simulation
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>