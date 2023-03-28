<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PdfFileProcessedEvent extends Event
{
    public const NAME = 'pdf.file.processed';

    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
