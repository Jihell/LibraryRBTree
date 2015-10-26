<?php
/**
 * @package v4
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree;

/**
 * Class CLIVisualisation
 */
class CLIVisualisation
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
     * The first node should be the root
     *
     * @param Node $node
     * @param string $indent
     * @return string The max indentation
     */
    protected function infixeRender(Node $node, $indent = '')
    {
        $maxIndent = '';
        if (!$node->isLeaf() && $node->haveChild(Node::POSITION_LEFT)) {
            // Show left side first
            $maxIndent = $this->infixeRender($node->getChild(Node::POSITION_LEFT), $indent.'  ');
        }

        echo sprintf('%s#%d', $maxIndent, $node->getId());
        echo ' L#'.($node->haveChild(Node::POSITION_LEFT) ? $node->getChild(Node::POSITION_LEFT)->getId() : '-');
        echo ' R#'.($node->haveChild(Node::POSITION_RIGHT) ? $node->getChild(Node::POSITION_RIGHT)->getId() : '-');
        echo ' P#'.(null !== $node->getParent() ? $node->getParent()->getId() : '-');
        echo ' C:'.(Node::COLOR_RED === $node->getColor() ? 'RED' : 'BLACK');
        echo PHP_EOL;

        if (!$node->isLeaf() && $node->haveChild(Node::POSITION_RIGHT)) {
            // Show left side first
            $this->infixeRender($node->getChild(Node::POSITION_RIGHT), $indent);
        }
    }
}
