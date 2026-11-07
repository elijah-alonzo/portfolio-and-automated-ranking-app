<?php

namespace App\Features\Evaluations\Exceptions;

use RuntimeException;

/**
 * A council action the evaluation's current state does not allow. The message is shown to the person as is.
 */
final class EvaluationRuleException extends RuntimeException {}
