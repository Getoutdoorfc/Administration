<?php

declare(strict_types=1);

namespace Administration\MyRectorFiles\MyRectorTailorMadeFuncs;

/**
 * Samler og deler mappingen af klasseomdøbninger på tværs af filer.
 */
class ClassRenameCollector
{
    /**
     * @var array<string, string> Mapping fra gamle til nye fuldt kvalificerede klassenavne.
     */
    private static array $classRenames = [];

    /**
     * Tilføjer en omdøbning til samlingen.
     *
     * @param string $oldClassName Det gamle fuldt kvalificerede klassenavn.
     * @param string $newClassName Det nye fuldt kvalificerede klassenavn.
     */
    public static function addClassRename(string $oldClassName, string $newClassName): void
    {
        self::$classRenames[$oldClassName] = $newClassName;
    }

    /**
     * Henter alle klasseomdøbninger.
     *
     * @return array<string, string> Mappingen af klasseomdøbninger.
     */
    public static function getClassRenames(): array
    {
        return self::$classRenames;
    }
}
