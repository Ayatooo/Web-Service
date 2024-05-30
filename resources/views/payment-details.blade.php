@extends('welcome')

@section('content')
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-gray-50 text-gray-900">
                        <div class="container mx-auto">
                            <h1 class="text-3xl font-bold mb-4">Payment Details</h1>
                            <p class="text-lg mb-2"><span class="font-semibold">ID:</span> {{ $payment->id }}</p>
                            <p class="text-lg mb-2"><span
                                    class="font-semibold">Amount:</span> {{ $payment->amount / 100 }} {{ $payment->currency }}
                            </p>
                            <p class="text-lg mb-2"><span class="font-semibold">Status:</span>
                                @if($payment->refund)
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-200 text-red-600">
                                        Refunded
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-full text-xs {{ $payment->status === 'succeeded' ? 'bg-green-200 text-green-600' : 'bg-yellow-200 text-yellow-600' }}">
                                    {{ $payment->status }}
                                </span>
                                @endif
                            </p>
                            <p class="text-lg mb-6"><span class="font-semibold">Email:</span> {{ $email }}</p>
                            @if($canRenew)
                                <form id="renew-form" class="mb-3" action="{{ route('stripe.renew', $payment->id) }}" method="POST" onsubmit="return confirmRenewal()">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">Renouveler la caution</button>
                                </form>
                            @endif
                            <a href="{{ route('stripe.dashboard') }}"
                               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
    <script>
        function confirmRenewal() {
            const confirmation = prompt("Veuillez saisir 'CONFIRMER' pour renouveler la caution:");
            return confirmation === 'CONFIRMER';
        }
    </script>
@endsection
