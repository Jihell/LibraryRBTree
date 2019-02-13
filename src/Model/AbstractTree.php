<?php
/**
 * @package library
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree\Model;

use Jihel\Library\RBTree\Model\NodeInterface as Node;

/**
 * Class Tree
 *
 * - A node is either red or black.
 * - The root is black. This rule is sometimes omitted. Since the root can always be changed from red to black, but not necessarily vice versa, this rule has little effect on analysis.
 * - All leaves (NIL) are black. (here the NIL are just empty node)
 * - If a node is red, then both its children are black.
 * - Every path from a given node to any of its descendant NIL nodes contains the same number of black nodes. The uniform number of black nodes in the paths from root to leaves is called the black-height of the red–black tree.
 */
abstract class AbstractTree implements TreeInterface
{
    /**
     * Root node of the tree
     *
     * @access protected
     * @var Node
     */
    protected $root;

    /**
     * @param Node|null $node
     */
    protected function setRoot(Node $node = null)
    {
        $this->root = null !== $node ? $node->setPosition(null)->setParent(null) : null;
    }

    /**
     * @return Node
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param Node|null $node
     */
    public function __construct(Node $node = null)
    {
        $this->setRoot($node);
    }

    /**
     * Insert a node
     *
     * @param  Node $node
     * @return $this
     */
    public function insert(Node $node)
    {
        // If root is null, set as root
        if (null === $this->root) {
            $this->setRoot($node);
        // Else Find the new parent
        } else {
            $insertNode = $this->searchClosestNode($this->root, $node->getId());
            $insertNode->setChild($this->compare($insertNode->getId(), $node->getId()), $node);
        }
        // New node is red
        $node->setColor(Node::COLOR_RED);

        $this->insertSort($node);
        $this->root->setColor(Node::COLOR_BLACK);

        return $this;
    }

    /**
     * @param Node $node
     * @return $this
     */
    protected function insertSort(Node $node)
    {
        // Skip if the parent don't exist, also it's the root so it's black
        if (null === $node->getParent()) {
            $node->setColor(Node::COLOR_BLACK);
            return $this;
        }

        // Skip if parent or node are not both red
        if (!Node::COLOR_RED === $node->getParent()->getColor()
            && Node::COLOR_RED === $node->getColor()
        ) {
            return $this;
        }

        $grandParent = $node->getGrandParent();
        $uncle = $node->getUncle();
        // First case, the parent and grand parent are not on the same side
        // Simply rotate the parent to grand parent's place
        if ($node->getPosition() === -$node->getParent()->getPosition()) {
            // There is no uncle or uncle is black
            if (null === $uncle || Node::COLOR_BLACK === $uncle->getColor()) {
                $parent = $node->getParent();
                // Rotate the parent to grand parent place
                $this->rotate($node->getParent(), -$node->getPosition());
                return $this->insertSort($parent);
            // Else no rotation needed just fix the color
            } else {
                $uncle->setColor(Node::COLOR_BLACK);
                $node->getParent()->setColor(Node::COLOR_BLACK);
                if (null !== $grandParent) {
                    $grandParent->setColor(Node::COLOR_RED);
                }
                return $this->insertSort($grandParent);
            }
            // Second case, both parent and grand parent are on the same side
        // Rotate the parent to grand parent place
        } else {
            // If uncle and parent are red, set both black
            if (null !== $uncle && Node::COLOR_RED === $uncle->getColor()
             && Node::COLOR_RED === $node->getParent()->getColor()
            ) {
                $uncle->setColor(Node::COLOR_BLACK);
                $node->getParent()->setColor(Node::COLOR_BLACK);
                $grandParent->setColor(Node::COLOR_RED);
                return $this->insertSort($grandParent);
            // Else if we have a grand parent (so the same direction as parent)
            } elseif (null !== $grandParent) {
                $node->getParent()->setColor(Node::COLOR_BLACK);
                $grandParent->setColor(Node::COLOR_RED);
                $this->rotate($grandParent, -$node->getPosition());
                return $this->insertSort($grandParent);
            }
        }

        // Elsewhere obviously there is no problem
        return $this;
    }

    /**
     * Recursive search of the parent node of $node in $hierarchy
     * First call must be with $this->root or course.
     *
     * @param Node $hierarchy
     * @param int $id
     * @return Node
     * @throws \Exception
     */
    protected function searchClosestNode(Node $hierarchy, $id)
    {
        $position = $this->compare($hierarchy->getId(), $id);
        if ($hierarchy->isLeaf() || 0 === $position || !$hierarchy->haveChild($position)) {
            return $hierarchy;
        }

        return $this->searchClosestNode($hierarchy->getChild($position), $id);
    }

    /**
     * Do a rotation with node's parent
     *
     * @param Node $node
     * @param int $toPosition
     * @return $this
     */
    protected function rotate(Node $node, $toPosition)
    {
        // The new child of node in $toPosition
        $tmp = $node->getChild(-$toPosition);
        // Set node's child the grand son of son
        $node->setChild(-$toPosition, $tmp->getChild($toPosition));

        if ($tmp->haveChild($toPosition)) {
            $tmp->getChild($toPosition)->setParent($node);
        }

        $tmp->setParent($node->getParent());
        // If it's not the root, set parent's child
        if (null !== $node->getParent()) {
            $node->getParent()->setChild(($toPosition === $node->getPosition() ? 1 : -1)* $toPosition, $tmp);
        }
        $tmp->setChild($toPosition, $node);
        $node->setParent($tmp);

        // Rotation done, it's possible the root have changed
        if (null === $tmp->getParent()) {
            $this->setRoot($tmp);
        }

        return $this;
    }

    /**
     * Find a node by id.
     * Recursive operation, the optional $node should not be given
     *
     * @param int $id
     * @param Node|null $node
     * @return false|Node
     */
    public function find($id, Node $node = null)
    {
        // Initialize if first iteration
        if (null === $node) {
            $node = $this->root;
            if ($node === null) {
                return false;
            }
        }

        // If the id is equal, it's our match !
        $position = $this->compare($node->getId(), $id);
        if (0 === $position) {
            return $node;
        }

        // Else if it's a nil, return false, else recursion
        return $node->haveChild($position) ? $this->find($id, $node->getChild($position)) : false;
    }

    /**
     * @param Node $node
     * @param int $position
     * @param Node|null $relative
     * @return Node
     */
    public function findRelative(Node $node, $position, Node $relative = null)
    {
        // If we have already seek deeper and found a leaf, return the leaf
        if (null !== $relative && $node->isLeaf()) {
            return $node;
        }

        // If we have a child at this position, go seek to this child opposite direction until find a leaf
        if ($node->haveChild($position)) {
            // If it's a deep search, don't rotate search order. The closest is the deepest
            return $this->findRelative($node->getChild($position), (null === $relative ? -1 : 1) * $position, $node);
        }

        // It's the parent if parent direction is the same as node, else it's grand parent
        return $node->getPosition() === -$position ? $node->getParent() : $node->getGrandParent();
    }

    /**
     * Alias method
     *
     * @param Node $node
     * @return Node
     */
    public function findPredecessor(Node $node)
    {
        return $this->findRelative($node, Node::POSITION_LEFT);
    }

    /**
     * Alias method
     *
     * @param Node $node
     * @return Node
     */
    public function findSuccessor(Node $node)
    {
        return $this->findRelative($node, Node::POSITION_RIGHT);
    }

    /**
     * Remove the node from the tree.
     * The removed node will be orphan (no child nor parent).
     *
     * @param Node $node
     * @return $this
     */
    public function remove(Node $node)
    {
        if (!$node->haveChild(Node::POSITION_LEFT) || !$node->haveChild(Node::POSITION_RIGHT)) {
            $tmp = $node;
        } else {
            $tmp = $this->findRelative(
                $node,
                null === $node->getPosition() ?
                  Node::POSITION_RIGHT : $node->getPosition()
            );
        }

        $alt = $tmp->getChild(Node::POSITION_LEFT) ?: $tmp->getChild(Node::POSITION_RIGHT);
        if (null === $alt) {
            $alt = $tmp;
        }
        $alt->setParent($tmp->getParent());

        if (null === $node->getParent()) {
            $this->root = $alt;
        } elseif (null !== $tmp->getParent()) {
            $tmp->getParent()->setChild($tmp->getPosition(), $alt);
        }

        if ($tmp !== $node) {
            if ($tmp !== $alt && Node::COLOR_BLACK === $tmp->getColor()) {
                $this->deleteSort($alt);
            }

            if (null !== $tmp->getParent()) {
                $tmp->getParent()->setChild($tmp->getPosition(), $tmp->getChild($tmp->getPosition()));
            }

            $tmp
                ->setChild(Node::POSITION_LEFT, $node->getChild(Node::POSITION_LEFT))
                ->setChild(Node::POSITION_RIGHT, $node->getChild(Node::POSITION_RIGHT))
                ->setParent($node->getParent())
                ->setColor($node->getColor())
            ;
            if ($node->haveChild(Node::POSITION_LEFT)) {
                $node->getChild(Node::POSITION_LEFT)->setParent($tmp);
            }
            if ($node->haveChild(Node::POSITION_RIGHT)) {
                $node->getChild(Node::POSITION_RIGHT)->setParent($tmp);
            }

            if (null !== $node->getParent()) {
                $node->getParent()->setChild($node->getPosition(), $tmp);
            }
        } else {
            if (Node::COLOR_BLACK === $node->getColor()) {
                $this->deleteSort($node);
            }
        }

        // Make original node orphan
        if (null !== $node->getParent() && $node === $node->getParent()->getChild($node->getPosition())) {
            $node->getParent()->setChild($node->getPosition(), null);
        }
        $node
            ->setPosition(null)
            ->setParent(null)
            ->setChild(Node::POSITION_LEFT, null)
            ->setChild(Node::POSITION_RIGHT, null)
        ;

        return $this;
    }

    /**
     * Do rotations on related places on deletion
     *
     * @param Node $node
     * @return $this
     */
    protected function deleteSort(Node $node)
    {
        // If is root or black, go back
        if (Node::COLOR_BLACK !== $node->getColor() || null === $node->getParent()) {
            return $this;
        }

        $direction = $node->getPosition();
        $tmp = $node->getParent()->getChild(-$direction);
        if (null !== $tmp) {
            if (Node::COLOR_RED === $tmp->getColor()) {
                $tmp->setColor(Node::COLOR_BLACK);
                $node->getParent()->setColor(Node::COLOR_RED);
                $this->rotate($node->getParent(), $direction);
                $tmp = $node->getParent()->getChild(-$direction);
            }

            if ($tmp->haveChild(Node::POSITION_LEFT) && $tmp->getChild(Node::POSITION_LEFT)->getColor() === Node::COLOR_BLACK
             && $tmp->haveChild(Node::POSITION_RIGHT) && $tmp->getChild(Node::POSITION_RIGHT)->getColor() === Node::COLOR_BLACK
            ) {
                $tmp->setColor(Node::COLOR_RED);
                return $this->deleteSort($node->getParent());
            } else {
                if ($tmp->haveChild(-$direction) && $tmp->getChild(-$direction)->getColor() === Node::COLOR_BLACK) {
                    if ($tmp->haveChild($direction)) {
                        $tmp->getChild($direction)->setColor(Node::COLOR_BLACK);
                    }
                    $tmp->setColor(Node::COLOR_RED);
                    $this->rotate($tmp, -$direction);
                    $tmp = $node->getParent()->getChild(-$direction);
                }

                $tmp->setColor($node->getParent()->getColor());
                $node->getParent()->setColor(Node::COLOR_BLACK);
                if ($tmp->getChild(-$direction)) {
                    $tmp->getChild(-$direction)->setColor(Node::COLOR_BLACK);
                }
                $this->rotate($node->getParent(), $direction);
                $node = $this->root;
            }
        }

        return $this->deleteSort($node->setColor(Node::COLOR_BLACK));
    }

    /**
     * Get nodes with id between min and max (inclusive)
     *
     * @param int $min
     * @param int $max
     * @return array|Node[]
     */
    public function enumerate($min, $max)
    {
        $out = [];
        $closest = $this->searchClosestNode($this->root, $min);
        // Include the first match only if above or equal to $min
        if ($this->compare($min, $closest->getId()) >= 0) {
            $out[] = $closest;
        }

        while (true) {
            // Get next node after $closest
            $nextNode = $this->findRelative($closest, Node::POSITION_RIGHT);
            if (null !== $nextNode // Not an end
             && $this->compare($max, $nextNode->getId()) <= 0 // Must be above
             && 0 != $this->compare($closest->getId(), $nextNode->getId()) // Not equal, should not happen
            ) {
                $out[] = $nextNode;
            } else {
                break;
            }

            $closest = $nextNode;
        }

        return $out;
    }

    /**
     * Return all Nodes in infixe ordered list.
     * The optional node argument is the starting point.
     * In most case it should be empty.
     *
     * @param Node|null $node
     * @return array|Node[]
     */
    public function infixeList(Node $node = null)
    {
        if (null === $node) {
            $node = $this->root;
        }

        $out = [];

        if (null !== $node && $node->haveChild(Node::POSITION_LEFT)) {
            $out = array_merge($out, $this->infixeList($node->getChild(Node::POSITION_LEFT)));
        }

        $out[] = $node;

        if (null !== $node && $node->haveChild(Node::POSITION_RIGHT)) {
            $out = array_merge($out, $this->infixeList($node->getChild(Node::POSITION_RIGHT)));
        }

        return $out;
    }

    /**
     * Compare two ids
     * If A is bellow B, return 1
     * If A is above B, return -1
     * If A is equal to B, return 0
     * It for syntax reason.
     *
     * @param mixed $idA
     * @param mixed $idB
     * @return bool
     */
    abstract protected function compare($idA, $idB);
}
