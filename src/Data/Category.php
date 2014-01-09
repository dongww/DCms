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
        $category = \R::findAll($name, ' order by sort ');

        foreach ($category as $c) {
            $this->category[] = array(
                'id' => $c->id,
                'title' => $c->title,
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

    public function addChildNode($parentId = 0, $title)
    {
        $child = \R::dispense($this->name);
        $child->title = $title;
        if ($parentId) {
            $parent = \R::load($this->name, $parentId);
            $maxSort = \R::getCell("select max(sort) from " . $this->name . " where " . $this->name . "_id = " . $parentId);
            $child->sort = $maxSort + 1;
            //$name = 'own' . ucwords($this->name) . '[]';
            $p = $this->name;
            $child->$p = $parent;
            //$parent->$name = $child;
            if (\R::store($child)) {
                return true;
            }
        } else {
            $count = \R::getCell('select count(*) from ' . $this->name);
            if ($count) {
                $maxSort = \R::getCell("select max(sort) from " . $this->name . " where " . $this->name . "_id is null");
                $child->sort = $maxSort + 1;
            } else {
                $child->sort = 1;
            }
            $p = $this->name . '_id';
            $child->$p = null;
            if (\R::store($child)) {
                return true;
            }
        }

        return false;
    }

    public function addPreNode($selectedId, $title)
    {
        $selected = \R::load($this->name, $selectedId);
        $sort = $selected->sort;
        $p = $this->name . '_id';
        $parent = $selected->$p;
        if ($parent) {
            $whereP = $p . ' = ' . $parent;
        } else {
            $whereP = $p . ' is null';
        }
        $nextAll = \R::findAll($this->name, ' where ' . $whereP . ' and sort >= ' . $sort . ' ');
        foreach ($nextAll as $n) {
            $n->sort = $n->sort + 1;
            \R::store($n);
        }
        $node = \R::dispense($this->name);
        $node->title = $title;
        $node->sort = $sort;
        $node->$p = $parent;
        if (\R::store($node)) {
            return true;
        }
    }

    public function addNextNode($selectedId, $title)
    {
        $selected = \R::load($this->name, $selectedId);
        $sort = $selected->sort;
        $p = $this->name . '_id';
        $parent = $selected->$p;
        if ($parent) {
            $whereP = $p . ' = ' . $parent;
        } else {
            $whereP = $p . ' is null';
        }
        $nextAll = \R::findAll($this->name, ' where ' . $whereP . ' and sort > ' . $sort . ' ');
        foreach ($nextAll as $n) {
            $n->sort = $n->sort + 1;
            \R::store($n);
        }
        $node = \R::dispense($this->name);
        $node->title = $title;
        $node->sort = $sort + 1;
        $node->$p = $parent;
        if (\R::store($node)) {
            return true;
        }
    }
} 