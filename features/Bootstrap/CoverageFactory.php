<?php
/**
 *  This file is part of the Aplorm package.
 *
 *  (c) Nicolas Moral <n.moral@live.fr>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Aplorm\Lexer\Behat\Bootstrap;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;

class CoverageFactory
{
    private static ?CodeCoverage $coverage = null;

    public static function getInstance(): ?CodeCoverage
    {
        if (null !== self::$coverage) {
            return self::$coverage;
        }

        if (isset($_SERVER['TRAVIS_BUILD_DIR'])) {
            $dir = $_SERVER['TRAVIS_BUILD_DIR'];
        } else {
            $dir = $_ENV['PWD'];
        }

        $filter = new Filter();
        $filter->addDirectoryToWhitelist($dir.'/src');

        self::$coverage = new CodeCoverage(null, $filter);
        self::$coverage->setDisableIgnoredLines(false);
        self::$coverage->setCheckForUnexecutedCoveredCode(false);
        return self::$coverage;
    }
}
