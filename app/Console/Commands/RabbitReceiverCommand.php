<?php

namespace App\Console\Commands;

use App\Domain\Interfaces\Input\VatSaverInterface;
use App\External\Interfaces\RabbitClientInterface;
use App\Model\Repository\LogRepository;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Redis\RedisManager;
use Interop\Queue\Consumer;

class RabbitReceiverCommand extends Command
{
    private $rabbitClient;
    private $vatSaver;
    private $logRepository;
    private $redis;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:receive {--exchange=test} {--o|once}';

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
     * @param VatSaverInterface $vatSaver
     * @param LogRepository $logRepository
     * @param RedisManager $redis
     */
    public function __construct(
        RabbitClientInterface $rabbitClient,
        VatSaverInterface $vatSaver,
        LogRepository $logRepository,
        RedisManager $redis
    ) {
        parent::__construct();
        $this->rabbitClient = $rabbitClient;
        $this->vatSaver = $vatSaver;
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
        $reconnect = false;
        $this->redis->set('rabbit_receive_enable', true);
        while (true) {
            try {
                if ($reconnect) {
                    $this->rabbitClient->reconnect();
                    $reconnect = false;
                }
                /** @var Consumer $consumer */
                $consumer = $this->rabbitClient->createConsumer($this->option('exchange'));
                while (!empty($this->redis->get('rabbit_receive_enable', false))) {
                    $message = $consumer->receive(5000);
                    if (!empty($message)) {
                        $data = $message->getBody();
                        if (!empty($data)) {
                            $this->vatSaver->saveVat($data);
                            $this->logRepository->store($data);
                            $this->info($data);
                        }
                        $consumer->acknowledge($message);
                    }

                    if ($this->option('once')) {
                        break 2;
                    }
                }
            } catch (Exception $ignored) {
                $reconnect = true;
                $this->error($ignored->getMessage());
                sleep(5);
            }
        }

        $this->info('Rabbit-receiver stopped!');

        return 0;
    }
}
