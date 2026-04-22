@extends('layouts.enrollment')

@section('title', 'Enroll — DMF Dental Training Center')
@section('meta_description', 'Fill out the enrollment form for DMF Dental Training Center. Fast and secure.')



@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">

    <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-brand-600 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to Home
    </a>

    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-2">Enrollment Form</h1>
        <p class="text-gray-500">Complete the form below to register and secure your spot.</p>
    </div>

    {{-- Progress --}}
    <div class="flex items-center gap-0 mb-10" role="progressbar" aria-label="Enrollment steps">
        @php
        $steps = [
            ['num' => 1, 'label' => 'Details',  'state' => 'active'],
            ['num' => 2, 'label' => 'Payment',  'state' => 'pending'],
            ['num' => 3, 'label' => 'Confirm',  'state' => 'pending'],
        ];
        @endphp
        @foreach($steps as $i => $step)
        <div class="flex items-center {{ $i < count($steps)-1 ? 'flex-1' : '' }}">
            <div class="flex flex-col items-center gap-1">
                <span class="w-9 h-9 rounded-full border-2 flex items-center justify-center text-sm font-bold transition-all
                             {{ $step['state'] === 'active'  ? 'bg-brand-600 border-brand-600 text-white shadow-md' : 'bg-white border-gray-200 text-gray-400' }}">
                    {{ $step['num'] }}
                </span>
                <span class="text-xs font-medium {{ $step['state'] === 'active' ? 'text-brand-700' : 'text-gray-400' }}">
                    {{ $step['label'] }}
                </span>
            </div>
            @if($i < count($steps)-1)
            <div class="flex-1 h-0.5 bg-gray-100 mx-2 mb-4 rounded-full"></div>
            @endif
        </div>
        @endforeach
    </div>

    <form id="enrollment-form" action="{{ route('enroll.store') }}" method="POST">
        @csrf

        {{-- 1. Personal Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6 md:p-8 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-brand-50 text-brand-600 flex items-center justify-center text-sm">1</span>
                Personal Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-400">*</span></label>
                    <input type="text" name="first_name" class="form-input" value="{{ old('first_name', $oldData['first_name'] ?? '') }}" placeholder="e.g. Juan" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" name="middle_name" class="form-input" value="{{ old('middle_name', $oldData['middle_name'] ?? '') }}" placeholder="e.g. Santos (optional)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Surname <span class="text-red-400">*</span></label>
                    <input type="text" name="surname" class="form-input" value="{{ old('surname', $oldData['surname'] ?? '') }}" placeholder="e.g. Dela Cruz" required>
                </div>
                
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Birthday <span class="text-red-400">*</span></label>
                    <input type="date" name="birthday" class="form-input" value="{{ old('birthday', $oldData['birthday'] ?? '') }}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sex <span class="text-red-400">*</span></label>
                    <select name="sex" class="form-input" required>
                        <option value="" disabled {{ !old('sex', $oldData['sex'] ?? '') ? 'selected' : '' }}>Select Sex</option>
                        <option value="Male" {{ old('sex', $oldData['sex'] ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex', $oldData['sex'] ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- 2. Contact Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6 md:p-8 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-brand-50 text-brand-600 flex items-center justify-center text-sm">2</span>
                Contact & Address
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-400">*</span></label>
                    <input type="tel" name="phone" class="form-input" value="{{ old('phone', $oldData['phone'] ?? '') }}" required placeholder="09XX XXX XXXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-400">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $oldData['email'] ?? '') }}" placeholder="you@example.com" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook / Messenger Name <span class="text-red-400">*</span></label>
                    <input type="text" name="facebook_messenger_name" class="form-input" value="{{ old('facebook_messenger_name', old('facebook', $oldData['facebook_messenger_name'] ?? ($oldData['facebook'] ?? ''))) }}" placeholder="e.g. Juan Dela Cruz" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook / Messenger Link <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="url" name="facebook_messenger_url" class="form-input" value="{{ old('facebook_messenger_url', $oldData['facebook_messenger_url'] ?? '') }}" placeholder="https://www.facebook.com/your.profile">
                </div>

                <div class="sm:col-span-2 mt-4 pt-4 border-t border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm mb-3">Complete Address <span class="text-red-400">*</span></h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <input type="text" name="addr_street" class="form-input" value="{{ old('addr_street', $oldData['addr_street'] ?? '') }}" placeholder="House/Building #, Street Name, Barangay" required>
                        </div>
                        <div class="sm:col-span-2">
                            <input type="text" name="addr_city" class="form-input" value="{{ old('addr_city', $oldData['addr_city'] ?? '') }}" placeholder="City / Municipality" required>
                        </div>
                        <div>
                            <input type="text" name="addr_province" class="form-input" value="{{ old('addr_province', $oldData['addr_province'] ?? '') }}" placeholder="Province" required>
                        </div>
                        <div>
                            <input type="text" name="addr_zip" class="form-input" value="{{ old('addr_zip', $oldData['addr_zip'] ?? '') }}" placeholder="Zip Code" required>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2 mt-4 pt-4 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <h3 class="font-bold text-gray-800 text-sm">Delivery Address <span class="text-gray-400 font-normal">(for Online Reviewees)</span></h3>
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" id="same_address" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <span class="font-medium text-brand-700">Same as Complete Address</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="delivery_fields">
                        <div class="sm:col-span-2">
                            <input type="text" name="deliv_street" class="form-input deliv-input" value="{{ old('deliv_street', $oldData['deliv_street'] ?? '') }}" placeholder="House/Building #, Street Name, Barangay">
                        </div>
                        <div class="sm:col-span-2">
                            <input type="text" name="deliv_city" class="form-input deliv-input" value="{{ old('deliv_city', $oldData['deliv_city'] ?? '') }}" placeholder="City / Municipality">
                        </div>
                        <div>
                            <input type="text" name="deliv_province" class="form-input deliv-input" value="{{ old('deliv_province', $oldData['deliv_province'] ?? '') }}" placeholder="Province">
                        </div>
                        <div>
                            <input type="text" name="deliv_zip" class="form-input deliv-input" value="{{ old('deliv_zip', $oldData['deliv_zip'] ?? '') }}" placeholder="Zip Code">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Academic Background --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6 md:p-8 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-brand-50 text-brand-600 flex items-center justify-center text-sm">3</span>
                Academic Background
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">School <span class="text-red-400">*</span></label>
                    <input type="text" name="school" class="form-input" value="{{ old('school', $oldData['school'] ?? '') }}" placeholder="e.g. University of the Philippines" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year Level <span class="text-red-400">*</span></label>
                    <select name="year_level" class="form-input" required>
                        <option value="" disabled {{ !old('year_level', $oldData['year_level'] ?? '') ? 'selected' : '' }}>Select level</option>
                        @foreach(['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', '6th Year', 'Graduate'] as $level)
                            <option value="{{ $level }}" {{ old('year_level', $oldData['year_level'] ?? '') === $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year Graduated (if applicable)</label>
                    <input type="text" name="year_graduated" class="form-input" value="{{ old('year_graduated', $oldData['year_graduated'] ?? '') }}" placeholder="e.g. 2024">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Taker Status <span class="text-red-400">*</span></label>
                    <select name="taker_status" class="form-input" required>
                        <option value="" disabled {{ !old('taker_status', $oldData['taker_status'] ?? '') ? 'selected' : '' }}>Select status</option>
                        <option value="First taker" {{ old('taker_status', $oldData['taker_status'] ?? '') === 'First taker' ? 'selected' : '' }}>First taker</option>
                        <option value="Re-taker" {{ old('taker_status', $oldData['taker_status'] ?? '') === 'Re-taker' ? 'selected' : '' }}>Re-taker</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- 4. Program Selection --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6 md:p-8 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-brand-50 text-brand-600 flex items-center justify-center text-sm">4</span>
                Program Selection
            </h2>

            @error('program')
                <p class="mb-4 text-xs text-red-600">{{ $message }}</p>
            @enderror
            
            <div class="space-y-8">
                @if(isset($packages) && $packages->count() > 0)
                <div>
                    <h3 class="text-md font-bold text-gray-700 border-b border-gray-100 pb-2 mb-4">Review Packages</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($packages as $pkg)
                        @php
                            $isEarlyBirdActive = $pkg->isEarlyBirdActive();
                            $activeFullPrice = $isEarlyBirdActive ? $pkg->price_early : $pkg->price_full;
                        @endphp
                        <label class="program-opt relative flex flex-col h-full rounded-xl border-2 border-gray-100 p-4 bg-white" for="prog-{{ $pkg->slug }}">
                            <input type="radio" id="prog-{{ $pkg->slug }}" name="program" value="{{ $pkg->slug }}" class="sr-only" required
                                   data-kind="package"
                                   data-full="{{ $activeFullPrice }}" data-dp="{{ $pkg->downpayment_amount }}"
                                   data-schedules='[]'
                                   {{ old('program', $oldData['program'] ?? (request('program') === $pkg->slug ? $pkg->slug : '')) === $pkg->slug ? 'checked' : '' }}>

                            <p class="font-bold text-brand-900 text-sm mb-2 leading-tight pr-4">{{ $pkg->name }}</p>

                            @if($pkg->programs->count() > 0)
                                <ul class="text-gray-500 text-[11px] mb-4 leading-snug space-y-1 flex-1">
                                    @foreach($pkg->programs as $incProgram)
                                        <li class="flex items-start gap-1"><span class="text-brand-400">•</span> <span>{{ $incProgram->name }}</span></li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="flex items-start gap-4 border-t border-gray-50 pt-3 mt-auto">
                                <div class="flex-1">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Full Price</p>
                                    @if($isEarlyBirdActive)
                                        <p class="font-bold text-gray-800 text-sm">
                                            <span class="line-through text-gray-400 font-normal text-[11px] mr-1">₱{{ number_format($pkg->price_full) }}</span>
                                            ₱{{ number_format($pkg->price_early) }}
                                        </p>
                                        <p class="text-[9px] text-accent-600 font-bold bg-accent-50 px-1 py-0.5 rounded inline-block mt-0.5 uppercase tracking-wide">Early Bird Applied!</p>
                                    @else
                                        <p class="font-bold text-gray-800 text-sm">₱{{ number_format($pkg->price_full) }}</p>
                                    @endif
                                </div>
                                <div class="pl-4 border-l border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Downpayment</p>
                                    <p class="font-bold text-brand-600 text-sm">₱{{ number_format($pkg->downpayment_amount) }}</p>
                                </div>
                            </div>

                            <span class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-200 flex items-center justify-center program-check transition-all">
                                <svg class="w-3 h-3 text-brand-600 opacity-0 program-check-icon transition-all" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                @foreach($programCategories as $categoryName => $programs)
                <div>
                    <h3 class="text-md font-bold text-gray-700 border-b border-gray-100 pb-2 mb-4">{{ $categoryName }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($programs as $prog)
                        @php
                            $isEarlyBirdActive = isset($prog->early_deadline) && now()->timezone('Asia/Manila')->startOfDay() <= \Carbon\Carbon::parse($prog->early_deadline);
                            $activeFullPrice = $isEarlyBirdActive ? $prog->price_early : $prog->price_full;
                        @endphp
                        <label class="program-opt relative flex flex-col h-full rounded-xl border-2 border-gray-100 p-4 bg-white" for="prog-{{ $prog->slug }}">
                            <input type="radio" id="prog-{{ $prog->slug }}" name="program" value="{{ $prog->slug }}" class="sr-only" required
                                   data-kind="program"
                                   data-full="{{ $activeFullPrice }}" data-dp="{{ $prog->downpayment_amount }}"
                                   data-schedules='@json($prog->schedules->map(fn($s) => ["id" => $s->id, "label" => $s->label, "mode" => $s->mode]))'
                                   {{ old('program', $oldData['program'] ?? (request('program') === $prog->slug ? $prog->slug : '')) === $prog->slug ? 'checked' : '' }}>
                            
                            <p class="font-bold text-brand-900 text-sm mb-2 leading-tight pr-4">{{ $prog->name }}</p>

                            {{-- Batch summary (from schedules) --}}
                            @php
                                $activeSchedules = $prog->schedules ?? collect();
                                $firstSchedule = $activeSchedules->first();
                            @endphp
                            @if($activeSchedules->count() > 0 && $firstSchedule)
                                <div class="text-[11px] text-gray-600 mb-3 leading-snug space-y-1.5 flex-1">
                                    <div class="flex items-start gap-1">
                                        <span class="text-brand-400">•</span>
                                        <span><span class="font-semibold text-gray-700">Batch:</span> {{ $firstSchedule->label }}</span>
                                    </div>
                                    @if(!empty($firstSchedule->mode))
                                        <div class="flex items-start gap-1">
                                            <span class="text-brand-400">•</span>
                                            <span><span class="font-semibold text-gray-700">Mode:</span> {{ $firstSchedule->mode }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($firstSchedule->slots))
                                        <div class="flex items-start gap-1">
                                            <span class="text-brand-400">•</span>
                                            <span><span class="font-semibold text-gray-700">Max Capacity:</span> {{ $firstSchedule->slots }} students</span>
                                        </div>
                                    @endif
                                    @if($activeSchedules->count() > 1)
                                        <div class="flex items-start gap-1">
                                            <span class="text-brand-400">•</span>
                                            <span class="text-gray-500">
                                                <span class="font-semibold text-gray-700">{{ $activeSchedules->count() }}</span> batches available — select your batch below.
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-start gap-4 border-t border-gray-50 pt-3 mt-auto">
                                <div class="flex-1">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Full Price</p>
                                    @if($isEarlyBirdActive)
                                        <p class="font-bold text-gray-800 text-sm">
                                            <span class="line-through text-gray-400 font-normal text-[11px] mr-1">₱{{ number_format($prog->price_full) }}</span>
                                            ₱{{ number_format($prog->price_early) }}
                                        </p>
                                        <p class="text-[9px] text-accent-600 font-bold bg-accent-50 px-1 py-0.5 rounded inline-block mt-0.5 uppercase tracking-wide">Early Bird Applied!</p>
                                    @else
                                        <p class="font-bold text-gray-800 text-sm">₱{{ number_format($prog->price_full) }}</p>
                                    @endif
                                </div>
                                <div class="pl-4 border-l border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Downpayment</p>
                                    <p class="font-bold text-brand-600 text-sm">₱{{ number_format($prog->downpayment_amount) }}</p>
                                </div>
                            </div>
                            
                            {{-- Check indicator --}}
                            <span class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-200 flex items-center justify-center program-check transition-all">
                                <svg class="w-3 h-3 text-brand-600 opacity-0 program-check-icon transition-all" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- 5. Select batch (if applicable) --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6 md:p-8 mb-6" id="schedule-section" style="display:none;">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-brand-50 text-brand-600 flex items-center justify-center text-sm">5</span>
                Select batch (if applicable)
            </h2>

            <label class="block text-sm font-medium text-gray-700 mb-1">Select Batch</label>
            <select name="schedule_id" id="schedule_id" class="form-input" data-old="{{ old('schedule_id', $oldData['schedule_id'] ?? '') }}">
                <option value="">Select a batch</option>
            </select>
            @error('schedule_id')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- 6. Payment Type Option --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-6 md:p-8 mb-8" id="payment-options-section" style="display: none;">
            <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="w-6 h-6 rounded bg-brand-50 text-brand-600 flex items-center justify-center text-sm">6</span>
                Payment Preference
            </h2>
            <p class="text-sm text-gray-500 mb-4">Choose how you would like to settle your balance today.</p>

            @error('payment_type')
                <p class="mb-4 text-xs text-red-600">{{ $message }}</p>
            @enderror
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="pay-type-opt relative flex flex-col items-center justify-center rounded-xl border-2 border-gray-100 p-5 bg-white text-center" for="pay-full">
                    <input type="radio" id="pay-full" name="payment_type" value="full" class="sr-only" required
                           {{ old('payment_type', $oldData['payment_type'] ?? '') === 'full' ? 'checked' : '' }}>
                    <p class="font-bold text-gray-800 mb-1">Full Payment</p>
                    <p class="text-2xl font-extrabold text-brand-600" id="lbl-full-price">₱0</p>
                </label>
                
                <label class="pay-type-opt relative flex flex-col items-center justify-center rounded-xl border-2 border-gray-100 p-5 bg-white text-center" for="pay-dp">
                    <input type="radio" id="pay-dp" name="payment_type" value="downpayment" class="sr-only" required
                           {{ old('payment_type', $oldData['payment_type'] ?? '') === 'downpayment' ? 'checked' : '' }}>
                    <p class="font-bold text-gray-800 mb-1">Downpayment</p>
                    <p class="text-2xl font-extrabold text-brand-600" id="lbl-dp-price">₱0</p>
                </label>
            </div>
        </div>

        <div class="space-y-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" required class="mt-0.5 w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-gray-500">I agree that all information provided is true and correct to the best of my knowledge.</span>
            </label>

            <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-accent-500 text-brand-950 font-extrabold text-lg rounded-xl shadow-[0_4px_14px_0_rgba(250,178,27,0.39)] hover:bg-accent-400 hover:shadow-[0_6px_20px_rgba(250,178,27,0.23)] active:scale-[0.99] transition-all duration-200">
                Proceed to Payment
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/enrollment-form.js') }}"></script>
@endsection
