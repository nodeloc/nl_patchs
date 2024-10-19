<?php

namespace Nodeloc\NlPatchs\Listener;
use Flarum\Post\Event\Posted;
use Nodeloc\NlPatchs\Extend\NodelocEventCollector;
class ReplyEvent
{
    protected $collector;
    public function __construct(NodelocEventCollector $collector)
    {
        $this->collector = $collector;
    }
    public function __invoke(Posted $event)
    {
        if ($event->post->discussion->user->can("use_nodeloc_events")) {
            $content = strip_tags($event->post->discussion->firstPost->getAttributes()['content']);
            if (str_contains($content, "[NodelocEventFlag]")) {
                $pos1 = strpos($content, "[NodelocEventFlag]") + strlen("[NodelocEventFlag]");
                $pos2 = strpos($content, "[/NodelocEventFlag]");
                $content = trim(substr($content, $pos1, $pos2 - $pos1));

                if ($this->collector->has($content)) {
                    if ($event->post->discussion->posts()->where('user_id', $event->post->user_id)->count() > 1) {
                        if ($this->collector->once($content)) {
                            return;
                        }
                    }
                    $callback = $this->collector->get($content);
                    $callback($event->post);
                }
            }
        }
    }
}