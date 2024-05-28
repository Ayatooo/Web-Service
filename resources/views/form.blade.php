@extends('welcome')

@section('content')
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Commander') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-none overflow-hidden sm:rounded-lg">
                    <div class="container mx-auto text-center align-middle">
                        <div class="bg-white rounded-lg p-8 inline-block">
                            <h1 class="text-3xl font-bold mb-6">Commander un article</h1>
                            <hr class="mb-6">
                            <form action="{{ route('form.submit') }}" method="post" class="max-w-md mx-auto">
                                @csrf
                                <div class="mb-4">
                                    <label for="lastname" class="block text-gray-700 text-sm font-bold mb-2">Nom</label>
                                    <input type="text" name="lastname" id="lastname"
                                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                           placeholder="Entrez votre nom">
                                </div>
                                <div class="mb-4">
                                    <label for="firstname" class="block text-gray-700 text-sm font-bold mb-2">Prénom</label>
                                    <input type="text" name="firstname" id="firstname"
                                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                           placeholder="Entrez votre prénom">
                                </div>
                                <div class="mb-4">
                                    <label for="age" class="block text-gray-700 text-sm font-bold mb-2">Age</label>
                                    <input type="number" name="age" id="age"
                                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                           placeholder="Entrez votre âge">
                                </div>
                                <div class="mb-4">
                                    <label for="city" class="block text-gray-700 text-sm font-bold mb-2">Ville</label>
                                    <select name="city" id="city"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        @foreach($cities as $city)
                                            <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-6">
                                    <label for="vehicle" class="block text-gray-700 text-sm font-bold mb-2">Véhicule</label>
                                    <select name="vehicle" id="vehicle"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        @foreach($cars as $car)
                                            <option value="{{ $car['id'] }}">{{ $car['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="token" value="ca6de466-69a0-4d85-a16b-e5d955eef655">
                                <div>
                                    <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        Envoyer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@endsection
