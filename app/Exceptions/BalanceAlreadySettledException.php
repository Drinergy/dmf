<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a student's remaining tuition balance is already fully paid.
 *
 * Used by EnrollmentBalanceService to signal the controller
 * to redirect to the success page instead of rendering the balance form.
 *
 * @author CKD
 *
 * @created 2026-04-20
 */
final class BalanceAlreadySettledException extends RuntimeException
{
    public function __construct(public readonly string $referenceNumber)
    {
        parent::__construct('Tuition balance is fully settled.');
    }
}
