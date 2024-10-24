<?php

namespace Nodeloc\NlPatchs\Console;


use ClarkWinkelmann\MoneyRewards\Reward;
use Flarum\Discussion\Discussion;
use Flarum\Locale\Translator;
use Flarum\Post\Post;
use Flarum\User\User;
use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;
use Symfony\Component\Console\Input\InputArgument;
class Update1024Reward extends Command
{
    /**
     * @var string
     */
    protected $signature = 'nl-patch:update_1024_reward';

    /**
     * @var string
     */
    protected $description = 'Fix for 1024 reward';
    private $events;
    private $translator;

    public function __construct(Dispatcher $events, Translator $translator)
    {
        parent::__construct();
        $this->events = $events;
        $this->translator = $translator;
        $this->addArgument("id", InputArgument::REQUIRED, "discussion id");
    }
    public function handle()
    {
        $discussion = Discussion::findOrFail($this->argument("id"));
        $posts = $discussion->posts();

        $this->withProgressBar($posts, function (Post $post) {
            $reward = Reward::where("post_id", $post->id)->first();
            if ($reward) {
                if ($reward->comment == "1024小礼物") {
                    $reward->comment == "1024 小礼物";
                    $reward->save();
                    User::lockForUpdate()->where('id', $post->user_id)->increment('money', 200);

                    $this->events->dispatch(new \Mattoid\MoneyHistory\Event\MoneyHistoryEvent(
                        $post->user,
                        200,
                        '1024_2024_GIFT',
                        $this->translator->trans('nodeloc-nl-patchs.api.1024_2024_gift')
                    ));
                }
            }
        });
    }
}