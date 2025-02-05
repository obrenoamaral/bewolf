<x-app-layout>
    <div class="pt-10">
        <div class="flex justify-between my-4 container mx-auto max-w-5xl">
            <h1 class="text-2xl text-gray-100">Clientes</h1>
        </div>
        <table class="container max-w-5xl mx-auto overflow-hidden">
            <thead>
            <tr class="text-gray-100">
                <th class=" p-2 text-left text-gray-100">Nome</th>
                <th class=" p-2 text-left text-gray-100">Empresa</th>
                <th class=" p-2 text-left text-gray-100">E-mail</th>
                <th class=" p-2 text-left text-gray-100">Celular</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($clients as $client)
                <tr class=" border-b border-b-gray-600 hover:bg-[#262626]">
                    <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100 w-52">{{ $client->name }}</td>
                    <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100 w-72">{{ $client->company }}</td>
                    <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100 w-52">{{ $client->email }}</td>
                    <td class="border-b border-b-gray-600 p-2 font-medium rounded-bl-lg text-gray-100 w-20">{{ $client->phone }}</td>
                    <td class="p-2 text-center flex gap-2 w-40">
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
