<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Create New Simulation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('simulations.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-white font-semibold mb-2">
                                Simulation Name
                            </label>
                            <input type="text" name="name" id="name" required
                                   class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30 focus:border-white/60 focus:outline-none"
                                   value="{{ old('name') }}">
                            @error('name')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="initial_investment" class="block text-white font-semibold mb-2">
                                    Initial Investment (€)
                                </label>
                                <input type="number" name="initial_investment" id="initial_investment" 
                                       step="0.01" min="0" required
                                       class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30 focus:border-white/60 focus:outline-none"
                                       value="{{ old('initial_investment', 1000) }}">
                                @error('initial_investment')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="monthly_contribution" class="block text-white font-semibold mb-2">
                                    Monthly Contribution (€)
                                </label>
                                <input type="number" name="monthly_contribution" id="monthly_contribution" 
                                       step="0.01" min="0" required
                                       class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30 focus:border-white/60 focus:outline-none"
                                       value="{{ old('monthly_contribution', 100) }}">
                                @error('monthly_contribution')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="growth_rate" class="block text-white font-semibold mb-2">
                                    Annual Growth Rate (0.05 = 5%)
                                </label>
                                <input type="number" name="growth_rate" id="growth_rate" 
                                       step="0.01" min="0" max="1" required
                                       class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30 focus:border-white/60 focus:outline-none"
                                       value="{{ old('growth_rate', 0.05) }}">
                                @error('growth_rate')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="inflation_rate" class="block text-white font-semibold mb-2">
                                    Annual Inflation Rate (0.02 = 2%)
                                </label>
                                <input type="number" name="inflation_rate" id="inflation_rate" 
                                       step="0.005" min="0" max="1" required
                                       class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30 focus:border-white/60 focus:outline-none"
                                       value="{{ old('inflation_rate', 0.02) }}">
                                @error('inflation_rate')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="risk_appetite" class="block text-white font-semibold mb-2">
                                    Risk Appetite (0-1)
                                </label>
                                <input type="number" name="risk_appetite" id="risk_appetite" 
                                       step="0.01" min="0" max="1" required
                                       class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30 focus:border-white/60 focus:outline-none"
                                       value="{{ old('risk_appetite', 0.5) }}">
                                @error('risk_appetite')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="market_influence" class="block text-white font-semibold mb-2">
                                    Market Influence (0-1)
                                </label>
                                <input type="number" name="market_influence" id="market_influence" 
                                       step="0.01" min="0" max="1" required
                                       class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30 focus:border-white/60 focus:outline-none"
                                       value="{{ old('market_influence', 0.7) }}">
                                @error('market_influence')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="investors" class="block text-white font-semibold mb-2">
                                    Number of Investors
                                </label>
                                <input type="number" name="investors" id="investors" 
                                       min="1" required
                                       class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30 focus:border-white/60 focus:outline-none"
                                       value="{{ old('investors', 10) }}">
                                @error('investors')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                                Create Simulation
                            </button>
                            <a href="{{ route('dashboard') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>