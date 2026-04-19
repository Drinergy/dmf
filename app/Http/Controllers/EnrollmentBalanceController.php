<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EnrollBalancePayRequest;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\EnrollmentFinancialService;
use App\Services\EnrollmentPricingService;
use App\Services\PaymongoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EnrollmentBalanceController extends Controller
{
    public function __construct(
        protected PaymongoService $paymongoService,
        protected EnrollmentFinancialService $enrollmentFinancialService,
    ) {}

    /**
     * Signed entry point for paying remaining tuition (downpayment enrollments).
     */
    public function show(Request $request, string $reference_number): RedirectResponse|View
    {
        $enrollment = Enrollment::with(['program', 'schedule'])
            ->where('reference_number', $reference_number)
            ->firstOrFail();

        $this->enrollmentFinancialService->recalculateEnrollmentFinancials($enrollment);
        $enrollment->refresh();

        if ($enrollment->payment_type !== 'downpayment') {
            return redirect()->route('enroll.form')
                ->with('error', 'This link is only for downpayment enrollments.');
        }

        $balance = EnrollmentPricingService::balanceTuitionDue($enrollment);
        if ($balance <= 0) {
            return redirect()->route('enroll.success', ['ref' => $enrollment->reference_number])
                ->with('success', 'Your tuition balance is fully settled. Thank you!');
        }

        $request->session()->put('balance_checkout_enrollment_id', $enrollment->id);

        $fee = EnrollmentPricingService::CONVENIENCE_FEE_PESOS;

        return view('enrollment.balance', [
            'enrollment' => $enrollment,
            'program' => $enrollment->program,
            'balance_tuition' => $balance,
            'convenience_fee' => $fee,
            'total_due' => $balance + $fee,
        ]);
    }

    /**
     * Start PayMongo checkout for the remaining tuition balance.
     */
    public function pay(EnrollBalancePayRequest $request): RedirectResponse
    {
        $id = (int) $request->session()->get('balance_checkout_enrollment_id');
        if ($id < 1) {
            return redirect()->route('enroll.form')
                ->with('error', 'Please open your pay balance link again.');
        }

        $enrollment = Enrollment::find($id);
        if (! $enrollment) {
            return redirect()->route('enroll.form')
                ->with('error', 'Enrollment not found.');
        }

        try {
            $checkout = $this->paymongoService->createCheckoutSession(
                $enrollment,
                $request->validated('payment_method'),
                Payment::PURPOSE_BALANCE,
            );

            $request->session()->put('latest_enrollment_ref', $enrollment->reference_number);
            $request->session()->put('latest_payment_id', $checkout['payment']->id);

            if (empty($checkout['checkout_url'])) {
                return redirect()->back()
                    ->with('error', 'Unable to initialize payment checkout. Please try again.');
            }

            return redirect()->away($checkout['checkout_url']);
        } catch (\Throwable $e) {
            Log::error('Balance payment checkout failed.', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Payment gateway is temporarily unavailable. Please try again.');
        }
    }
}
