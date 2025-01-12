<?php

namespace Nodeloc\NlPatchs\Content;

use Flarum\Settings\SettingsRepositoryInterface;

class FormatContent
{
    protected $whiteList = [];
    protected SettingsRepositoryInterface $settings;
    protected $currentRootDomain;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;

        // 获取白名单并按逗号分隔为数组
        $this->whiteList = array_map('trim', explode(',', $this->settings->get('nodeloc-nl-patchs.white_list', '')));

        // 获取当前根域名
        $this->currentRootDomain = $this->getRootDomain(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    }

    public function __invoke($serializer, $model, $attributes)
    {
        if (isset($attributes["contentHtml"])) {
            $newHTML = $attributes["contentHtml"];

            if (!is_null($newHTML)) {
                $newHTML = preg_replace_callback(
                    '/<a\s+(?:[^>]*?\s+)?href=["\']([^"\']+)["\']([^>]*)>(.*?)<\/a>/is',
                    function ($matches) {
                        $url = $matches[1];
                        $attributes = $matches[2];
                        $text = $matches[3];

                        // 对非白名单且非当前域名及子域名的外部链接添加跳转提示
                        if ($this->isExternalLink($url) && !$this->isInWhiteList($url)) {
                            $redirectUrl = '/goto/' . urlencode($url);
                            return "<a href=\"{$redirectUrl}\" {$attributes} target=\"_blank\" rel=\"noopener noreferrer\">{$text}</a>";
                        }

                        return $matches[0]; // 保留原样
                    },
                    $newHTML
                );
            }

            $attributes['contentHtml'] = $newHTML;
        }

        return $attributes;
    }

    private function isExternalLink($url)
    {
        $urlHost = parse_url($url, PHP_URL_HOST);

        if (!$urlHost) {
            return false; // 视为站内链接
        }

        $urlRootDomain = $this->getRootDomain($urlHost);

        // 检查是否为当前域名或其子域名
        return strtolower($this->currentRootDomain) !== strtolower($urlRootDomain);
    }

    private function isInWhiteList($url)
    {
        $urlHost = parse_url($url, PHP_URL_HOST);

        if (!$urlHost) {
            return false;
        }

        // 检查白名单域名及其子域名
        foreach ($this->whiteList as $whiteDomain) {
            $whiteDomain = trim($whiteDomain);

            // 确保白名单域名为根域名
            if (stripos($urlHost, $whiteDomain) === (strlen($urlHost) - strlen($whiteDomain))
                && (strlen($urlHost) == strlen($whiteDomain) || $urlHost[strlen($urlHost) - strlen($whiteDomain) - 1] == '.')) {
                return true;
            }
        }

        return false;
    }


    private function getRootDomain($host)
    {
        $parts = explode('.', $host);
        $count = count($parts);

        // 处理标准域名，如 example.com 和子域名
        if ($count >= 2) {
            return implode('.', array_slice($parts, -2));
        }

        return $host;
    }
}
