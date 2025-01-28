<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container mx-auto">
        <h2 class="text-2xl font-bold mb-6">Configuration des plateformes</h2>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf

            <!-- Slack -->
            <div class="mb-4">
                <label for="slack_webhook" class="block text-sm font-medium text-gray-700">Webhook Slack</label>
                <input type="url" id="slack_webhook" name="slack_webhook"
                    value="{{ optional($userPlatforms->where('platform', 'slack')->first())->config['webhook'] ?? '' }}"
                    class="w-full border-gray-300 rounded p-2">
                @error('slack_webhook')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Telegram -->
            <div class="mb-4">
                <label for="telegram_bot_token" class="block text-sm font-medium text-gray-700">Telegram Bot Token</label>
                <input type="text" id="telegram_bot_token" name="telegram_bot_token"
                    value="{{ optional($userPlatforms->where('platform', 'telegram')->first())->config['bot_token'] ?? '' }}"
                    class="w-full border-gray-300 rounded p-2">
                @error('telegram_bot_token')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telegram_channel_id" class="block text-sm font-medium text-gray-700">Telegram Channel ID</label>
                <input type="text" id="telegram_channel_id" name="telegram_channel_id"
                    value="{{ optional($userPlatforms->where('platform', 'telegram')->first())->config['chat_id'] ?? '' }}"
                    class="w-full border-gray-300 rounded p-2">
                @error('telegram_channel_id')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex items-center">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Enregistrer</button>
            </div>
        </form>
    </div>
</x-app-layout>