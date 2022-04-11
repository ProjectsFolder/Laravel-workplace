<?php

namespace App\External\Mq;

use App\External\Interfaces\RabbitClientInterface;
use Enqueue\AmqpLib\AmqpConnectionFactory;
use Enqueue\AmqpLib\AmqpConsumer;
use Enqueue\AmqpLib\AmqpContext;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\AmqpTopic;
use Interop\Amqp\Impl\AmqpBind;
use Interop\Queue\Consumer;
use Interop\Queue\Exception\Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;

class RabbitClient implements RabbitClientInterface
{
    private $rabbitUrl;
    /** @var AmqpContext */
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
     * @throws Exception
     *
     * @throws \Interop\Queue\Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function send(string $exchangeName, string $message)
    {
        $endpoint = $this->context->createTopic($exchangeName);
        $endpoint->setType(AmqpTopic::TYPE_FANOUT);
        $endpoint->setFlags(AmqpTopic::FLAG_DURABLE);
        $this->context->declareTopic($endpoint);
        $producer = $this->context->createProducer();
        $producer->send($endpoint, $this->context->createMessage($message));
    }

    /**
     * @param string $exchangeName
     *
     * @return AmqpConsumer|Consumer
     *
     * @throws Exception
     */
    public function createConsumer(string $exchangeName)
    {
        $queue = $this->context->createQueue(uniqid());
        $queue->setFlags(AmqpQueue::FLAG_IFUNUSED | AmqpQueue::FLAG_AUTODELETE | AmqpQueue::FLAG_EXCLUSIVE);
        $this->context->declareQueue($queue);

        $endpoint = $this->context->createTopic($exchangeName);
        $endpoint->setType(AmqpTopic::TYPE_FANOUT);
        $endpoint->setFlags(AmqpTopic::FLAG_DURABLE);
        $this->context->declareTopic($endpoint);

        $this->context->bind(new AmqpBind($endpoint, $queue));
        return $this->context->createConsumer($queue);
    }
}
