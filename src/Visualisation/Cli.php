<?php
/**
 * @package v4
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree\Visualisation;

use Jihel\Library\RBTree\Model\NodeInterface as Node;
use Jihel\Library\RBTree\Model\TreeInterface as Tree;

/**
 * Class Cli
 *
 * Provide a simple cli visualisation
 */
class Cli
{
    /**
     * Simple recursive visualisation.
     *
     * @param Tree $tree
     * @return string
     */
    public function render(Tree $tree)
    {
        return $this->infixeRender($tree->getRoot());
    }

    /**
     * Simple recursive visualisation.
     * The first node should be the root.
     * Colors are for linux console, sorry windows guys :p
     *
     * @param Node $node
     * @param string $indent
     * @return string The max indentation
     */
    protected function infixeRender(Node $node, $indent = '')
    {
        if (!$node->isLeaf() && $node->haveChild(Node::POSITION_LEFT)) {
            // Show left side first
            $this->infixeRender($node->getChild(Node::POSITION_LEFT), $indent.'  ');
        }

        echo sprintf('%s%s#%d', $indent, $node->getPosition() == 0 ? '-' : ($node->getPosition() > 0 ? '\\': '/'), $node->getId());
        echo ' L#'.($node->haveChild(Node::POSITION_LEFT) ? $node->getChild(Node::POSITION_LEFT)->getId() : '-');
        echo ' R#'.($node->haveChild(Node::POSITION_RIGHT) ? $node->getChild(Node::POSITION_RIGHT)->getId() : '-');
        echo ' P#'.(null !== $node->getParent() ? $node->getParent()->getId() : '-');
        echo ' C:'.(Node::COLOR_RED === $node->getColor() ? "\033[0;31mRED\033[0m" : "\033[0;32mBLACK\033[0m");
        echo PHP_EOL;

        if (!$node->isLeaf() && $node->haveChild(Node::POSITION_RIGHT)) {
            // Show left side first
            $this->infixeRender($node->getChild(Node::POSITION_RIGHT), $indent.'  ');
        }
    }
}
