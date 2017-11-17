<?php

namespace Neirda24\Bundle\GuzzleBundleHeaderDisableCachePlugin\EventListener;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class NoCacheSubscriber implements EventSubscriberInterface
{
    public const DEFAULT_SKIP_CACHE_HEADER = 'X-Guzzle-Skip-Cache';

    /**
     * @var array
     */
    private $guzzleClients = [];

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['removeCacheHandlersIfNeeded', 40],
            ],
        ];
    }

    /**
     * @param Client $client
     * @param string $headerName
     */
    public function addGuzzleClient(Client $client, string $headerName = self::DEFAULT_SKIP_CACHE_HEADER): void
    {
        if (!array_key_exists($headerName, $this->guzzleClients)) {
            $this->guzzleClients[$headerName] = [];
        }

        $this->guzzleClients[$headerName][spl_object_hash($client)] = $client;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function removeCacheHandlersIfNeeded(GetResponseEvent $event): void
    {
        $request = $event->getRequest();

        $headersToCompare = array_keys($this->guzzleClients);

        foreach ($headersToCompare as $headerToCompare) {
            if ($request->headers->has($headerToCompare)) {
                $guzzleClients = $this->guzzleClients[$headerToCompare];
                array_walk($guzzleClients, [$this, 'removeCacheHandlerFromClient']);
                unset($guzzleClients);
            }
        }
    }

    /**
     * @param Client $client
     */
    private function removeCacheHandlerFromClient(Client $client): void
    {
        /** @var HandlerStack $handler */
        $handler = $client->getConfig('handler');
        $handler->remove('cache');
    }
}
