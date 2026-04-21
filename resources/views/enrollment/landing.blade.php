@extends('layouts.enrollment')

@section('title', 'DMF Dental Training Center — Dentistry Board Exam Review Program')
@section('meta_description', 'Enroll in our Dentistry Board Exam Review Program. High passing rates, expert faculty, and flexible online & face-to-face schedules.')



@section('content')

{{-- ════════════════════════════════════════
    HERO SECTION
════════════════════════════════════════ --}}
<section class="hero-gradient relative overflow-hidden">

    {{-- Decorative blobs --}}
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-16 -left-16 w-72 h-72 bg-accent-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 lg:py-36">
        <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">

            {{-- Left: Copy --}}
            <div class="flex-1 text-center lg:text-left">

                {{-- Badge --}}
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-800/50 text-accent-300 text-xs font-semibold uppercase tracking-widest mb-6 border border-brand-700 backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent-500 animate-pulse"></span>
                    2026 Enrollment Now Open
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight mb-6">
                    Your Pathway to
                    <span class="text-accent-400 relative">
                        Dental Excellence
                        <svg class="absolute -bottom-1 left-0 w-full text-accent-600/50" viewBox="0 0 200 8" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <path d="M2 6 Q50 2 100 4 Q150 6 198 2" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none" opacity="0.6"/>
                        </svg>
                    </span>
                </h1>

                <p class="text-lg text-brand-100 font-light leading-relaxed max-w-xl mx-auto lg:mx-0 mb-8 opacity-90">
                    Join thousands of successful dentists who trusted DMF Dental Training Center to guide them to success in the dentistry boards.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                    <a href="{{ url('/enroll') }}"
                       id="hero-enroll-btn"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-accent-500 text-brand-950 font-extrabold rounded-xl shadow-[0_4px_14px_0_rgba(250,178,27,0.39)] hover:bg-accent-400 hover:shadow-[0_6px_20px_rgba(250,178,27,0.23)] active:scale-[0.98] transition-all duration-200 text-base">
                        Enroll Now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="#programs"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-brand-800/40 text-white font-semibold rounded-xl border border-brand-700 backdrop-blur-sm hover:bg-brand-700 transition-all duration-200 text-base">
                        View Programs
                    </a>
                </div>

                {{-- Social proof --}}
                <div class="mt-8 flex items-center justify-center lg:justify-start gap-3">
                    <div class="flex -space-x-2">
                        @foreach(['bg-pink-400','bg-purple-400','bg-amber-400','bg-teal-400'] as $color)
                        <span class="w-8 h-8 rounded-full {{ $color }} ring-2 ring-white flex items-center justify-center text-white text-xs font-bold">
                            {{ chr(rand(65,90)) }}
                        </span>
                        @endforeach
                    </div>
                    <p class="text-sm text-brand-200">
                        <span class="font-semibold text-white">2,400+</span> graduates this year
                    </p>
                </div>
            </div>

            {{-- Right: Stats card --}}
            <div class="flex-shrink-0 w-full max-w-sm lg:max-w-xs">
                <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6 space-y-5">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-widest">Why DMF Dental?</h3>

                    @php
                    $stats = [
                        ['value' => '94%',  'label' => 'National Passing Rate', 'icon' => '🏆'],
                        ['value' => '15+',  'label' => 'Years of Excellence',    'icon' => '📅'],
                        ['value' => '50+',  'label' => 'Expert Instructors',     'icon' => '👨‍⚕️'],
                        ['value' => '100%', 'label' => 'Satisfaction Guarantee', 'icon' => '✅'],
                    ];
                    @endphp

                    @foreach($stats as $stat)
                    <div class="flex items-center gap-4">
                        <span class="text-2xl">{{ $stat['icon'] }}</span>
                        <div>
                            <p class="text-2xl font-extrabold text-brand-600 stat-number leading-none">{{ $stat['value'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>





{{-- ════════════════════════════════════════
    FEATURED PROGRAMS SECTION
════════════════════════════════════════ --}}
<section id="programs" class="scroll-mt-20 py-16 md:py-24 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-12">
            <span class="text-brand-600 text-sm font-semibold uppercase tracking-widest">Highly Recommended</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 mb-4 tracking-tight">Featured Review Packages</h2>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto">Our most popular bundles offering complete preparation. Looking for individual subjects or practical-only courses? View all programs on our enrollment page.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                // Display only the first 3 packages from the main category
                $topPackages = ($packages ?? collect())->take(3);
            @endphp
            
            @foreach($topPackages as $package)
            @php
                $isEarlyBirdActive = $package->isEarlyBirdActive();
            @endphp
            <div class="relative program-card rounded-2xl border border-gray-100 shadow-soft bg-white p-6 flex flex-col h-full hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group">
                <div class="mb-5">
                    @if($package->tag)
                        <span class="inline-block px-3 py-1 bg-brand-50 text-brand-700 text-[11px] font-bold rounded-full mb-3 uppercase tracking-wider">{{ $package->tag }}</span>
                    @endif
                    <h4 class="text-xl font-extrabold text-gray-900 leading-tight mb-5">{{ $package->name }}</h4>
                    
                    <div class="p-4 bg-gray-50 rounded-xl mb-4 group-hover:bg-brand-50 transition-colors duration-300">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1 font-semibold">Full Price</p>
                        @if($isEarlyBirdActive)
                            <p class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <span class="line-through text-gray-400 font-normal text-xs">₱{{ number_format($package->price_full) }}</span>
                                <span class="text-2xl text-accent-600 font-extrabold">₱{{ number_format($package->price_early) }}</span>
                            </p>
                            <span class="text-[10px] text-white font-bold bg-accent-500 px-1.5 py-0.5 rounded shadow-sm inline-block mt-2 uppercase tracking-wide">Early Bird Active!</span>
                        @else
                            <p class="text-2xl font-extrabold text-gray-900">₱{{ number_format($package->price_full) }}</p>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between text-sm px-1">
                        <span class="text-gray-500 font-medium">Downpayment</span>
                        <span class="font-bold text-gray-900">₱{{ number_format($package->downpayment_amount) }}</span>
                    </div>
                </div>

                <ul class="space-y-3.5 flex-1 mb-8 mt-4 text-sm text-gray-600 border-t border-gray-100 pt-5 pr-2">
                    @foreach($package->programs as $incProgram)
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="leading-snug font-medium">{{ $incProgram->name }}</span>
                    </li>
                    @endforeach
                </ul>

                <a href="{{ url('/enroll') }}?program={{ $package->slug }}" class="mt-auto block text-center px-6 py-3.5 rounded-xl text-sm font-bold transition-all duration-200 bg-white border-2 border-brand-100 text-brand-700 hover:border-brand-600 hover:bg-brand-600 hover:text-white active:scale-[0.98]">
                    Select Package
                </a>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12 md:mt-16">
            <p class="text-gray-500 mb-5 text-sm md:text-base">Looking for individual subjects, practicals, or online-only courses?</p>
            <a href="{{ url('/enroll') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-brand-950 text-white font-bold rounded-xl shadow-md hover:bg-brand-800 hover:shadow-lg transition-all text-sm md:text-base group">
                View All Programs & Enroll
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

    </div>
</section>

{{-- ════════════════════════════════════════
    WHY CHOOSE US
════════════════════════════════════════ --}}
<section class="py-20 md:py-28 bg-white border-y border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-accent-600 text-sm font-semibold uppercase tracking-widest">The DMF Advantage</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 mb-4 tracking-tight">Why Choose DMF Dental?</h2>
            <p class="text-lg text-gray-500">We provide the most comprehensive, intensive, and results-driven training programs in the country to ensure your success.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- Feature 1 -->
            <div class="flex flex-col items-center text-center group">
                <div class="w-16 h-16 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-brand-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-3">Expert Lecturers</h4>
                <p class="text-base text-gray-500 leading-relaxed">Learn directly from seasoned professionals with years of active practice and unparalleled teaching experience.</p>
            </div>
            <!-- Feature 2 -->
            <div class="flex flex-col items-center text-center group">
                <div class="w-16 h-16 bg-accent-50 text-accent-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-accent-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-3">Hybrid Flexibility</h4>
                <p class="text-base text-gray-500 leading-relaxed">Mix Face-to-Face intensity with Pure Online convenience depending on your availability and learning style.</p>
            </div>
            <!-- Feature 3 -->
            <div class="flex flex-col items-center text-center group">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-3">Highest Passing Rate</h4>
                <p class="text-base text-gray-500 leading-relaxed">We produce topnotchers equipped with rigorous mock tests, exclusive coaching, and practical drills.</p>
            </div>
        </div>
    </div>
</section>


{{-- ════════════════════════════════════════
    TESTIMONIALS
════════════════════════════════════════ --}}
<section class="bg-white border-t border-gray-100 py-16 md:py-20" id="about">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-brand-600 text-sm font-semibold uppercase tracking-widest">Success Stories</span>
            <h2 class="text-3xl font-bold text-gray-900 mt-2">What Our Graduates Say</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
            $testimonials = [
                ['name' => 'Dr. Maria Santos', 'year' => 'Board Passer 2024', 'quote' => 'DentReview gave me the structure and confidence I needed. The mock exams were spot-on — I felt completely prepared on exam day!', 'initial' => 'M', 'color' => 'bg-pink-400'],
                ['name' => 'Dr. Jose Reyes', 'year' => 'Board Passer 2024', 'quote' => 'The comprehensive program covered every topic thoroughly. The instructors were brilliant and always available for questions.', 'initial' => 'J', 'color' => 'bg-brand-500'],
                ['name' => 'Dr. Ana Lim', 'year' => 'Board Passer 2023', 'quote' => 'Flexible online sessions fit perfectly with my schedule. I passed on my first attempt and would highly recommend DentReview to everyone!', 'initial' => 'A', 'color' => 'bg-emerald-400'],
            ];
            @endphp

            @foreach($testimonials as $t)
            <div class="bg-slate-50 rounded-2xl p-6 border border-gray-100">
                <div class="flex gap-1 mb-4">
                    @for($i=0; $i<5; $i++)
                    <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-gray-600 text-sm leading-relaxed italic mb-5">"{{ $t['quote'] }}"</p>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full {{ $t['color'] }} flex items-center justify-center text-white text-sm font-bold">{{ $t['initial'] }}</div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $t['name'] }}</p>
                        <p class="text-xs text-gray-400">{{ $t['year'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ════════════════════════════════════════
    FINAL CTA SECTION
════════════════════════════════════════ --}}
<section class="py-16 md:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-gradient-to-br from-brand-800 to-brand-950 rounded-3xl p-10 md:p-14 shadow-card relative overflow-hidden border border-brand-700/50">
            {{-- Decorative circles --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-accent-500/10 rounded-full -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/3 pointer-events-none"></div>

            <span class="text-accent-400 text-sm font-semibold uppercase tracking-widest block mb-3">Ready to Start?</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Your Board Exam Success Starts Here</h2>
            <p class="text-brand-100/80 mb-8 max-w-lg mx-auto">Seats are limited. Secure your spot today and take the first step toward becoming a licensed dentist.</p>

            <a href="{{ url('/enroll') }}"
               id="cta-enroll-btn"
               class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-accent-500 text-brand-950 font-extrabold rounded-xl shadow-[0_4px_14px_0_rgba(250,178,27,0.39)] hover:bg-accent-400 hover:shadow-[0_6px_20px_rgba(250,178,27,0.23)] active:scale-[0.98] transition-all duration-200 text-base">
                Enroll Now
                <svg class="w-4 h-4 text-brand-900" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
