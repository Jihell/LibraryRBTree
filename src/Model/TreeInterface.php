<?php
/**
 * @package library
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree\Model;

/**
 * Interface TreeInterface
 *
 * Basically trees are used with integer id nodes, but you can implement your own node
 * id type. See README.md for instructions
 */
interface TreeInterface
{
    /**
     * Should call a root setter to setup node parent and position
     *
     * @param NodeInterface|null $node
     */
    function __construct(NodeInterface $node = null);

    /**
     * @return NodeInterface
     */
    function getRoot();

    /**
     * Insert a node a perform rotations if needed
     *
     * @param NodeInterface $node
     * @return $this
     */
    function insert(NodeInterface $node);

    /**
     * Remove the node from the tree.
     * The removed node will be orphan (no child nor parent).
     *
     * @param NodeInterface $node
     * @return $this
     */
    function remove(NodeInterface $node);

    /**
     * Find the node with the given id
     *
     * @param mixed $id
     * @return mixed
     */
    function find($id);

    /**
     * Find the predecessor (left) or successor (right).
     * If none exist, return given node
     *
     * @param NodeInterface $node
     * @param int $position
     * @return NodeInterface
     */
    function findRelative(NodeInterface $node, $position);

    /**
     * @param NodeInterface $node
     * @return NodeInterface|null
     */
    function findPredecessor(NodeInterface $node);

    /**
     * @param NodeInterface $node
     * @return NodeInterface|null
     */
    function findSuccessor(NodeInterface $node);

    /**
     * Min and max, can be anything but basically an int, as the ids
     * Return an ordered list of node between the given values (inclusive)
     *
     * @param mixed $min
     * @param mixed $max
     * @return array|NodeInterface[]
     */
    function enumerate($min, $max);

    /**
     * Return all Nodes in infixe ordered list.
     *
     * @return mixed
     */
    function infixeList();
}
