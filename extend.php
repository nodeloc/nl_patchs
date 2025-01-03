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

use ClarkWinkelmann\MoneyRewards\Reward;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\Discussion\DiscussionValidator;
use Flarum\Discussion\Event\Saving;
use Flarum\Extend;
use Flarum\Post\Event\Posted;
use Flarum\Post\Post;
use Flarum\Tags\Api\Serializer\TagSerializer;
use Flarum\Tags\Tag;
use Flarum\User\User;
use Nodeloc\NlPatchs\Api\Controller\GetLoungeData;
use Nodeloc\NlPatchs\Condition\LotteryAttendCondition;
use Nodeloc\NlPatchs\Condition\LotterySentCondition;
use Nodeloc\NlPatchs\Console\Update1024Reward;
use Nodeloc\NlPatchs\Content\FormatContent;
use Nodeloc\NlPatchs\Content\TagSerializerAttributes;
use Nodeloc\NlPatchs\Extend\NodelocEvent;
use Nodeloc\NlPatchs\Listener\CreatingDiscussion;
use Nodeloc\NlPatchs\Listener\LotteryEvents;
use Nodeloc\NlPatchs\Listener\ReplyEvent;
use Nodeloc\NlPatchs\Listener\RewardSent;
use Nodeloc\NlPatchs\Service\NodelocServiceProvider;
use Xypp\Collector\Extend\ConditionProvider;
use Xypp\Collector\Helper\RewardHelper;
use Xypp\ForumQuests\Event\QuestDone;
use Flarum\Api\Serializer\BasicPostSerializer;
use Flarum\Api\Serializer\PostSerializer;


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
        ->listen(Saving::class, CreatingDiscussion::class)
        ->listen(QuestDone::class, RewardSent::class)
        ->listen(Posted::class, ReplyEvent::class)
    // ->subscribe(LotteryEvents::class)
    ,
    (new Extend\Settings())
        ->default("nodeloc-nl-patchs.lounge_id", 37)
        ->default("nodeloc-nl-patchs.share_id", 21)
        ->serializeToForum('nodeloc-nl-patchs.share_id', "nodeloc-nl-patchs.share_id")
        ->default("nodeloc-nl-patchs.lounge_allow", 2),
    (new Extend\Routes('api'))
        ->get('/nodeloc-lounge', 'nodeloc-lounge', GetLoungeData::class),
    // (new ConditionProvider)
    //     ->provide(LotterySentCondition::class)
    //     ->provide(LotteryAttendCondition::class),
    (new Extend\ServiceProvider)
        ->register(NodelocServiceProvider::class),
    (new Extend\Formatter)
        ->configure(function (\s9e\TextFormatter\Configurator $config) {
            $config->BBCodes->addCustom(
                '[NodelocEventFlag]{TEXT}[/NodelocEventFlag]',
                '<div class="NodelocEventFlag"></div>'
            );
        }),
    (new Extend\ApiSerializer(PostSerializer::class))
        ->attributes(FormatContent::class),
    (new Extend\ApiSerializer(BasicPostSerializer::class))
        ->attributes(FormatContent::class),

];
