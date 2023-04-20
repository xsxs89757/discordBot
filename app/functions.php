<?php
/**
 * Here is your custom functions.
 */
function object_to_array($object) {
    if (is_object($object)) {
        // 获取对象的所有属性，包括公共、保护和私有属性
        $object = (array) $object;
    }

    if (is_array($object)) {
        // 遍历数组的每个元素，递归地将对象转换为数组
        $new = [];
        foreach ($object as $key => $value) {
            $new[$key] = object_to_array($value);
        }
    } else {
        $new = $object;
    }

    return $new;
}