<?php

namespace App\Console\Commands;

use App\Domain\Interfaces\Input\VatSaverInterface;
use App\External\Interfaces\RabbitClientInterface;
use App\Infrastructure\Notification\Jobs\WriteLog;
use Enqueue\AmqpLib\AmqpConsumer;
use Exception;
use Illuminate\Bus\Dispatcher;
use Illuminate\Console\Command;
use Illuminate\Redis\RedisManager;

class RabbitReceiverCommand extends Command
{
    private $rabbitClient;
    private $vatSaver;
    private $redis;
    private $dispatcher;

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
     * @param RedisManager $redis
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        RabbitClientInterface $rabbitClient,
        VatSaverInterface $vatSaver,
        RedisManager $redis,
        Dispatcher $dispatcher
    ) {
        parent::__construct();
        $this->rabbitClient = $rabbitClient;
        $this->vatSaver = $vatSaver;
        $this->redis = $redis;
        $this->dispatcher = $dispatcher;
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
                /** @var AmqpConsumer $consumer */
                $consumer = $this->rabbitClient->createConsumer($this->option('exchange'), env('RABBIT_QUEUE_NAME'));
                while (!empty($this->redis->get('rabbit_receive_enable', false))) {
                    $message = $consumer->receive(5000);
                    if (!empty($message)) {
                        $data = $message->getBody();
                        if (!empty($data)) {
                            $this->vatSaver->saveVat($data);
                            $this->dispatcher->dispatch(new WriteLog($data));
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
