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
use Nodeloc\NlPatchs\Api\Controller\GetLoungeData;
use Nodeloc\NlPatchs\Condition\LotteryAttendCondition;
use Nodeloc\NlPatchs\Condition\LotterySentCondition;
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


    (new NodelocEvent)
        ->addOnce("1yr_badge", function (Post $post) {
            resolve(RewardHelper::class)->reward($post->user, "badge", 25);
        })
        ->add("1024_2024", function (Post $post) {
            $hasDispatch = $post->discussion->posts()->whereExists(function ($query) {
                $query->selectRaw("1")
                    ->from("money_rewards")
                    ->whereColumn("posts.id", "money_rewards.post_id");
            })
                ->where("posts.user_id", $post->user_id)
                ->exists();
            if ($hasDispatch)
                return;
            $userKey = md5($post->user_id . "1024_2024");
            if (str_contains($post->content, $userKey)) {
                $reward = new Reward();
                $reward->post()->associate($post);
                $reward->giver()->associate($post->discussion->user);
                $reward->receiver()->associate($post->user);
                $reward->amount = 200;
                $reward->new_money = true;
                $reward->comment = "1024小礼物";
                $reward->save();
            }
        }),
    (new Extend\ApiSerializer(UserSerializer::class))
        ->attribute("1024_2024_flag", fn($serializer, $user, $attr) => md5($user->id . "1024_2024"))
];
