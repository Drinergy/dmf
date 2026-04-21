@extends('layouts.enrollment')

@section('title', 'Pay Remaining Tuition — DMF Dental Training Center')
@section('meta_description', 'Complete payment for your remaining program tuition.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-brand-600 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to Home
    </a>

    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-2">Pay Remaining Tuition</h1>
        <p class="text-gray-500">Reference <span class="font-mono font-semibold text-brand-700">{{ $enrollment->reference_number }}</span></p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl border border-red-100 bg-red-50 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ $pay_url }}" method="POST" class="flex flex-col lg:flex-row gap-6 items-start">
        @csrf

        <div class="flex-1 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6">
                <h2 class="text-base font-bold text-gray-700 mb-4">Student</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-400">Name</span>
                        <span class="font-medium text-gray-800 text-right">{{ $enrollment->full_name }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-400">Program</span>
                        <span class="font-medium text-gray-800 text-right">{{ $purchasable_name }}</span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-gray-400">Tuition paid to date</span>
                        <span class="font-medium text-gray-800">₱{{ number_format($enrollment->amount_paid_tuition) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6">
                <h2 class="text-base font-bold text-gray-700 mb-4">Choose Payment Method</h2>
                <p class="text-xs text-gray-400 mb-5">You'll be redirected to PayMongo to complete your payment securely.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php
                    $payMethods = [
                        ['value' => 'gcash', 'label' => 'GCash', 'desc' => 'Pay via GCash e-wallet', 'bg' => '#EFF5FF', 'svg' => '<svg viewBox="0 0 32 32" fill="none" class="w-7 h-7"><circle cx="16" cy="16" r="16" fill="#007DFF"/><text x="16" y="21" text-anchor="middle" fill="white" font-size="11" font-weight="bold" font-family="Arial">G</text></svg>'],
                        ['value' => 'paymaya', 'label' => 'PayMaya', 'desc' => 'Pay via Maya e-wallet', 'bg' => '#EDFAF6', 'svg' => '<svg viewBox="0 0 32 32" fill="none" class="w-7 h-7"><circle cx="16" cy="16" r="16" fill="#00B388"/><text x="16" y="21" text-anchor="middle" fill="white" font-size="10" font-weight="bold" font-family="Arial">M</text></svg>'],
                        ['value' => 'shopee_pay', 'label' => 'ShopeePay', 'desc' => 'Pay via ShopeePay wallet', 'bg' => '#FFF2EE', 'svg' => '<svg viewBox="0 0 32 32" fill="none" class="w-7 h-7"><circle cx="16" cy="16" r="16" fill="#EE4D2D"/><text x="16" y="21" text-anchor="middle" fill="white" font-size="8" font-weight="bold" font-family="Arial">SP</text></svg>'],
                        ['value' => 'qrph', 'label' => 'QRPH', 'desc' => 'Scan and pay with QRPH', 'bg' => '#EFF6FF', 'svg' => '<svg viewBox="0 0 32 32" fill="none" class="w-7 h-7"><rect width="32" height="32" rx="16" fill="#1e40af"/><text x="16" y="21" text-anchor="middle" fill="white" font-size="9" font-weight="bold" font-family="Arial">QR</text></svg>'],
                    ];
                    @endphp
                    @foreach($payMethods as $method)
                    <label class="pay-opt block rounded-xl border-2 border-gray-100 p-3.5 bg-white" for="bal-pay-{{ $method['value'] }}">
                        <input type="radio" id="bal-pay-{{ $method['value'] }}" name="payment_method" value="{{ $method['value'] }}" class="sr-only" {{ $method['value'] === 'gcash' ? 'checked' : '' }}>
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center" style="background:{{ $method['bg'] }}">{!! $method['svg'] !!}</span>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $method['label'] }}</p>
                                <p class="text-gray-400 text-xs">{{ $method['desc'] }}</p>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('payment_method')
                    <p class="mt-3 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6">
                <h2 class="text-base font-bold text-gray-700 mb-5">Summary</h2>
                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Remaining tuition</span>
                        <span class="font-semibold text-gray-800">₱{{ number_format($balance_tuition) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Payment processing fee</span>
                        <span class="font-semibold text-gray-800">₱{{ number_format($convenience_fee) }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                        <span class="font-bold text-gray-800">Total</span>
                        <span class="font-extrabold text-brand-700 text-2xl">₱{{ number_format($total_due) }}</span>
                    </div>
                </div>
                <button type="submit" class="flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-accent-500 text-white font-extrabold rounded-xl shadow-md hover:bg-accent-400 hover:text-white transition-all text-base">
                    Pay remaining tuition
                </button>
                <p class="text-[10px] text-gray-400 text-center mt-3">Early-bird pricing applies if you complete this payment on or before the discount end date. After that date, the regular list price applies.</p>
            </div>
        </div>
    </form>
</div>
@endsection
