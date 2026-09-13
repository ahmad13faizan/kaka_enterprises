<?php

namespace Agency\B2BFields\Rules;

use Agency\B2BFields\Support\IdProof;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that id_proof_number matches the document pattern that corresponds
 * to the submitted id_proof_type (Requirement 7.6, 7.8).
 *
 * The rule is constructed with the submitted id_proof_type so it can pick the
 * correct pattern. When the type is missing or outside the allowed set, this
 * rule stays silent and lets the dedicated id_proof_type rule
 * (`required|in:GST,Aadhaar,PAN`) report that error, avoiding duplicate/confusing
 * messages on the same submission.
 *
 * Usage (task 8.2, in a Form Request):
 *   'id_proof_number' => ['required', 'string', 'max:50', new IdProofNumber($this->input('id_proof_type'))],
 * or simply spread IdProof::rules($this->input('id_proof_type')).
 */
class IdProofNumber implements ValidationRule
{
    /**
     * Convenience re-export so callers can reference the patterns via the rule.
     */
    public const ID_PROOF_PATTERNS = IdProof::ID_PROOF_PATTERNS;

    public function __construct(protected ?string $idProofType)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = IdProof::patternFor($this->idProofType);

        // Unknown/absent type is reported by the id_proof_type rule; nothing to do here.
        if ($pattern === null) {
            return;
        }

        if (! is_string($value) || preg_match($pattern, $value) !== 1) {
            $format = IdProof::formatFor($this->idProofType);

            $fail(sprintf(
                'The id_proof_number does not match the expected format for %s. Expected format: %s.',
                $this->idProofType,
                $format
            ));
        }
    }
}
