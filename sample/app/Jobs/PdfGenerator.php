<?php

namespace App\Jobs;

use Knp\Snappy\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PdfGenerator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $path = '';

    /**
     * Create a new job instance.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(Pdf $pdf): void
    {
        $pdf->generateFromHtml(
            '<h1>Laravel</h><p>Sample PDF Output.</p>', $this->path
        );
    }
}
