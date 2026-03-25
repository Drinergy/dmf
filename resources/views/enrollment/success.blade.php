@extends('layouts.enrollment')

@section('title', 'Enrollment Successful — DMF Dental Training Center')
@section('meta_description', 'Your enrollment at DMF Dental Training Center is confirmed. Check your email for details.')



@section('content')


{{-- ── Progress Indicator ── --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
    <div class="flex items-center gap-0 mb-10">
        @php
        $steps = [
            ['num' => 1, 'label' => 'Details', 'state' => 'done'],
            ['num' => 2, 'label' => 'Payment', 'state' => 'done'],
            ['num' => 3, 'label' => 'Confirm', 'state' => 'active'],
        ];
        @endphp
        @foreach($steps as $i => $step)
        <div class="flex items-center {{ $i < count($steps)-1 ? 'flex-1' : '' }}">
            <div class="flex flex-col items-center gap-1">
                <span class="w-9 h-9 rounded-full border-2 flex items-center justify-center text-sm font-bold
                             {{ $step['state'] === 'active' ? 'bg-brand-600 border-brand-600 text-white shadow-md' : '' }}
                             {{ $step['state'] === 'done'   ? 'bg-brand-100 border-brand-300 text-brand-700' : '' }}">
                    @if($step['state'] === 'done')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </span>
                <span class="text-xs font-medium {{ $step['state'] === 'active' ? 'text-brand-700' : 'text-gray-400' }}">{{ $step['label'] }}</span>
            </div>
            @if($i < count($steps)-1)
            <div class="flex-1 h-0.5 bg-brand-300 mx-2 mb-4 rounded-full"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>


{{-- ── Main success card ── --}}
<div class="max-w-2xl mx-auto px-4 sm:px-6 pb-16">
    <div class="success-card bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden">

        {{-- Celebration header --}}
        <div class="relative bg-gradient-to-br from-brand-600 to-brand-800 px-8 py-12 text-center overflow-hidden">

            {{-- Decorative bg circles --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/3"></div>

            {{-- Animated success icon --}}
            <div class="relative inline-flex items-center justify-center mb-6">
                {{-- Ripple rings --}}
                <span class="absolute w-24 h-24 rounded-full bg-white/20 ripple-ring"></span>
                <span class="absolute w-20 h-20 rounded-full bg-white/15 ripple-ring" style="animation-delay:0.4s"></span>

                {{-- Check circle --}}
                <span class="check-circle relative z-10 w-16 h-16 rounded-full bg-white flex items-center justify-center shadow-lg">
                    <svg class="w-9 h-9 text-brand-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2 relative z-10">
                Enrollment Successful!
            </h1>
            <p class="text-brand-100/80 relative z-10 text-base">
                Welcome to DMF Dental Training Center, {{ $enrollment->first_name }}! 🎉
            </p>
        </div>


        {{-- Body: enrollment summary --}}
        <div class="px-6 sm:px-8 py-8 space-y-6">

            {{-- Reference number badge --}}
            <div class="flex items-center justify-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-brand-50 border border-brand-100 rounded-full text-sm">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    <span class="text-gray-500">Reference No.</span>
                    <span class="font-mono font-bold text-brand-700 tracking-wider">{{ $enrollment->reference_number }}</span>
                </div>
            </div>

            {{-- Detail rows --}}
            <div class="rounded-2xl border border-gray-100 divide-y divide-gray-50 overflow-hidden">
                @php
                $details = [
                    ['label' => 'Full Name',     'value' => $enrollment->full_name, 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['label' => 'Program',       'value' => $program->name,         'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['label' => 'Payment Type',  'value' => $enrollment->payment_type === 'downpayment' ? 'Downpayment' : 'Full Payment', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['label' => 'Amount Paid',   'value' => '₱' . number_format($enrollment->total_amount), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['label' => 'Date Enrolled', 'value' => $enrollment->created_at->timezone('Asia/Manila')->format('F j, Y'), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                if(!empty($schedule)) {
                    array_splice($details, 2, 0, [[
                        'label' => 'Batch',
                        'value' => trim($schedule->label . (!empty($schedule->mode) ? " ({$schedule->mode})" : '')),
                        'icon'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    ]]);
                }
                if($enrollment->email) {
                    array_splice($details, 1, 0, [['label' => 'Email', 'value' => $enrollment->email, 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z']]);
                }
                if($enrollment->phone) {
                    array_splice($details, 2, 0, [['label' => 'Phone', 'value' => $enrollment->phone, 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z']]);
                }
                @endphp

                @foreach($details as $detail)
                <div class="flex items-center gap-3 px-5 py-3.5 bg-white">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $detail['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="flex-1 flex justify-between items-center min-w-0">
                        <span class="text-sm text-gray-400 flex-shrink-0 mr-4">{{ $detail['label'] }}</span>
                        <span class="font-semibold text-gray-800 text-sm text-right truncate">{{ $detail['value'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Email confirmation notice --}}
            <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-100 rounded-xl text-sm text-amber-800">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="font-semibold text-amber-700">Check Your Email</p>
                    <p class="text-amber-600/80 mt-0.5">A confirmation email with your enrollment details and next steps has been sent to your registered email address. Please check your spam/junk folder if you don't see it.</p>
                </div>
            </div>

            {{-- Next steps --}}
            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-3">What happens next?</h3>
                <div class="space-y-2.5">
                    @php
                    $nextSteps = [
                        ['step' => '1', 'text' => 'Our team will verify your enrollment within 24 hours.'],
                        ['step' => '2', 'text' => 'We will send a detailed confirmation to your registered email address.'],
                        ['step' => '3', 'text' => 'Join your first session on your scheduled date. Good luck!'],
                    ];
                    @endphp
                    @foreach($nextSteps as $ns)
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">{{ $ns['step'] }}</span>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $ns['text'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <a href="{{ url('/') }}"
                   id="back-home-btn"
                   class="flex-1 flex items-center justify-center gap-2 px-5 py-3 bg-brand-600 text-white font-semibold rounded-xl shadow-sm hover:bg-brand-700 transition-all duration-200 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Back to Home
                </a>
                <a href="{{ url('/enroll') }}"
                   id="enroll-another-btn"
                   class="flex-1 flex items-center justify-center gap-2 px-5 py-3 bg-white text-brand-700 font-semibold rounded-xl border border-brand-100 hover:border-brand-300 hover:bg-brand-50 transition-all duration-200 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Enroll Another Person
                </a>
            </div>

        </div>

        {{-- Footer note --}}
        <div class="border-t border-gray-100 px-8 py-4 text-center">
            <p class="text-xs text-gray-400">
                Questions? <a href="tel:+6329973580654" class="text-brand-600 hover:underline font-medium">+63 997 358 0654</a>
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/success-confetti.js') }}"></script>
@endsection
