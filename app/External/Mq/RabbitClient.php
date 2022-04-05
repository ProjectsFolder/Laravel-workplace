<?php

namespace App\External\Mq;

use App\External\Interfaces\RabbitClientInterface;
use Enqueue\AmqpLib\AmqpConnectionFactory;
use Enqueue\AmqpLib\AmqpConsumer;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\AmqpTopic;
use Interop\Amqp\Impl\AmqpBind;
use Interop\Queue\Consumer;

class RabbitClient implements RabbitClientInterface
{
    private $rabbitUrl;
    private $context;

    public function __construct(string $rabbitUrl)
    {
        $this->rabbitUrl = $rabbitUrl;
        $this->reconnect();
    }

    public function reconnect()
    {
        $factory = new AmqpConnectionFactory($this->rabbitUrl);
        $this->context = $factory->createContext();
    }

    /**
     * @param string $exchangeName
     * @param string $message
     */
    public function send(string $exchangeName, string $message)
    {
        $endpoint = $this->context->createTopic($exchangeName);
        $endpoint->setType(AmqpTopic::TYPE_FANOUT);
        $this->context->declareTopic($endpoint);
        $producer = $this->context->createProducer();
        $producer->send($endpoint, $this->context->createMessage($message));
    }

    /**
     * @param string $exchangeName
     *
     * @return AmqpConsumer|Consumer
     */
    public function createConsumer(string $exchangeName)
    {
        $queue = $this->context->createQueue(uniqid());
        $queue->setFlags(AmqpQueue::FLAG_IFUNUSED | AmqpQueue::FLAG_AUTODELETE | AmqpQueue::FLAG_EXCLUSIVE);
        $this->context->declareQueue($queue);

        $endpoint = $this->context->createTopic($exchangeName);
        $endpoint->setType(AmqpTopic::TYPE_FANOUT);
        $this->context->declareTopic($endpoint);

        $this->context->bind(new AmqpBind($endpoint, $queue));
        return $this->context->createConsumer($queue);
    }
}
