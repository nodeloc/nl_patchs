<?php

namespace Nodeloc\NlPatchs\Extend;
use Flarum\Foundation\ContainerUtil;
use Illuminate\Contracts\Container\Container;

class NodelocEventCollector
{
    public $events = [];
    protected Container $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function add($event, $callback)
    {
        $this->events[$event] = $callback;
    }
    public function has($event)
    {
        return isset($this->events[$event]);
    }
    public function get($event)
    {
        if (!$this->has($event))
            return null;
        return ContainerUtil::wrapCallback($this->events[$event], $this->container);
    }
}