<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Likes\Listener;

use Flarum\Foundation\ValidationException;
use Flarum\Likes\Event\PostWasLiked;
use Flarum\Likes\Event\PostWasUnliked;
use Flarum\Locale\Translator;
use Flarum\Post\Event\Deleted;
use Flarum\Post\Event\Saving;
use Illuminate\Contracts\Events\Dispatcher;

class SaveLikesToDatabase
{
    protected Translator $translator;
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Saving::class, [$this, 'whenPostIsSaving']);
        $events->listen(Deleted::class, [$this, 'whenPostIsDeleted']);
    }

    /**
     * @param Saving $event
     */
    public function whenPostIsSaving(Saving $event)
    {
        $post = $event->post;
        $data = $event->data;

        if ($post->exists && isset($data['attributes']['isLiked'])) {
            $actor = $event->actor;
            $liked = (bool) $data['attributes']['isLiked'];

            $actor->assertCan('like', $post);

            $currentlyLiked = $post->likes()->where('user_id', $actor->id)->exists();

            if ($liked && !$currentlyLiked) {
                if ($actor->money < 1) {
                    throw new ValidationException(['message' => $this->translator->trans("nodeloc-nl-patchs.api.no_enough_money")]);
                }
                $post->likes()->attach($actor->id);

                $post->raise(new PostWasLiked($post, $actor));
            } elseif ($currentlyLiked) {
                $post->likes()->detach($actor->id);

                $post->raise(new PostWasUnliked($post, $actor));
            }
        }
    }

    /**
     * @param Deleted $event
     */
    public function whenPostIsDeleted(Deleted $event)
    {
        $event->post->likes()->detach();
    }
}