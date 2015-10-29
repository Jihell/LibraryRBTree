<?php
/**
 * @package library
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree\Model;

/**
 * Class Node
 */
abstract class AbstractNode implements NodeInterface
{
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
     * @var array|NodeInterface[]
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
     * Parent of the node
     *
     * @var NodeInterface
     */
    protected $parent = null;

    /**
     * @param int $id
     * @param mixed $value
     */
    public function __construct($id, $value)
    {
        $this
            ->setId($id)
            ->setValue($value)
        ;
    }

    public abstract function __toString();

    /**
     * @return mixed
     */
    public abstract function getId();

    /**
     * @param mixed $id
     * @return $this
     */
    public abstract function setId($id);

    /**
     * @return mixed
     */
    public abstract function getValue();

    /**
     * @param mixed $value
     * @return $this
     */
    public abstract function setValue($value);

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
     * @return NodeInterface
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
     * @param NodeInterface|null $child
     * @return $this
     */
    public function setChild($position, NodeInterface $child = null)
    {
        $this->children[$position] = $child;
        if (null !== $child) {
            $child->setParent($this)->setPosition($position);
        }
        return $this;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param integer|null $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return NodeInterface|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param NodeInterface|null $parent
     * @return $this
     */
    public function setParent(NodeInterface $parent = null)
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
     * @return NodeInterface|null
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
     * @return NodeInterface|null
     */
    public function getUncle()
    {
        if (null !== $this->getGrandParent()) {
            return $this->getGrandParent()->getChild(-$this->getParent()->getPosition());
        }

        return null;
    }

    /**
     * Is a NIL / Leaf
     *
     * @return bool
     */
    public function isLeaf()
    {
        return null === $this->children[static::POSITION_LEFT]
            && null === $this->children[static::POSITION_RIGHT];
    }
}
