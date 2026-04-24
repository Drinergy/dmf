<?php

namespace App\Http\Requests;

use App\Models\Package;
use App\Models\Program;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $data = [];

        if (! $this->filled('facebook_messenger_name') && $this->filled('facebook')) {
            $data['facebook_messenger_name'] = $this->input('facebook');
        }

        $this->merge($data);
    }

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $slug = (string) $this->input('program');
        $program = Program::query()->where('slug', $slug)->first();
        $package = $program ? null : Package::query()->where('slug', $slug)->first();

        $scheduleRules = ['nullable'];
        if ($program) {
            $activeScheduleCount = $program->schedules()->where('is_active', true)->count();
            $scheduleRules = [
                $activeScheduleCount > 1 ? 'required' : 'nullable',
                Rule::exists('schedules', 'id')
                    ->where(fn ($q) => $q
                        ->where('program_id', $program->id)
                        ->where('is_active', true)
                    ),
            ];
        } elseif ($package) {
            $scheduleRules = ['nullable'];
        }

        return [
            // Personal
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'birthday' => 'required|date',
            'sex' => 'required|string|in:Male,Female',

            // Contact
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'facebook_messenger_name' => 'required|string|max:255',
            'facebook_messenger_url' => ['nullable', 'url', 'max:255'],

            // Address
            'addr_street' => 'required|string|max:255',
            'addr_city' => 'required|string|max:255',
            'addr_province' => 'required|string|max:255',
            'addr_zip' => 'required|string|max:10',
            'deliv_street' => 'nullable|string|max:255',
            'deliv_city' => 'nullable|string|max:255',
            'deliv_province' => 'nullable|string|max:255',
            'deliv_zip' => 'nullable|string|max:10',

            // Academic
            'school' => 'required|string|max:255',
            'year_level' => 'required|string|max:50',
            'year_graduated' => 'nullable|string|max:10',
            'taker_status' => 'required|string|max:50',

            // Program & Payment
            'program' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $slug = (string) $value;
                    $exists = Program::query()->where('slug', $slug)->exists()
                        || Package::query()->where('slug', $slug)->exists();
                    if (! $exists) {
                        $fail('Selected program/package is invalid.');
                    }
                },
            ],
            'schedule_id' => $scheduleRules,
            'payment_type' => 'required|in:full,downpayment',

            'data_accuracy_ack' => 'required|accepted',
        ];
    }
}
