<x-app-layout>
        <div class="container mx-auto px-4 py-8 max-w-5xl">
            <h1 class="text-2xl font-semibold mb-4 text-gray-100">Editar Conteúdo do E-mail</h1>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-500 text-white rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('email.update') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="greeting" class="block text-lg text-gray-100">Saudação:</label>
                    <textarea name="greeting" id="greeting" rows="4" class="w-full p-3 border border-gray-300 rounded bg-transparent text-gray-100">{{ $content->greeting }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="intro_text" class="block text-lg text-gray-100">Texto de Introdução:</label>
                    <textarea name="intro_text" id="intro_text" rows="6" class="w-full p-3 border border-gray-300 rounded bg-transparent text-gray-100">{{ $content->intro_text }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="closing_text" class="block text-lg text-gray-100">Texto de Fechamento:</label>
                    <textarea name="closing_text" id="closing_text" rows="4" class="w-full p-3 border border-gray-300 rounded bg-transparent text-gray-100">{{ $content->closing_text }}</textarea>
                </div>

                <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded bg-transparent hover:bg-dark-secondary border border-gray-300">Salvar Alterações</button>
            </form>
        </div>
</x-app-layout>
