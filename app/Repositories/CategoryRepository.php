<?php

namespace App\Repositories;

class CategoryRepository
{
    /**
     * Update when Children_id set to Parent_id
     */
    public function updateChildrensIfChildrenIdSetToParentId($category)
    {
        if ($category->children->count()) {
            foreach ($category->children as $child) {

                $child->parent_id = $category->id;
                $child->depth = ($category->depth + 1);
                $child->save();

                if ($category->children->count()) {
                    $this->updateChildrensIfChildrenIdSetToParentId($child);
                }
            }
        }
    }

    /**
     * Update when Parent_id set to Children_id
     */
    public function updateChildrensIfParentIdSetToChildrenId($category, $parentCategory)
    {
        if ($category->children->count()) {

            foreach ($category->children as $child) {
                $child->parent_id = ($parentCategory ? $parentCategory->id : null);
                $child->depth = ($parentCategory ? $parentCategory->depth + 1 : 0);
                $child->save();

                if ($child->children->count()) {
                    $this->updateChildrensIfParentIdSetToChildrenId($child, $child);
                }
            }
        }
    }

    /**
     * Update when any category_id set to null
     */
    public function updateChildrensIfParentIdSetToNull($category, int $depth = 0)
    {
        $category->depth = $depth;
        $category->save();

        if ($category->children->count()) {
            foreach ($category->children as $child) {

                $child->parent_id = $category->id;
                $child->depth = $depth + 1;
                $child->save();

                // updating descendants recursively
                if ($child->children->count()) {
                    $this->updateChildrensIfParentIdSetToNull($child, ($depth + 1));
                }
            }
        }
    }
}
