<?php

declare(strict_types=1);

namespace App\Services\Whisper;

use Exception;

final class WhisperDownloadException extends Exception
{
    public function __construct(
        string $message,
        public readonly ?string $details = null,
    ) {
        parent::__construct($message);
    }

    public function getFullMessage(): string
    {
        if ($this->details) {
            return "{$this->message}: {$this->details}";
        }

        return $this->message;
    }
}
