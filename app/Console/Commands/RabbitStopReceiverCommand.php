<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Redis\RedisManager;

class RabbitStopReceiverCommand extends Command
{
    private $redis;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:receive:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command receive rabbit';

    /**
     * Create a new command instance.
     *
     * @param RedisManager $redis
     */
    public function __construct(RedisManager $redis)
    {
        parent::__construct();
        $this->redis = $redis;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->redis->set('rabbit_receive_enable', false);

        $this->info('Rabbit-receiver stopped!');

        return 0;
    }
}
