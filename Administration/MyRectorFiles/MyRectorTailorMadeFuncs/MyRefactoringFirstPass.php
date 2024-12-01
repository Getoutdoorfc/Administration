<?php

declare(strict_types=1);

namespace Administration\MyRectorFiles\MyRectorTailorMadeFuncs;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use Administration\MyRectorFiles\MyRectorTailorMadeFuncs\MyRefactoringHelpers;

final class MyRefactoringFirstPass extends AbstractRector implements ConfigurableRectorInterface, DocumentedRuleInterface
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
            'Første pass: Indsamler klasseomdøbninger baseret på filstruktur og PSR-4 standarder.',
            []
        );
    }

    public function getNodeTypes(): array
    {
        return [
            Class_::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Class_) {
            $filePath = $this->getFilePath($node);
            if ($filePath === null) {
                // Kun logge advarsel, hvis noden er en klasse
                $this->logger->log("Kunne ikke bestemme filstien for noden.", 'WARNING');
                return null;
            }

            $expectedNamespace = $this->getExpectedNamespace($filePath);
            $expectedClassName = $this->getExpectedClassName($filePath);
            if ($expectedNamespace === null || $expectedClassName === null) {
                $this->logger->log("Kunne ikke bestemme forventet namespace eller klassenavn for fil: {$filePath}", 'WARNING');
                return null;
            }

            $currentClassName = $this->getName($node);
            $oldFullyQualifiedClassName = $this->getName($node);
            $newFullyQualifiedClassName = $expectedNamespace . '\\' . $expectedClassName;

            if ($oldFullyQualifiedClassName !== $newFullyQualifiedClassName) {
                ClassRenameCollector::addClassRename($oldFullyQualifiedClassName, $newFullyQualifiedClassName);
                $this->logger->log("Tilføjer omdøbning fra {$oldFullyQualifiedClassName} til {$newFullyQualifiedClassName}");
            }
        }
        return null;
    }
}
