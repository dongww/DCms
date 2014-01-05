<?php
/**
 * User: dongww
 * Date: 13-12-31
 * Time: 上午11:09
 */

namespace Data;


class Category
{
    protected $category;
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
        $category = \R::findAll($name);

        foreach ($category as $c) {
            $this->category[] = array(
                'id' => $c->id,
                'title' => $c->name,
                'parent_id' => $c->$name->id
            );
        }
    }

    protected function hasChildren($id)
    {
        foreach ($this->category as $row) {
            if ($row['parent_id'] == $id)
                return true;
        }
        return false;
    }

    public function getTreeView($parent = 0)
    {
        $result = '<ul id="jstree_demo_div">';
        if ($this->category) {
            foreach ($this->category as $row) {
                if ($row['parent_id'] == $parent) {
                    $result .= '<li class="jstree-open" id="category_' . $this->name . '_' . $row['id'] . '">' . $row['title'];
                    if ($this->hasChildren($row['id']))
                        $result .= $this->getTreeView($row['id']);
                    $result .= "</li>";
                }
            }
        }

        $result .= "</ul>";

        return $result;
    }

    public function getJson()
    {

    }
} 