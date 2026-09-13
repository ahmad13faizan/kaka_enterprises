<?php

namespace Agency\B2BFields\Support;

use Agency\B2BFields\Rules\IdProofNumber;

/**
 * Single source of truth for the B2B "id proof" validation used on the company
 * registration form (Requirement 7.6, 7.7, 7.8).
 *
 * The GSTIN pattern here is the canonical Indian GSTIN format also consumed by
 * the admin customer GST field (see B2BFieldsServiceProvider). Keeping it in one
 * place avoids two diverging copies of the regex.
 */
class IdProof
{
    /**
     * Indian GSTIN format: 2 digit state code + 10 char PAN + entity digit + 'Z' + checksum.
     * Example: 22AAAAA0000A1Z5
     */
    public const GSTIN_PATTERN = '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/';

    /**
     * Map of the allowed id_proof_type value => the regex the id_proof_number must match.
     * GST reuses the canonical GSTIN pattern above (single source of truth).
     */
    public const ID_PROOF_PATTERNS = [
        'GST'     => self::GSTIN_PATTERN,
        'PAN'     => '/^[A-Z]{5}[0-9]{4}[A-Z]$/',
        'Aadhaar' => '/^[2-9][0-9]{11}$/',
    ];

    /**
     * The exact allowed set for id_proof_type (Requirement 7.7).
     */
    public const ALLOWED_TYPES = ['GST', 'Aadhaar', 'PAN'];

    /**
     * Human-readable expected format per type, surfaced in the error message
     * when id_proof_number does not match (Requirement 7.8).
     */
    public const FORMAT_DESCRIPTIONS = [
        'GST'     => '15-character GSTIN, e.g. 22AAAAA0000A1Z5',
        'PAN'     => '10-character PAN, e.g. ABCDE1234F',
        'Aadhaar' => '12-digit Aadhaar number, e.g. 234567890123',
    ];

    /**
     * Return the regex pattern for the given id_proof_type, or null when the type
     * is not one of the allowed values.
     */
    public static function patternFor(?string $type): ?string
    {
        return self::ID_PROOF_PATTERNS[$type] ?? null;
    }

    /**
     * Return the human-readable expected format for the given id_proof_type.
     */
    public static function formatFor(?string $type): ?string
    {
        return self::FORMAT_DESCRIPTIONS[$type] ?? null;
    }

    /**
     * Reusable rules array for the id_proof_type / id_proof_number pair.
     *
     * Consumed by the registration Form Requests (task 8.2). The submitted
     * id_proof_type selects which document pattern id_proof_number must match.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function rules(?string $idProofType): array
    {
        return [
            'id_proof_type'   => ['required', 'in:' . implode(',', self::ALLOWED_TYPES)],
            'id_proof_number' => ['required', 'string', 'max:50', new IdProofNumber($idProofType)],
        ];
    }
}
