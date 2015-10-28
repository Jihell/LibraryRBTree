<?php
/**
 * @package library
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree;

/**
 * Class Node
 */
class Node
{
    /**
     * COLOR_BLACK
     *
     * @var bool
     */
    const COLOR_BLACK = true;

    /**
     * COLOR_RED
     *
     * @var bool
     */
    const COLOR_RED = false;

    /**
     * Children position
     *
     * @var bool
     */
    const POSITION_LEFT = -1;

    /**
     * Children position
     *
     * @var bool
     */
    const POSITION_RIGHT = 1;

    /**
     * id the value tha we will compare.
     * Basically it's an integer. You can override the Tree::compare
     * to set other comparison functions
     *
     * @var int
     */
    protected $id;

    /**
     * The content of this node. Can be anything
     *
     * @var mixed
     */
    protected $value;

    /**
     * Current color of the node
     *
     * @var bool
     */
    protected $color = self::COLOR_BLACK;

    /**
     * @var array|Node[]
     */
    protected $children = [
        self::POSITION_LEFT => null,
        self::POSITION_RIGHT => null,
    ];

    /**
     * Node's position relative to parent
     *
     * @var bool
     */
    protected $position;

    /**
     * @param int $id
     * @param mixed $value
     */
    public function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * Parent of the node
     *
     * @var Node
     */
    protected $parent = null;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param boolean $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @param int $position
     * @return Node
     */
    public function getChild($position)
    {
        return $this->children[$position];
    }

    /**
     * @param int $position
     * @return bool
     */
    public function haveChild($position)
    {
        return null !== $this->children[$position];
    }

    /**
     * Set child
     *
     * @param int $position
     * @param Node|null $child
     * @return $this
     */
    public function setChild($position, Node $child = null)
    {
        $this->children[$position] = $child;
        if (null !== $child) {
            $child->setParent($this)->setPosition($position);
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param boolean|null $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return Node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Node $parent
     * @return $this
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;
        return $this;
    }


    /**
     * =========================================================================
     *                              SHORTCUTS
     * =========================================================================
     */

    /**
     * get grand parent if a parent exist
     *
     * @return Node|null
     */
    public function getGrandParent()
    {
        if (null !== $this->getParent()) {
            return $this->getParent()->getParent();
        }

        return null;
    }

    /**
     * Get uncle if grand parent exist
     *
     * @return Node|null
     */
    public function getUncle()
    {
        if (null !== $this->getGrandParent()) {
            return $this->getGrandParent()->getChild(-$this->getParent()->getPosition());
        }

        return null;
    }

    /**
     * A NIL
     *
     * @return bool
     */
    public function isLeaf()
    {
        return null === $this->children[static::POSITION_LEFT]
            && null === $this->children[static::POSITION_RIGHT];
    }
}
