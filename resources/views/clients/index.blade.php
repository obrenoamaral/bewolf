<x-app-layout>
    <div class="p-10 bg-dark">
        <div class="flex flex-wrap justify-between my-4 container mx-auto max-w-5xl">
            <h1 class="text-2xl text-gray-100 w-full md:w-auto">Clientes</h1>
            <a href="{{ route('clients.export') }}" class="btn btn-primary border border-gray-300 hover:bg-dark-secondary text-gray-100 p-2 rounded w-full md:w-auto text-center">Exportar para Excel</a>
        </div>
        <div class="container max-w-5xl mx-auto overflow-x-auto">
            <table class="min-w-full">
                <thead>
                <tr class="text-gray-100">
                    <th class="p-2 text-left text-gray-100">Nome</th>
                    <th class="p-2 text-left text-gray-100">Empresa</th>
                    <th class="p-2 text-left text-gray-100">E-mail</th>
                    <th class="p-2 text-left text-gray-100">Celular</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($clients as $client)
                    <tr class="border-b border-b-gray-600 hover:bg-[#262626]">
                        <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100 break-words">{{ $client->name }}</td>
                        <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100 break-words">{{ $client->company }}</td>
                        <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100 break-words">{{ $client->email }}</td>
                        <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100">{{ $client->phone }}</td>
                        <td class="p-2 text-center flex gap-2 w-40">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
