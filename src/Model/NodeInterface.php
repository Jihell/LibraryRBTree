<?php
/**
 * @package library
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree\Model;

/**
 * Interface NodeInterface
 *
 * Basically node are used with integer id, but you can implement your own node
 * id type. See README.md for instructions
 */
interface NodeInterface
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
     * @var integer
     */
    const POSITION_LEFT = -1;

    /**
     * Children position
     *
     * @var integer
     */
    const POSITION_RIGHT = 1;

    /**
     * Children position
     *
     * @var null
     */
    const POSITION_ROOT = null;

    /**
     * Id can be anything. Basically you will want to use it with integer.
     *
     * @return mixed
     */
    function getId();

    /**
     * @param mixed $id
     * @return $this
     */
    function setId($id);

    /**
     * @return mixed
     */
    function getValue();

    /**
     * @param mixed $value
     * @return $this
     */
    function setValue($value);

    /**
     * @return boolean
     */
    function getColor();

    /**
     * @param boolean $color
     * @return $this
     */
    function setColor($color);

    /**
     * @return integer
     */
    function getPosition();

    /**
     * Position can be null if root
     *
     * @param integer|null $position
     * @return $this
     */
    function setPosition($position);

    /**
     * @return NodeInterface|null
     */
    function getParent();

    /**
     * If the parent is null, it's a root
     *
     * @param NodeInterface|null $parent
     * @return $this
     */
    function setParent(NodeInterface $parent = null);

    /**
     * @param int $position
     * @return NodeInterface
     */
    function getChild($position);

    /**
     * Set child
     *
     * @param int $position
     * @param NodeInterface|null $child
     * @return $this
     */
    function setChild($position, NodeInterface $child = null);

    /**
     * @param int $position
     * @return bool
     */
    function haveChild($position);

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
    function getGrandParent();

    /**
     * Get uncle if grand parent exist
     *
     * @return NodeInterface|null
     */
    function getUncle();

    /**
     * Is a NIL / Leaf
     * A node without child is a leaf (so with both entry as null)
     *
     * @return bool
     */
    function isLeaf();
}
