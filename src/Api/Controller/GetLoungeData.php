<?php

namespace Nodeloc\NlPatchs\Api\Controller;
use Flarum\Discussion\Discussion;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Xypp\LocalizeDate\Helper\CarbonZoneHelper;

class GetLoungeData implements RequestHandlerInterface
{

    protected CarbonZoneHelper $carbonZoneHelper;
    protected SettingsRepositoryInterface $settings;
    public function __construct(CarbonZoneHelper $carbonZoneHelper, SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
        $this->carbonZoneHelper = $carbonZoneHelper;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $attributes = [];
        if ($actor->can("ignoreLoungeLimit")) {
            $attributes['loungeCounter'] = 999;
        } else {
            $attributes['loungeCounter'] =
                intval($this->settings->get('nodeloc-nl-patchs.lounge_allow'))
                -
                (Discussion::where("user_id", $actor->id)
                    ->where('created_at', '>=', $this->carbonZoneHelper->now()->setTime(0, 0))
                    ->whereExists(function ($query) {
                        $query->selectRaw("1")
                            ->whereColumn('discussions.id', 'discussion_tag.discussion_id')
                            ->from('discussion_tag')
                            ->where('discussion_tag.tag_id', intval($this->settings->get('nodeloc-nl-patchs.lounge_id')));
                    })
                    ->count());
        }
        $attributes['loungeId'] = $this->settings->get('nodeloc-nl-patchs.lounge_id');
        return new JsonResponse($attributes);
    }
}