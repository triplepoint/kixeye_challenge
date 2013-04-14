<?php

namespace KixeyeLibs\Template;

/**
 * This simple template render combines a template
 * path and an array of data to generate rendered
 * output.
 */
class Renderer
{
    /**
     * Given a file path for a template, and an optional
     * array of data to pack the template with, return
     * the rendered template content.
     *
     * @param string $path the path to the template
     * @param array  $data an optional data collection for the template to use
     *
     * @return string the rendered template
     */
    public function render($path, $data = array())
    {
        ob_start();

        require $path;

        $return = ob_get_contents();
        ob_end_clean();

        return $return;
    }
}
