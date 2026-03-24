<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\EnrollPayRequest;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Services\EnrollmentService;
use App\Services\PaymongoService;
use App\Models\Enrollment;
use App\Models\Program;

class EnrollmentController extends Controller
{
    public function __construct(
        protected EnrollmentService $enrollmentService,
        protected PaymongoService $paymongoService
    ) {}
    /**
     * Landing / Home page.
     */
    public function landing()
    {
        $programCategories = $this->enrollmentService->getGroupedActivePrograms();
        return view('enrollment.landing', compact('programCategories'));
    }

    /**
     * Enrollment form page.
     */
    public function form()
    {
        $programCategories = $this->enrollmentService->getGroupedActivePrograms();
        $oldData = session('enrollment_data', []);

        return view('enrollment.form', compact('programCategories', 'oldData'));
    }

    /**
     * Cache form data to session (creates DB record ONLY at checkout step).
     */
    public function store(StoreEnrollmentRequest $request)
    {
        // 1. Validate and store to session temporarily
        $request->session()->put('enrollment_data', $request->validated());

        // 2. Head to payment page (where DB creation happens when clicking Pay Now)
        return redirect()->route('enroll.payment');
    }

    /**
     * Order summary / payment page.
     */
    public function payment(Request $request)
    {
        $oldData = session('enrollment_data');
        
        // If session expired or they refreshed wildly, send back to form
        if (!$oldData) {
            return redirect()->route('enroll.form');
        }

        $program = Program::where('slug', $oldData['program'])->firstOrFail();

        // Pass a mocked transient Enrollment object to the view just so UI won't break 
        // because it expects an active $enrollment model structure.
        $enrollment = new Enrollment($oldData);
        $enrollment->first_name   = $oldData['first_name']; // ensure name helper fields exist explicitly
        $enrollment->surname      = $oldData['surname'];
        $enrollment->payment_type = $oldData['payment_type'] ?? 'full';
        
        $enrollment->base_amount  = ($enrollment->payment_type === 'full') ? $program->active_price : $program->price_dp;
        $enrollment->convenience_fee = 50;
        $enrollment->total_amount    = $enrollment->base_amount + $enrollment->convenience_fee;

        return view('enrollment.payment', [
            'enrollment' => $enrollment,
            'program'    => $program
        ]);
    }

    /**
     * Create the DB record and process actual checkout (via Webhook/PayMongo or manual)
     */
    public function pay(EnrollPayRequest $request)
    {
        $oldData = session('enrollment_data');

        if (!$oldData) {
            return redirect()->route('enroll.form');
        }

        try {
            $enrollment = $this->enrollmentService->createEnrollment($oldData);
            $checkout = $this->paymongoService->createCheckoutSession(
                $enrollment,
                $request->validated('payment_method')
            );

            $request->session()->put('latest_enrollment_ref', $enrollment->reference_number);
            $request->session()->put('latest_payment_id', $checkout['payment']->id);

            if (empty($checkout['checkout_url'])) {
                return redirect()->route('enroll.payment')
                    ->with('error', 'Unable to initialize payment checkout. Please try again.');
            }

            return redirect()->away($checkout['checkout_url']);
        } catch (\Throwable $e) {
            Log::error('Enrollment payment checkout failed.', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('enroll.payment')
                ->with('error', 'Payment gateway is temporarily unavailable. Please try again.');
        }
    }

    /**
     * Success / confirmation page.
     */
    public function success(Request $request)
    {
        $enrollment = null;

        $referenceNumber = $request->query('ref') ?: $request->session()->get('latest_enrollment_ref');
        if ($referenceNumber) {
            $enrollment = Enrollment::with('program')
                ->where('reference_number', $referenceNumber)
                ->first();
        }

        if (!$enrollment) {
            $checkoutSessionId = $request->query('checkout_session_id') ?: $request->query('id');
            if ($checkoutSessionId) {
                $enrollment = $this->paymongoService->syncCheckoutSessionStatus($checkoutSessionId);
            }
        }

        if (!$enrollment) {
            return redirect()->route('enroll.form')
                ->with('error', 'Enrollment session not found. Please try again.');
        }

        $request->session()->forget(['current_enrollment_id', 'enrollment_data', 'latest_enrollment_ref', 'latest_payment_id']);

        return view('enrollment.success', [
            'enrollment' => $enrollment,
            'program'    => $enrollment->program
        ]);
    }

    public function cancel(Request $request)
    {
        return view('enrollment.cancel', [
            'referenceNumber' => $request->query('ref'),
        ]);
    }
}
