<?php

namespace Stokq\View\Helper\Nav\Renderer;

use Stokq\Stdlib\Nav;

/**
 * Class Simple
 * @package Stokq\View\Helper\Nav\Renderer
 */
class Simple implements RendererInterface
{

    /**
     * @param Nav $nav
     * @return string
     */
    public function render(Nav $nav)
    {
        $getVal = function(array $spec, $key, $default = null) {
            return isset($spec[$key]) ? $spec[$key] : $default;
        };

        $html = [];
        foreach ($nav->toArray() as $name => $spec) {
            $title = $getVal($spec, 'title');
            $icon = $getVal($spec, 'icon');
            $url = $getVal($spec, 'url');

            $html[] = sprintf(
                '<li class="%s"><a href="%s"><i class="fa fa-%s"></i> %s</a></li>',
                $nav->isActive($name) ? 'active' : '',
                $url,
                $icon,
                $title
            );
        }

        return implode("\n", $html);
    }
}
