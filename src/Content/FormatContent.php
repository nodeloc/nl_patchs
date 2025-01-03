<?php

namespace Nodeloc\NlPatchs\Content;

class FormatContent
{
    public function __invoke($serializer, $model, $attributes)
    {

        if (isset($attributes["contentHtml"])) {
            $newHTML = $attributes["contentHtml"];

            // 检查 HTML 内容是否为空
            if (!is_null($newHTML)) {
                // 使用正则表达式匹配所有的链接
                $newHTML = preg_replace_callback(
                    '/<a\s+(?:[^>]*?\s+)?href=["\']([^"\']+)["\']([^>]*)>(.*?)<\/a>/is',
                    function ($matches) {
                        $url = $matches[1]; // 获取链接的 href 值
                        $attributes = $matches[2]; // 获取链接的其他属性
                        $text = $matches[3]; // 获取链接的文本

                        // 判断是否为站内链接或内部链接
                        if ($this->isExternalLink($url)) {
                            // 对非站内链接添加跳转提示
                            $redirectUrl = '/goto/' . urlencode($url);
                            return "<a href=\"{$redirectUrl}\" {$attributes} target=\"_blank\" rel=\"noopener noreferrer\">{$text}</a>";
                        }

                        // 保持原样处理站内链接
                        return $matches[0];
                    },
                    $newHTML
                );
            }

            $attributes['contentHtml'] = $newHTML;
        }

        return $attributes;
    }

    /**
     * 检查是否为外部链接
     *
     * @param string $url
     * @return bool
     */
    private function isExternalLink($url)
    {
        // 获取当前站点的主机名
        $host = parse_url(app('flarum.settings')->get('url'), PHP_URL_HOST);
        $urlHost = parse_url($url, PHP_URL_HOST);

        // 如果 URL 没有主机名，认为是内部链接
        if (!$urlHost) {
            return false;
        }

        // 比较主机名是否不同
        return $host !== $urlHost;
    }
}
