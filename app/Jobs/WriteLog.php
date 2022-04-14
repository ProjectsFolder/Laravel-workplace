<?php

namespace App\Jobs;

use App\Model\Repository\LogRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WriteLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    /**
     * Create a new job instance.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
        $this->onQueue('logger');
    }

    /**
     * Execute the job.
     *
     * @param LogRepository $logRepository
     *
     * @return void
     */
    public function handle(LogRepository $logRepository)
    {
        $logRepository->store($this->message);
    }
}
