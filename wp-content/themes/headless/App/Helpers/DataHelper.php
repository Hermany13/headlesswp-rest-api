<?php

class DataHelper
{
    static function getFatherCategory($categories)
    {

        foreach ($categories as $category) {
            if ($category->category_parent == 0) return $category->name;
        }

        return '';
    }
}
