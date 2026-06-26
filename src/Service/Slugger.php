<?php
declare(strict_types=1);

namespace App\Service;

final class Slugger
{
    public function slugify(string $value): string
    {
        $value = mb_strtolower(trim($value));

        $value = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; Lower()',
            $value
        ) ?: $value;

        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        $value = trim($value, '-');

        if ($value === '') {
            return 'company';
        }

        return $value;
    }
}
