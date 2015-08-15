<?php
namespace Scheb\Tombstone\Analyzer\Source;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitor\NameResolver;
use Scheb\Tombstone\Analyzer\TombstoneList;

class TombstoneVisitor extends NameResolver
{
    /**
     * @var string|null
     */
    private $currentClass = null;

    /**
     * @var string[]
     */
    private $currentMethod = array();

    /**
     * @var string
     */
    private $file;

    /**
     * @var TombstoneList
     */
    private $tombstoneList;

    /**
     * @param TombstoneList $tombstoneList
     */
    public function __construct(TombstoneList $tombstoneList)
    {
        $this->tombstoneList = $tombstoneList;
    }

    /**
     * @param string $file
     */
    public function setCurrentFile($file)
    {
        $this->file = $file;
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        if ($node instanceof Class_) {
            $this->currentClass = (string) $node->namespacedName;
        }
        if ($node instanceof ClassMethod) {
            $methodName = $this->currentClass . ($node->isStatic() ? '::' : '->') . $node->name;
            $this->currentMethod[] = $methodName;
        }
        if ($node instanceof Function_) {
            $this->currentMethod[] = (string) $node->namespacedName;
        }
        if ($node instanceof FuncCall) {
            if ($this->isTombstoneFunction($node)) {
                $line = $node->getLine();
                $methodName = $this->getCurrentMethodName();
                $params = $this->extractParameters($node);
                $date = isset($params[0]) ? $params[0] : null;
                $author = isset($params[1]) ? $params[1] : null;
                $this->tombstoneList->addTombstone($this->file, $line, $methodName, $date, $author);
            }
        }
    }

    public function leaveNode(Node $node)
    {
        parent::leaveNode($node);
        if ($node instanceof ClassMethod || $node instanceof Function_) {
            array_pop($this->currentMethod);
        }
    }

    /**
     * @param FuncCall $node
     *
     * @return bool
     */
    private function isTombstoneFunction(FuncCall $node)
    {
        $nameParts = $node->name->parts;
        return count($nameParts) == 1 && $nameParts[0] == "tombstone";
    }

    /**
     * @return null
     */
    private function getCurrentMethodName()
    {
        end($this->currentMethod);
        return $this->currentMethod ? $this->currentMethod[key($this->currentMethod)] : null;
    }

    /**
     * @return TombstoneList
     */
    public function getTombstones()
    {
        return $this->tombstoneList;
    }

    /**
     * @param FuncCall $node
     *
     * @return string[]
     */
    private function extractParameters(FuncCall $node)
    {
        $params = array();
        foreach ($node->args as $arg) {
            if ($arg->value instanceof String_) {
                $params[] = $arg->value->value;
            } else {
                $params[] = null;
            }
        }

        return $params;
    }
}
