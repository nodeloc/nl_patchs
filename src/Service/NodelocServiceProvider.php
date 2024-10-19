<?php

namespace Nodeloc\NlPatchs\Service;

use Flarum\Extension\ExtensionManager;
use Flarum\Foundation\AbstractServiceProvider;
use Illuminate\Contracts\Container\Container;
use Nodeloc\NlPatchs\Extend\NodelocEventCollector;
class NodelocServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->singleton(NodelocEventCollector::class, function (Container $container) {
            $collector = new NodelocEventCollector($container);
            return $collector;
        });
    }
}