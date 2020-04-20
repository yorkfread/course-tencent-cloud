<?php

namespace App\Caches;

use App\Models\Category as CategoryModel;

class MaxCategoryId extends Cache
{

    protected $lifetime = 365 * 86400;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return 'max_category_id';
    }

    public function getContent($id = null)
    {
        $category = CategoryModel::findFirst(['order' => 'id DESC']);

        return $category->id ?? 0;
    }

}
