<x-tenant-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Editar') }}
            <x-btn-link class="ml-4 float-right" href="{{ route('post.index') }}">Voltar</x-btn-link>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('post.update', $post->id) }}">
                        @csrf
                        @method('put')
                        <!-- Name -->
                        <div>
                            <x-input-label for="title" :value="__('Titulo')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" value="{{ $post->title }}" name="title"
                                required autofocus autocomplete="title" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="body" :value="__('Conteúdo')" />
                            <x-text-input id="body" class="block mt-1 w-full" type="text" name="body" value="{{ $post->body }}" col="30"
                                rows="5" required autofocus  />
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        </div>


                        <div class="flex items-center justify-end mt-4">

                            <x-primary-button class="ms-4">
                                {{ __('Registrar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tenant-app-layout>
