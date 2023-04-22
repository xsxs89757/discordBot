<?php

/**
 * 正则提取#uuid#
 *
 * @param string $content
 * @return string
 */
function pregGetUUID(string $content) : string
{
    $content = $content;
    $uuid = '';
    $pattern = "/#(.*?)#/";
    if (preg_match($pattern, $content, $matches)) {
        $uuid = $matches[1];
    }
    return $uuid;
}