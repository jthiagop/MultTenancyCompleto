<x-tenant-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Postes') }}
            <x-btn-link class="ml-4 float-right" href="{{ route('post.create') }}">Add Postes</x-btn-link>
        </h2>
    </x-slot>
    <div class="py-12">
        @include('__massage')

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Name
                            </th><th scope="col" class="px-6 py-3">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Permição
                            </th>
                            <th scope="col" class="px-6 py-3 text-right">
                                Açôes
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">
                                    {{ $post->id }}

                                </td>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $post->title }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $post->body }}
                                </td>
                                <td class="px-6 py-4">


                                </td>
                                <td class="px-6 py-4 text-right">
                                    <x-btn-link href="{{ route('post.edit', $post->id) }}">Editar</x-btn-link>

                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-tenant-app-layout>
