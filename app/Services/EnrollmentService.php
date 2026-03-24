<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Program;

class EnrollmentService
{
    /**
     * Get all active programs grouped by category for the frontend.
     */
    public function getGroupedActivePrograms()
    {
        return Program::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');
    }

    /**
     * Create a new enrollment record from validated form data.
     */
    public function createEnrollment(array $data): Enrollment
    {
        $program = Program::where('slug', $data['program'])->firstOrFail();

        // Compute pricing
        $baseAmount = ($data['payment_type'] === 'full') 
            ? $program->active_price 
            : $program->price_dp;

        $convenienceFee = 50;
        $totalAmount = $baseAmount + $convenienceFee;

        // Create pending enrollment record
        return Enrollment::create([
            'reference_number' => Enrollment::generateReference(),
            'status'           => 'pending',
            
            'first_name'       => $data['first_name'],
            'middle_name'      => $data['middle_name'] ?? null,
            'surname'          => $data['surname'],
            'birthday'         => $data['birthday'],
            'sex'              => $data['sex'],
            
            'phone'            => $data['phone'],
            'email'            => $data['email'],
            'facebook'         => $data['facebook'] ?? null,
            
            'addr_street'      => $data['addr_street'],
            'addr_city'        => $data['addr_city'],
            'addr_province'    => $data['addr_province'],
            'addr_zip'         => $data['addr_zip'],
            'deliv_street'     => $data['deliv_street'] ?? null,
            'deliv_city'       => $data['deliv_city'] ?? null,
            'deliv_province'   => $data['deliv_province'] ?? null,
            'deliv_zip'        => $data['deliv_zip'] ?? null,
            
            'school'           => $data['school'],
            'year_level'       => $data['year_level'],
            'year_graduated'   => $data['year_graduated'] ?? null,
            'taker_status'     => $data['taker_status'],
            
            'program_id'       => $program->id,
            'schedule_id'      => $data['schedule_id'] ?? null,
            
            'payment_type'     => $data['payment_type'],
            'base_amount'      => $baseAmount,
            'convenience_fee'  => $convenienceFee,
            'total_amount'     => $totalAmount,
        ]);
    }
}
