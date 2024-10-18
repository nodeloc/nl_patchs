<?php

namespace Nodeloc\NlPatchs\Listener;

use Illuminate\Events\Dispatcher;
use Nodeloc\Lottery\Events\LotteryWasCreated;
use Nodeloc\Lottery\Events\LotteryWasEntered;
use Xypp\Collector\Data\ConditionData;
use Xypp\Collector\Event\UpdateCondition;
class LotteryEvents
{
    protected $events;
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }
    public function subscribe($events)
    {
        $events->listen(LotteryWasCreated::class, [$this, 'sent']);
        $events->listen(LotteryWasEntered::class, [$this, 'attend']);
    }

    public function sent(LotteryWasCreated $event)
    {
        $this->events->dispatch(
            new UpdateCondition(
                $event->lottery->user,
                [new ConditionData('lottery_sent', 1)]
            )
        );
    }

    public function attend(LotteryWasEntered $event)
    {
        $this->events->dispatch(
            new UpdateCondition(
                $event->actor,
                [new ConditionData('lottery_attend', 1)]
            )
        );
    }
}