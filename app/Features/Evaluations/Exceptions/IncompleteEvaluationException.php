<?php

namespace App\Features\Evaluations\Exceptions;

use RuntimeException;

final class IncompleteEvaluationException extends RuntimeException
{
    public static function make(): self
    {
        return new self('All evaluation questions must be answered before submitting.');
    }
}
