<?php

namespace App\Console\Commands;

use App\Model\Repository\LogRepository;
use App\Service\Interfaces\RabbitClientInterface;
use Illuminate\Console\Command;
use Interop\Queue\Consumer;

class RabbitReceiverCommand extends Command
{
    private $rabbitClient;
    private $logRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:receive {--exchange=test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command receive rabbit';

    /**
     * Create a new command instance.
     *
     * @param RabbitClientInterface $rabbitClient
     * @param LogRepository $logRepository
     */
    public function __construct(RabbitClientInterface $rabbitClient, LogRepository $logRepository)
    {
        parent::__construct();
        $this->rabbitClient = $rabbitClient;
        $this->logRepository = $logRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var Consumer $consumer */
        $consumer = $this->rabbitClient->createConsumer($this->option('exchange'));
        while (true) {
            $message = $consumer->receive();
            if (!empty($message)) {
                $consumer->acknowledge($message);
                $message = $message->getBody();
                if (!empty($message)) {
                    $this->logRepository->create($message);
                }
            }
        }
    }
}
