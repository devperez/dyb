<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    @if (session('status'))
        <div id="flash-message"
            class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white p-4 rounded-md shadow-lg">
            {{ session('status') }}
        </div>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('scheduledMessages.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea id="content" name="content" rows="4" class="mt-1 block w-full border-gray-300 rounded-md"
                        placeholder="Votre message"></textarea>
                </div>

                <div class="mb-4">
                    <p class="block text-sm font-medium text-gray-700">Choisissez les réseaux sociaux :</p>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="platforms[]" value="slack" class="form-checkbox">
                        <span class="ml-2">Slack</span>
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="checkbox" name="platforms[]" value="telegram" class="form-checkbox">
                        <span class="ml-2">Telegram</span>
                    </label>
                </div>

                <div class="mb-4">
                    <label for="send_at" class="block text-sm font-medium text-gray-700">Date et heure d'envoi</label>
                    <input type="datetime-local" id="send_at" name="send_at"
                        class="mt-1 block w-full border-gray-300 rounded-md">
                </div>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Planifier</button>
            </form>
        </div>
    </div>
    <script>
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(function() {
                flashMessage.classList.add('opacity-0'); // Ajoute l'animation de disparition
                flashMessage.classList.add('transition-opacity'); // Transition pour l'animation
                flashMessage.classList.add('duration-300'); // Durée de l'animation
            }, 3000); // Attend 3 secondes avant de commencer l'animation
        };
    </script>
</x-app-layout>
