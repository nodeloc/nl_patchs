<?php

namespace Nodeloc\NlPatchs\Listener;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Nodeloc\NlPatchs\Extend\NodelocEventCollector;
use Xypp\LocalizeDate\Helper\CarbonZoneHelper;

class CreatingDiscussion
{
    protected CarbonZoneHelper $carbonZoneHelper;
    protected SettingsRepositoryInterface $settings;
    protected Translator $translator;
    protected NodelocEventCollector $collector;
    public function __construct(CarbonZoneHelper $carbonZoneHelper, SettingsRepositoryInterface $settings, Translator $translator, NodelocEventCollector $collector)
    {
        $this->carbonZoneHelper = $carbonZoneHelper;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->collector = $collector;
    }
    public function __invoke(Saving $event)
    {
        $content = $event->data['attributes']['content'];
        if (str_contains($content, "[NodelocEventFlag]")) {
            $event->actor->assertCan('use_nodeloc_events');
            $pos1 = strpos($content, "[NodelocEventFlag]") + strlen("[NodelocEventFlag]");
            $pos2 = strpos($content, "[/NodelocEventFlag]");
            $content = trim(substr($content, $pos1, $pos2 - $pos1));

            if (!$this->collector->has($content)) {
                throw new ValidationException(['message' => $this->translator->trans("nodeloc-nl-patchs.api.invalid_event_name",['name' => $content])]);
            }
        }

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