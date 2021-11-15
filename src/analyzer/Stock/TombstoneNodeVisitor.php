<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Stock;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitor\NameResolver;

class TombstoneNodeVisitor extends NameResolver
{
    /**
     * @var string|null
     */
    private $currentClass = null;

    /**
     * @var string[]
     */
    private $currentMethod = [];

    /**
     * @var TombstoneExtractor
     */
    private $tombstoneCallback;

    /**
     * @var string[]
     */
    private $tombstoneFunctionNames;

    public function __construct(TombstoneExtractor $tombstoneCallback, array $tombstoneFunctionNames)
    {
        parent::__construct();
        $this->tombstoneCallback = $tombstoneCallback;
        $this->tombstoneFunctionNames = $tombstoneFunctionNames;
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        if ($node instanceof Class_) {
            $this->visitClassNode($node);
        } elseif ($node instanceof ClassMethod) {
            $this->visitMethodNode($node);
        } elseif ($node instanceof Function_) {
            $this->visitFunctionNode($node);
        } elseif ($node instanceof FuncCall) {
            $this->visitFunctionCallNode($node);
        } elseif ($node instanceof StaticCall) {
            $this->visitStaticCallNode($node);
        }
    }

    private function visitClassNode(Class_ $node): void
    {
        $this->currentClass = $this->getNamespacedName($node);
    }

    private function visitMethodNode(ClassMethod $node): void
    {
        $methodName = $this->currentClass.($node->isStatic() ? '::' : '->').$node->name;
        $this->currentMethod[] = $methodName;
    }

    private function visitFunctionNode(Function_ $node): void
    {
        $this->currentMethod[] = $this->getNamespacedName($node);
    }

    /**
     * @param Class_|Function_ $node
     */
    private function getNamespacedName($node): string
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (!isset($node->namespacedName)) {
            $nodeName = isset($node->name) ? (string) $node->name : '<anonymous>';
            throw new \RuntimeException(sprintf('Node %s of type %s did not provide attribute namespacedName', $nodeName, \get_class($node)));
        }

        return (string) $node->namespacedName;
    }

    private function visitFunctionCallNode(FuncCall $node): void
    {
        if ($this->isTombstoneFunction($node)) {
            /** @psalm-suppress PossiblyInvalidCast */
            $functionName = (string) $node->name;
            $line = $node->getLine();
            $methodName = $this->getCurrentMethodName();
            $arguments = $this->extractArguments($node->args);
            /** @psalm-suppress PossiblyNullArgument */
            $this->tombstoneCallback->onTombstoneFound($functionName, $arguments, $line, $methodName);
        }
    }

    private function visitStaticCallNode(StaticCall $node): void
    {
        if ($this->isStaticTombstoneMethod($node)) {
            /** @psalm-suppress PossiblyInvalidCast */
            $tombstoneMethodName = (string) $node->class.'::'.(string) $node->name;
            $line = $node->getLine();
            $methodName = $this->getCurrentMethodName();
            $arguments = $this->extractArguments($node->args);
            /** @psalm-suppress PossiblyNullArgument */
            $this->tombstoneCallback->onTombstoneFound($tombstoneMethodName, $arguments, $line, $methodName);
        }
    }

    public function leaveNode(Node $node)
    {
        parent::leaveNode($node);
        if ($node instanceof ClassMethod || $node instanceof Function_) {
            array_pop($this->currentMethod);
        }
    }

    private function isTombstoneFunction(FuncCall $node): bool
    {
        // Function name must be available
        if (!($node->name instanceof Node\Name)) {
            return false;
        }

        $fqn = (string) $node->name;

        // Unambiguous calls resolving to a FQN
        if (\in_array($fqn, $this->tombstoneFunctionNames)) {
            return true;
        }

        // Ambiguous calls that may refer to the local namespace
        if ($node->name->hasAttribute('namespacedName')) {
            $fqn = (string) $node->name->getAttribute('namespacedName');
            if (\in_array($fqn, $this->tombstoneFunctionNames)) {
                return true;
            }
        }

        return false;
    }

    private function isStaticTombstoneMethod(StaticCall $node): bool
    {
        // Class and method name must be available
        if (!($node->class instanceof Node\Name && $node->name instanceof Node\Identifier)) {
            return false;
        }

        $fqn = (string) $node->class.'::'.(string) $node->name;

        // Unambiguous calls resolving to a FQN
        return \in_array($fqn, $this->tombstoneFunctionNames);
    }

    private function getCurrentMethodName(): ?string
    {
        end($this->currentMethod);

        return $this->currentMethod ? $this->currentMethod[key($this->currentMethod)] : null;
    }

    /**
     * @param Node\Arg[]|Node\VariadicPlaceholder[] $args
     *
     * @return list<string|null>
     */
    private function extractArguments(array $args): array
    {
        $params = [];
        foreach ($args as $arg) {
            if ($arg instanceof Node\VariadicPlaceholder) {
                break; // Can't extract from ...$var arguments
            } elseif ($arg->value instanceof String_) {
                /** @psalm-suppress RedundantCastGivenDocblockType */
                $params[] = (string) $arg->value->value;
            } else {
                $params[] = null;
            }
        }

        return $params;
    }
}
