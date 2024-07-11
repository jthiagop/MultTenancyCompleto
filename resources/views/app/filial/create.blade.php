<x-tenant-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tenants') }}
            <x-btn-link class="ml-4 float-right" href="{{ route('filial.index') }}">Voltar</x-btn-link>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('filial.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Filial -->
                        <div class="mt-4">
                            <x-input-label for="name" :value="__('Filial')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- CEP -->
                        <div class="mt-4">
                            <x-input-label for="cep" :value="__('CEP')" />
                            <x-text-input id="cep" class="block mt-1 w-full" type="text" name="cep"
                                :value="old('cep')" autocomplete="postal-code" />
                            <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                        </div>

                        <!-- Rua -->
                        <div class="mt-4">
                            <x-input-label for="rua" :value="__('Rua')" />
                            <x-text-input id="rua" class="block mt-1 w-full" type="text" name="rua"
                                :value="old('rua')" autocomplete="street-address" />
                            <x-input-error :messages="$errors->get('rua')" class="mt-2" />
                        </div>

                        <!-- Número -->
                        <div class="mt-4">
                            <x-input-label for="numero" :value="__('Número')" />
                            <x-text-input id="numero" class="block mt-1 w-full" type="text" name="numero"
                                :value="old('numero')" />
                            <x-input-error :messages="$errors->get('numero')" class="mt-2" />

                            <!-- Cidade -->
                            <div class="mt-4">
                                <x-input-label for="cidade" :value="__('Cidade')" />
                                <x-text-input id="cidade" class="block mt-1 w-full" type="text" name="cidade"
                                    :value="old('cidade')" />
                                <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                            </div>

                            <!-- Bairro -->
                            <div class="mt-4">
                                <x-input-label for="bairro" :value="__('Bairro')" />
                                <x-text-input id="bairro" class="block mt-1 w-full" type="text" name="bairro"
                                    :value="old('bairro')" />
                                <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                            </div>

                            <!-- UF -->
                            <div class="mt-4">
                                <x-input-label for="uf" :value="__('UF')" />
                                <x-text-input id="uf" class="block mt-1 w-full" type="text" name="uf"
                                    :value="old('uf')" />
                                <x-input-error :messages="$errors->get('uf')" class="mt-2" />
                            </div>

                            <!-- Complemento -->
                            <div class="mt-4">
                                <x-input-label for="complemento" :value="__('Complemento')" />
                                <x-text-input id="complemento" class="block mt-1 w-full" type="text"
                                    name="complemento" :value="old('complemento')" />
                                <x-input-error :messages="$errors->get('complemento')" class="mt-2" />
                            </div>

                            <div class="mt-4">

                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                    for="file_input">Upload file</label>
                                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="photo" name="photo" type="file">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">SVG, PNG,
                                    JPG or GIF (MAX. 800x400px).</p>

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
