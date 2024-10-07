<?php

namespace Nodeloc\NlPatchs\Listener;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Xypp\LocalizeDate\Helper\CarbonZoneHelper;

class CreatingDiscussion
{
    protected CarbonZoneHelper $carbonZoneHelper;
    protected SettingsRepositoryInterface $settings;
    protected Translator $translator;
    public function __construct(CarbonZoneHelper $carbonZoneHelper, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->carbonZoneHelper = $carbonZoneHelper;
        $this->settings = $settings;
        $this->translator = $translator;
    }
    public function __invoke(Saving $event)
    {
        if ($event->actor->can("ignoreLoungeLimit")) {
            return;
        }

        $isLounge = false;
        if (isset($event->data['relationships']['tags']['data'])) {
            $linkage = (array) $event->data['relationships']['tags']['data'];

            foreach ($linkage as $link) {
                if ((int) $link['id'] == $this->settings->get('nodeloc-nl-patchs.lounge_id')) {
                    $isLounge = true;
                }
            }
        }
        if (!$isLounge) {
            return;
        }

        if ($event->discussion->exists) {
            $tags = $event->discussion->tags;
            /**
             * @var \Flarum\Database\Eloquent\Collection $tags
             */
            if (!$tags->where("id", $this->settings->get('nodeloc-nl-patchs.lounge_id'))->count() > 0) {
                return;
            }
        }


        $count = Discussion::where("user_id", $event->discussion->user_id)
            ->where('created_at', '>=', $this->carbonZoneHelper->now()->setTime(0, 0))
            ->whereExists(function ($query) {
                $query->selectRaw("1")
                    ->whereColumn('discussions.id', 'discussion_tag.discussion_id')
                    ->from('discussion_tag')
                    ->where('discussion_tag.tag_id', intval($this->settings->get('nodeloc-nl-patchs.lounge_id')));
            })
            ->count();
        if ($count >= intval($this->settings->get('nodeloc-nl-patchs.lounge_allow'))) {
            throw new ValidationException(['message' => $this->translator->trans('nodeloc-nl-patchs.api.lounge_full')]);
        }
    }
}