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
                            <h1 class="text-3xl font-bold mb-4">Dashboard</h1>
                            <div class="flex justify-between mb-6">
                                <div class="w-1/2 bg-white rounded-lg shadow-md p-6 bg-green-100 ">
                                    <h3 class="text-2xl font-semibold mb-4">Argent disponible</h3>
                                    <p class="text-3xl font-semibold">{{ $availableBalance }}</p>
                                </div>
                                <div class="w-1/2 bg-white rounded-lg shadow-md p-6 ml-3 bg-yellow-100">
                                    <h3 class="text-2xl font-semibold mb-4">Argent en attente</h3>
                                    <p class="text-3xl font-semibold">{{ $pendingBalance }}</p>
                                </div>
                            </div>
                            <h3 class="text-2xl font-semibold mb-4">Payments</h3>
                            <table class="min-w-full bg-white rounded-lg shadow-md">
                                <thead>
                                <tr class="w-full bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">ID</th>
                                    <th class="py-3 px-6 text-left">Amount</th>
                                    <th class="py-3 px-6 text-left">Status</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm font-light">
                                @foreach ($payments as $payment)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left">{{ $payment->id }}</td>
                                        <td class="py-3 px-6 text-left">{{ $payment->amount / 100 }} {{ $payment->currency }}</td>
                                        <td class="py-3 px-6 text-left">
                                            @if(!$payment->refund)
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs {{ $payment->status == 'succeeded' ? 'bg-green-200 text-green-600' : 'bg-yellow-200 text-yellow-600' }}">
                                                {{ $payment->status }}
                                            </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs bg-red-200 text-red-600">
                                                Refunded
                                            </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <a href="{{ route('stripe.payment.show', $payment->id) }}"
                                               class="text-blue-500 hover:underline">Details</a>
                                            @if ($payment->status == 'requires_capture')
                                                <form action="{{ route('stripe.payment.capture', $payment->id) }}"
                                                      method="POST" class="inline-block ml-2">
                                                    @csrf
                                                    <input type="text" name="amount" placeholder="Amount to capture"
                                                           class="border border-gray-300 rounded px-2 py-1 text-sm">
                                                    <button type="submit"
                                                            class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-700">
                                                        Capture
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($payment->status == 'succeeded')
                                                <form action="{{ route('stripe.payment.refund', $payment->id) }}"
                                                      method="POST" class="inline-block ml-2">
                                                    @csrf
                                                    <button type="submit"
                                                            class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-700">
                                                        Refund
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@endsection
