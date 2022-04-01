<?php

namespace App\Console\Commands;

use App\Model\Repository\LogRepository;
use App\Service\Interfaces\RabbitClientInterface;
use Illuminate\Console\Command;
use Illuminate\Redis\RedisManager;
use Interop\Queue\Consumer;

class RabbitReceiverCommand extends Command
{
    private $rabbitClient;
    private $logRepository;
    private $redis;

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
     * @param RedisManager $redis
     */
    public function __construct(RabbitClientInterface $rabbitClient, LogRepository $logRepository, RedisManager $redis)
    {
        parent::__construct();
        $this->rabbitClient = $rabbitClient;
        $this->logRepository = $logRepository;
        $this->redis = $redis;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->redis->set('rabbit_receive_enable', true);
        /** @var Consumer $consumer */
        $consumer = $this->rabbitClient->createConsumer($this->option('exchange'));
        while (!empty($this->redis->get('rabbit_receive_enable', false))) {
            $message = $consumer->receive(5000);
            if (!empty($message)) {
                $consumer->acknowledge($message);
                $message = $message->getBody();
                if (!empty($message)) {
                    $this->logRepository->create($message);
                }
            }
        }

        return 0;
    }
}
