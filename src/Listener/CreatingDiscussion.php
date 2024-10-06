<?php

namespace Nodeloc\NlPatchs\Listener;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving;
use Flarum\Foundation\ValidationException;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Xypp\LocalizeDate\Helper\CarbonZoneHelper;

class CreatingDiscussion{
    protected CarbonZoneHelper $carbonZoneHelper;
    protected SettingsRepositoryInterface $settings;
    protected Translator $translator;
    public function __construct(CarbonZoneHelper $carbonZoneHelper,SettingsRepositoryInterface $settings,Translator $translator)
    {
        $this->carbonZoneHelper = $carbonZoneHelper;
        $this->settings = $settings;
        $this->translator = $translator;
    }
    public function __invoke(Saving $event){
        $count = Discussion::where('created_at', '>=', $this->carbonZoneHelper->now()->setTime(0, 0)->utc())
            ->whereExists(function ($query) {
                $query->selectRaw("1")
                    ->whereColumn('discussions.id', 'discussion_tag.discussion_id')
                    ->from('discussion_tag')
                    ->where('discussion_tag.tag_id', intval($this->settings->get('nodeloc-nl-patchs.lounge_id')));
            })
            ->count();
        if($count >= intval($this->settings->get('nodeloc-nl-patchs.lounge_allow'))){
            throw new ValidationException(['message' => $this->translator->trans('nodeloc-nl-patchs.api.lounge_full')]);
        }
    }
}