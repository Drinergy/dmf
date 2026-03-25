<?php

namespace App\Http\Requests;

use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $programSlug = (string) $this->input('program');
        $program = Program::query()
            ->where('slug', $programSlug)
            ->first();

        $activeScheduleCount = $program
            ? $program->schedules()->where('is_active', true)->count()
            : 0;

        $scheduleRules = [
            $activeScheduleCount > 1 ? 'required' : 'nullable',
        ];

        if ($program) {
            $scheduleRules[] = Rule::exists('schedules', 'id')
                ->where(fn ($q) => $q
                    ->where('program_id', $program->id)
                    ->where('is_active', true)
                );
        } else {
            $scheduleRules[] = 'nullable';
        }

        return [
            // Personal
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'surname'        => 'required|string|max:255',
            'birthday'       => 'required|date',
            'sex'            => 'required|string|in:Male,Female',

            // Contact
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|max:255',
            'facebook'       => 'nullable|string|max:255',

            // Address
            'addr_street'    => 'required|string|max:255',
            'addr_city'      => 'required|string|max:255',
            'addr_province'  => 'required|string|max:255',
            'addr_zip'       => 'required|string|max:10',
            'deliv_street'   => 'nullable|string|max:255',
            'deliv_city'     => 'nullable|string|max:255',
            'deliv_province' => 'nullable|string|max:255',
            'deliv_zip'      => 'nullable|string|max:10',

            // Academic
            'school'         => 'required|string|max:255',
            'year_level'     => 'required|string|max:50',
            'year_graduated' => 'nullable|string|max:10',
            'taker_status'   => 'required|string|max:50',

            // Program & Payment
            'program'        => 'required|string|exists:programs,slug',
            'schedule_id'    => $scheduleRules,
            'payment_type'   => 'required|in:full,downpayment',
        ];
    }
}
