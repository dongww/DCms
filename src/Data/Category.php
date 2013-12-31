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
        $this->category = \R::findAll($name);
    }

    protected function hasChildren($id)
    {
        $name = $this->name;
        foreach ($this->category as $obj) {
            if ($obj->$name->id == $id)
                return true;
        }
        return false;
    }

    public function getTreeView($parent = 0)
    {
        $name = $this->name;
        $result = "<ul>";
        foreach ($this->category as $obj) { //echo $obj->$name->id . ',';
            if ($obj->$name->id == $parent) {
                $result .= '<li>' . $obj->id;
                if ($this->hasChildren($obj->id))
                    $result .= $this->getTreeView($obj->id);
                $result .= "</li>";
            }
        }
        $result .= "</ul>";

        return $result;
    }
} 