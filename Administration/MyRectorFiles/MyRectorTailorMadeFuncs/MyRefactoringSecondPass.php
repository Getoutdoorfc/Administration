<?php

declare(strict_types=1);

namespace Administration\MyRectorFiles\MyRectorTailorMadeFuncs;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use Rector\Rector\AbstractRector;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use Administration\MyRectorFiles\MyRectorTailorMadeFuncs\MyRefactoringHelpers;

final class MyRefactoringSecondPass extends AbstractRector implements ConfigurableRectorInterface, DocumentedRuleInterface
{
    use MyRefactoringHelpers;

    private array $projectPaths = [];
    private MyRectorLoggingHandler $logger;
    private string $timestamp;

    public function configure(array $configuration): void
    {
        $this->projectPaths = $configuration['projectPaths'] ?? [];
        $this->timestamp = $configuration['timestamp'] ?? date('Ymd_His');
        $this->logger = MyRectorLoggingHandler::getInstance(__DIR__, $this->timestamp);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Andet pass: Anvender klasseomdøbninger til at opdatere namespaces, klassenavne, use-statements og referencer.',
            []
        );
    }

    public function getNodeTypes(): array
    {
        return [
            Stmt\Namespace_::class,
            Stmt\Class_::class,
            Stmt\Use_::class,
            Expr\New_::class,
            Expr\StaticCall::class,
            Expr\StaticPropertyFetch::class,
            Expr\ClassConstFetch::class,
            Expr\Instanceof_::class,
            Expr\FuncCall::class,
            Expr\PropertyFetch::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        $classRenames = ClassRenameCollector::getClassRenames();

        // Kun hente filstien for Namespace_ og Class_ noder
        if ($node instanceof Stmt\Namespace_ || $node instanceof Stmt\Class_) {
            $filePath = $this->getFilePath($node);

            if ($filePath === null) {
                $this->logger->log("Kunne ikke bestemme filstien for noden.", 'WARNING');
                return null;
            }

            $expectedNamespace = $this->getExpectedNamespace($filePath);
            $expectedClassName = $this->getExpectedClassName($filePath);

            if ($expectedNamespace === null || $expectedClassName === null) {
                $this->logger->log("Kunne ikke bestemme forventet namespace eller klassenavn for fil: {$filePath}", 'WARNING');
                return null;
            }

            // Opdater namespace
            if ($node instanceof Stmt\Namespace_) {
                if ($this->getName($node) !== $expectedNamespace) {
                    $this->logger->log("Opdaterer namespace i fil {$filePath} til {$expectedNamespace}");
                    $node->name = new Node\Name($expectedNamespace);
                }
            }

            // Opdater klassenavn
            if ($node instanceof Stmt\Class_) {
                $currentClassName = $this->getName($node);
                if ($currentClassName !== $expectedClassName) {
                    $this->logger->log("Ændrer klassenavn i fil {$filePath} fra {$currentClassName} til {$expectedClassName}");
                    $node->name = new Node\Identifier($expectedClassName);
                }
            }
        }

        // Opdater use-statements
        if ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $useName = $use->name->toString();
                if (isset($classRenames[$useName])) {
                    $newUseName = $classRenames[$useName];
                    $this->logger->log("Opdaterer use-statement fra {$useName} til {$newUseName}");
                    $use->name = new Node\Name($newUseName);
                }
            }
        }

        // Opdater klassereferencer
        $newNode = $this->updateClassReferences($node, $classRenames);

        if ($newNode !== null) {
            return $newNode;
        }

        // Opdater PHPDoc-kommentarer
        $this->updatePhpDoc($node, $classRenames);

        return $node;
    }
}
