<?php

namespace Nodeloc\NlPatchs\Extend;
use Flarum\Foundation\ContainerUtil;
use Illuminate\Contracts\Container\Container;

class NodelocEventCollector
{
    public $events = [];
    public $_once = [];
    protected Container $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function add($event, $callback,$once)
    {
        $this->events[$event] = $callback;
        $this->_once[$event] = $once;
    }
    public function has($event)
    {
        return isset($this->events[$event]);
    }
    public function once($event)
    {
        return $this->_once[$event];
    }
    public function get($event)
    {
        if (!$this->has($event))
            return null;
        return ContainerUtil::wrapCallback($this->events[$event], $this->container);
    }
}