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
 * Implementation of Red Black Tree for integer id node's.
 */
class Tree extends Model\AbstractTree
{
    /**
     * Integer implementation
     *
     * @param int $idA
     * @param int $idB
     * @return bool
     */
    protected function compare($idA, $idB)
    {
        if ($idA == $idB) {
            return 0;
        }
        return $idA < $idB ? 1 : -1;
    }
    
    public function getMin(Model\NodeInterface $node)
    {
        if (null === $node) {
            $node = $this->root;
        }
        while ($node and $node->haveChild(Node::POSITION_LEFT)) {
            $node = $node->getChild(Node::POSITION_LEFT);
        }
        return $node;
    }

    public function getMax(Model\NodeInterface $node)
    {
        if (null === $node) {
            $node = $this->root;
        }
        while ($node and $node->haveChild(Node::POSITION_RIGHT)) {
            $node = $node->getChild(Node::POSITION_RIGHT);
        }
        return $node;
    }
}
