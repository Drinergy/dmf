@extends('layouts.enrollment')

@section('title', 'Payment Cancelled — DMF Dental Training Center')
@section('meta_description', 'Your payment was cancelled. You can return to enrollment and try again anytime.')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <div class="flex items-center gap-0 mb-10">
        @php
        $steps = [
            ['num' => 1, 'label' => 'Details', 'state' => 'done'],
            ['num' => 2, 'label' => 'Payment', 'state' => 'active'],
            ['num' => 3, 'label' => 'Confirm', 'state' => 'pending'],
        ];
        @endphp
        @foreach($steps as $i => $step)
        <div class="flex items-center {{ $i < count($steps)-1 ? 'flex-1' : '' }}">
            <div class="flex flex-col items-center gap-1">
                <span class="w-9 h-9 rounded-full border-2 flex items-center justify-center text-sm font-bold
                             {{ $step['state'] === 'active' ? 'bg-amber-500 border-amber-500 text-white shadow-md' : '' }}
                             {{ $step['state'] === 'done' ? 'bg-brand-100 border-brand-300 text-brand-700' : '' }}
                             {{ $step['state'] === 'pending' ? 'bg-white border-gray-200 text-gray-400' : '' }}">
                    @if($step['state'] === 'done')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    @else
                    {{ $step['num'] }}
                    @endif
                </span>
                <span class="text-xs font-medium {{ $step['state'] === 'active' ? 'text-amber-600' : 'text-gray-400' }}">{{ $step['label'] }}</span>
            </div>
            @if($i < count($steps)-1)
            <div class="flex-1 h-0.5 {{ $step['state'] === 'done' ? 'bg-brand-300' : 'bg-gray-100' }} mx-2 mb-4 rounded-full"></div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden">
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 px-8 py-10 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20 mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Payment Cancelled</h1>
            <p class="text-amber-100">Your payment session was not completed.</p>
        </div>

        <div class="px-6 sm:px-8 py-8 space-y-5">
            <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-sm text-amber-800">
                No charge was made. If this was unintentional, you may go back and retry your payment.
            </div>
            @if(!empty($referenceNumber))
                <div class="flex items-center justify-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-brand-50 border border-brand-100 rounded-full text-sm">
                        <span class="text-gray-500">Reference No.</span>
                        <span class="font-mono font-bold text-brand-700 tracking-wider">{{ $referenceNumber }}</span>
                    </div>
                </div>
            @endif

            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-2">What you can do next</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <span class="w-5 h-5 mt-0.5 rounded-full bg-brand-100 text-brand-700 text-xs font-bold flex items-center justify-center">1</span>
                        Return to the payment page and try again.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-5 h-5 mt-0.5 rounded-full bg-brand-100 text-brand-700 text-xs font-bold flex items-center justify-center">2</span>
                        Choose a different payment method if needed.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-5 h-5 mt-0.5 rounded-full bg-brand-100 text-brand-700 text-xs font-bold flex items-center justify-center">3</span>
                        Contact support if the issue continues.
                    </li>
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <a href="{{ url('/enroll/payment') }}"
                   class="flex-1 flex items-center justify-center gap-2 px-5 py-3 bg-brand-600 text-white font-semibold rounded-xl shadow-sm hover:bg-brand-700 transition-all duration-200 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3m0 0a3 3 0 006 0m-6 0H5m14 0h-4m-7 8h8a4 4 0 004-4V9a4 4 0 00-4-4H8a4 4 0 00-4 4v6a4 4 0 004 4z"/>
                    </svg>
                    Retry Payment
                </a>
                <a href="{{ url('/enroll') }}"
                   class="flex-1 flex items-center justify-center gap-2 px-5 py-3 bg-white text-brand-700 font-semibold rounded-xl border border-brand-100 hover:border-brand-300 hover:bg-brand-50 transition-all duration-200 text-sm">
                    Back to Enrollment
                </a>
            </div>
        </div>

        <div class="border-t border-gray-100 px-8 py-4 text-center">
            <p class="text-xs text-gray-400">
                Need help? <a href="tel:+6329973580654" class="text-brand-600 hover:underline font-medium">+63 997 358 0654</a>
            </p>
        </div>
    </div>
</div>
@endsection
