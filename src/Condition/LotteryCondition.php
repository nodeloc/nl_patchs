<?php

namespace Nodeloc\NlPatchs\Condition;
use Flarum\User\User;
use Nodeloc\Lottery\Lottery;
use Xypp\Collector\ConditionDefinition;
use Xypp\Collector\Data\ConditionAccumulation;

// class LotteryCondition extends ConditionDefinition
// {
//     public bool $accumulateAbsolute = true;
//     public bool $accumulateUpdate = true;
//     public bool $needManualUpdate = false;
//     public function __construct()
//     {
//         parent::__construct("lottery_sent", null, "nodeloc-nl-patchs.lib.condition.lottery_sent");
//     }
//     public function getAbsoluteValue(User $user, ConditionAccumulation $accumulation): bool
//     {
//         $lotterys = Lottery::where('user_id', $user->id)->get();
        
//     }
// }
