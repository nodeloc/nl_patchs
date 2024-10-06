<?php

/*
 * This file is part of nodeloc/nl-patchs.
 *
 * Copyright (c) 2024 Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Nodeloc\NlPatchs;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Discussion\DiscussionValidator;
use Flarum\Discussion\Event\Saving;
use Flarum\Extend;
use Flarum\Tags\Api\Serializer\TagSerializer;
use Flarum\Tags\Tag;
use Nodeloc\NlPatchs\Api\Controller\GetLoungeData;
use Nodeloc\NlPatchs\Content\TagSerializerAttributes;
use Nodeloc\NlPatchs\Listener\CreatingDiscussion;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/less/admin.less'),
    new Extend\Locales(__DIR__ . '/locale'),
    (new Extend\ApiSerializer(TagSerializer::class))
        ->attributes(TagSerializerAttributes::class),
    (new Extend\Event())
        ->listen(Saving::class, CreatingDiscussion::class),
    (new Extend\Settings())
        ->default("nodeloc-nl-patchs.lounge_id", 37)
        ->default("nodeloc-nl-patchs.lounge_allow", 2),
    (new Extend\Routes('api'))
        ->get('/nodeloc-lounge', 'nodeloc-lounge', GetLoungeData::class)
];
