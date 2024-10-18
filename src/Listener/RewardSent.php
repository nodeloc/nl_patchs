<?php

namespace Nodeloc\NlPatchs\Listener;
use Flarum\Locale\Translator;
use Xypp\ForumQuests\Event\QuestDone;
use Illuminate\Contracts\Events\Dispatcher;

class RewardSent
{

    protected Translator $translator;
    protected Dispatcher $events;
    public function __construct(Translator $translator, Dispatcher $events)
    {
        $this->translator = $translator;
        $this->events = $events;
    }
    public function __invoke(QuestDone $event)
    {
        $total = 0;
        $event->quest->eachRewards(function ($name, $value) use (&$total) {
            if ($name == "money") {
                $total += $value;
            }
        });
        if ($total) {
            $this->events->dispatch(new \Mattoid\MoneyHistory\Event\MoneyHistoryEvent(
                $event->user,
                $total,
                'quest_done',
                $this->translator->trans('nodeloc-nl-patchs.api.quest_done')
            ));
        }
    }
}