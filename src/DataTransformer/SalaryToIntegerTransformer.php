<?php

declare(strict_types=1);

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


/**
 * @implements DataTransformerInterface<string|int|null, string|int|null>
 */
class SalaryToIntegerTransformer implements DataTransformerInterface
{
    /**
     * Transforms the stored integer value into a formatted string with commas for display.
     */
    public function transform( $value): string
    {

        if (empty($value)) {
            return '';
        }

        // Ensure it's a numeric value before formatting
        if (!is_numeric($value)) {
            throw new TransformationFailedException('Expected a numeric value.');
        }

        return number_format((int)$value, 0, '.', ',');
    }

    /**
     * Transforms the formatted string back to an integer for the database.
     */
    public function reverseTransform(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0; // Return 0 if the field is empty
        }

        // Remove commas and ensure it's a numeric value
        $cleanValue = str_replace(',', '', $value);

        if (!is_numeric($cleanValue)) {
            throw new TransformationFailedException('Expected a numeric value.');
        }

        return (int)$cleanValue;
    }
}
