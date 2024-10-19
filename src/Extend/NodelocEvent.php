<?php

namespace Nodeloc\NlPatchs\Extend;
use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;

class NodelocEvent implements ExtenderInterface
{
    protected $events = [];
    protected $once = [];
    public function add($name, $callback)
    {
        $this->once[$name] = false;
        $this->events[$name] = $callback;
        return $this;
    }
    public function addOnce($name, $callback)
    {
        $this->add($name, $callback);
        $this->once[$name] = true;
        return $this;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        $container->resolving(
            NodelocEventCollector::class,
            function (NodelocEventCollector $collector, Container $container) {
                foreach ($this->events as $name => $callback) {
                    $collector->add($name, $callback, $this->once[$name]);
                }
            }
        );
        return $this;
    }
}