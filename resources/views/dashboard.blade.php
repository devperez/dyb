<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    @if (session('refresh'))
        <script>
            window.location.reload();
        </script>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Colonne gauche : Formulaire -->
                <div class="bg-white p-6 rounded-lg shadow">
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
                            <label for="send_at" class="block text-sm font-medium text-gray-700">Date et heure
                                d'envoi</label>
                            <input type="datetime-local" id="send_at" name="send_at"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                            name="action" value="store">Planifier</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-blue-700"
                            name="action" value="post">Poster de suite</button>
                    </form>
                </div>

                <!-- Colonne droite -->
                <div class="bg-gray-100 p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Aperçu des messages</h2>
                    <p class="text-sm text-gray-600 mb-2">Ici, vous pouvez voir un aperçu des messages planifiés ou
                        envoyés.</p>

                    @if ($messages->count() > 0)
                        <div class="overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="min-w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            Message
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Date et Heure d'envoi
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Plateforme
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Statut
                                        </th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @foreach ($messages as $message)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium text-gray-900"
                                                title="{{ $message->content }}">
                                                {{ \Illuminate\Support\Str::limit($message->content, 50) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span id="send_at_{{ $message->id }}" class="date-display"
                                                    style="cursor: {{ $message->status == 'pending' ? 'pointer' : 'default' }}; color: {{ $message->status == 'pending' ? '#1D4ED8' : 'inherit' }};"
                                                    title="{{ $message->status == 'pending' ? 'Cliquer pour éditer' : '' }}"
                                                    onclick="editDate({{ $message->id }})">
                                                    {{ \Carbon\Carbon::parse($message->send_at)->format('d-m-Y H:i') }}
                                                </span>
                                                <!-- Form to update date/time -->
                                                <form id="update-form_{{ $message->id }}" method="POST"
                                                    action="{{ route('scheduledMessages.update', $message->id) }}"
                                                    class="hidden">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="datetime-local" id="send_at_input_{{ $message->id }}"
                                                        name="send_at" style="width:180px"
                                                        value="{{ \Carbon\Carbon::parse($message->send_at)->format('Y-m-d\TH:i') }}"
                                                        class="w-full mt-2">
                                                    <button type="submit"
                                                    class="text-white bg-blue-500 px-2 py-2 rounded-md hover:bg-blue-600 mt-2">Enregistrer</button>
                                                    <button type="button" class="text-white bg-red-500 px-2 py-2 rounded-md hover:bg-red-600""
                                                        onclick="cancelEdit({{ $message->id }})">Annuler</button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4">{{ implode(', ', $message->platforms) }}</td>
                                            <td class="px-6 py-4">
                                                @if ($message->status == 'sent')
                                                    <span class="text-green-500">Envoyé</span>
                                                @else
                                                    <span class="text-yellow-500">En attente</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- Pagination links -->
                            <div class="mt-4 px-2">
                                {{ $messages->links() }}
                            </div>
                        </div>
                    @else
                        <p>Aucun message en attente de publication.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        function editDate(messageId) {
            var dateDisplay = document.getElementById('send_at_' + messageId);
            var input = document.getElementById('send_at_input_' + messageId);
            var form = document.getElementById('update-form_' + messageId);

            // Vérifier que les éléments existent avant de manipuler leurs classes
            if (dateDisplay && input && form) {
                // Show the input and form
                dateDisplay.classList.add('hidden');
                form.classList.remove('hidden');
            } else {
                console.error("Erreur : Impossible de trouver l'élément avec l'ID " + messageId);
            }
        }

        function cancelEdit(messageId) {
            var dateDisplay = document.getElementById('send_at_' + messageId);
            var input = document.getElementById('send_at_input_' + messageId);
            var form = document.getElementById('update-form_' + messageId);

            // Vérification si les éléments existent
            if (dateDisplay && input && form) {
                // Masquer l'input et réafficher la date
                dateDisplay.classList.remove('hidden');
                form.classList.add('hidden');
            } else {
                console.error("Erreur : Impossible de trouver l'élément avec l'ID " + messageId);
            }
        }
    </script>
</x-app-layout>
