<?php

namespace Nodeloc\NlPatchs\Content;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Settings\SettingsRepositoryInterface;
use Xypp\LocalizeDate\Helper\CarbonZoneHelper;

class LoungeCounter
{
    protected CarbonZoneHelper $carbonZoneHelper;
    protected SettingsRepositoryInterface $settings;
    public function __construct(CarbonZoneHelper $carbonZoneHelper, SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
        $this->carbonZoneHelper = $carbonZoneHelper;
    }

    public function __invoke(ForumSerializer $forumSerializer, $model, $attributes)
    {
        $attributes['loungeCounter'] =
            intval($this->settings->get('nodeloc-nl-patchs.lounge_allow'))
            -
            (Discussion::where('created_at', '>=', $this->carbonZoneHelper->now()->setTime(0, 0)->utc())
                ->whereExists(function ($query) {
                    $query->selectRaw("1")
                        ->whereColumn('discussions.id', 'discussion_tag.discussion_id')
                        ->from('discussion_tag')
                        ->where('discussion_tag.tag_id', intval($this->settings->get('nodeloc-nl-patchs.lounge_id')));
                })
                ->count());
        $attributes['loungeId'] = $this->settings->get('nodeloc-nl-patchs.lounge_id');
        return $attributes;
    }
}