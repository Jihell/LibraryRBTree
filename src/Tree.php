<?php
/**
 * @package v4
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree;

/**
 * Class Tree
 *
 * Implementation of Red Black Tree.
 *
 * - A node is either red or black.
 * - The root is black. This rule is sometimes omitted. Since the root can always be changed from red to black, but not necessarily vice versa, this rule has little effect on analysis.
 * - All leaves (NIL) are black. (here the NIL are just empty node)
 * - If a node is red, then both its children are black.
 * - Every path from a given node to any of its descendant NIL nodes contains the same number of black nodes. The uniform number of black nodes in the paths from root to leaves is called the black-height of the red–black tree.
 */
class Tree
{
    /**
     * Root node of the tree
     *
     * @access protected
     * @var Node
     */
    protected $root;

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
        $this->root = $node;
    }

    /**
     * Insert a node
     *
     * @param  Node $node
     * @return Node New inserted node with child and parent nodes set
     */
    public function insert(Node $node)
    {
        // If root is null, set as root
        if (null === $this->root) {
            $this->root = $node;
        // Else Find the new parent
        } else {
            $insertNode = $this->recursiveSearchClosestNode($this->root, $node->getId());
            $insertNode->setChild($this->compare($insertNode->getId(), $node->getId()), $node);
        }
        // New node is red
        $node->setColor(Node::COLOR_RED);

        $this->recursiveSortNode($node);
        $this->root->setColor(Node::COLOR_BLACK);

        return $this;
    }

    /**
     * @param Node $node
     * @return $this
     */
    protected function recursiveSortNode(Node $node)
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
                return $this->recursiveSortNode($parent);
            // Else no rotation needed just fix the color
            } else {
                $uncle->setColor(Node::COLOR_BLACK);
                $node->getParent()->setColor(Node::COLOR_BLACK);
                if (null !== $grandParent) {
                    $grandParent->setColor(Node::COLOR_RED);
                }
                return $this->recursiveSortNode($grandParent);
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
                return $this->recursiveSortNode($grandParent);
            // Else if we have a grand parent (so the same direction as parent)
            } elseif (null !== $grandParent) {
                $node->getParent()->setColor(Node::COLOR_BLACK);
                $grandParent->setColor(Node::COLOR_RED);
                $this->rotate($grandParent, -$node->getPosition());
                return $this->recursiveSortNode($grandParent);
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
    protected function recursiveSearchClosestNode(Node $hierarchy, $id)
    {
        $position = $this->compare($hierarchy->getId(), $id);
        if ($hierarchy->isLeaf() || 0 === $position || !$hierarchy->haveChild($position)) {
            return $hierarchy;
        }

        return $this->recursiveSearchClosestNode($hierarchy->getChild($position), $id);
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
            $node->getParent()->setChild(($toPosition === $node->getPosition() ? 1 : -1 )* $toPosition, $tmp);
        }
        $tmp->setChild($toPosition, $node);
        $node->setParent($tmp);

        // Rotation done, it's possible the root have changed
        if (null === $tmp->getParent()) {
            $this->root = $tmp;
        }

        return $this;
    }

    /**
     * Find a node by id.
     * Recursive operation, the optional $node should not be given
     *
     * @param int $id
     * @param Node $node
     * @return false|Node
     */
    public function findId($id, Node $node = null)
    {
        // Initialize if first iteration
        if (null === $node) {
            $node = $this->root;
        }

        // If the id is equal, it's our match !
        $position = $this->compare($node->getId(), $id);
        if (0 === $position) {
            return $node;
        }

        // Else if it's a nil, return false, else recursion
        return $node->isLeaf() ? false : $this->findId($id, $node->getChild($position));
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
        return $node->getPosition() === $node->getParent()->getPosition() ? $node->getParent() : $node->getGrandParent() ;
    }

    /**
     * Remove the node from the tree
     *
     * @param Node $node
     * @return $this
     */
    public function remove(Node $node)
    {
        // First case, the node is a leaf, we can remove it safely
        if ($node->isLeaf()) {
            // Make parent reject node
            $node->getParent()->setChild($node->getPosition(), null);
            // Make orphan
            $node
                ->setPosition(null)
                ->setParent(null)
            ;

            return $this;
        }

        // Second case, the node have one or two children.
        // Move children the way of node upward if possible, else the other way
        $tmp = $this->findRelative($node, $node->getPosition()) ?: $this->findRelative($node, -$node->getPosition());
        // Attach temp node to node's parent
        $tmp->setParent($node->getParent());
        $node->getParent()->setChild($node->getPosition(), $tmp);
        // Temp is black, so node is red, so we have two black consecutive and that's not normal
        if (Node::COLOR_BLACK === $tmp->getColor()) {
            $this->recursiveSortNode($tmp);
        }
        $tmp->setColor($node->getColor());
        // Make orphan
        $node
            ->setPosition(null)
            ->setParent(null)
        ;

        return $this;
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
        $closest = $this->recursiveSearchClosestNode($this->root, $min);
        // Include the first match only if above or equal to $min
        if ($this->compare($closest->getId(), $min) >= 0) {
            $out[] = $closest;
        }

        while(true) {
            // Get next node after $closest
            $nextNode = $this->findRelative($closest, Node::POSITION_RIGHT);
            if ($this->compare($nextNode->getId(), $max) <= 0
             && 0 != $this->compare($closest->getId(), $nextNode->getId())
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
     * Compare two ids
     * If A is bellow B, return 1
     * If A is above B, return -1
     * If A is equal to B, return 0
     *
     * @param int $idA
     * @param int $idB
     * @return bool
     * @throws \Exception
     */
    protected function compare($idA, $idB)
    {
        if ($idA == $idB) {
            return 0;
        }
        return $idA < $idB ? 1 : -1;
    }
}
