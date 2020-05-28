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

namespace Aplorm\Lexer\Behat\Context;

use Aplorm\Lexer\Behat\Bootstrap\CoverageFactory;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover;
use SebastianBergmann\CodeCoverage\Report\Html\Facade;

trait CoverageTrait
{
    /**
     * @var CodeCoverage
     */
    private static $coverage;

    private static function getDir(): string
    {
        return $_SERVER['TRAVIS_BUILD_DIR'] ?? $_ENV['PWD'];
    }

    /**
     * @BeforeSuite
     */
    public static function setup(BeforeSuiteScope $scope): void
    {
        static::$coverage = CoverageFactory::getInstance();
    }

    /**
     * @AfterSuite
     */
    public static function tearDown(AfterSuiteScope $scope): void
    {
        if (isset($_SERVER['TRAVIS_BUILD_DIR'])) {
            $writer = new Clover();
            $writer->process(static::$coverage, self::getDir().'/coverage-behat.xml');
        } else {
            $writer = new Facade();
            $writer->process(static::$coverage, self::getDir().'/public/coverage-behat');
        }
    }

    /**
     * @BeforeScenario
     */
    public function startCoverage(BeforeScenarioScope $scope): void
    {
        static::$coverage->start("{$scope->getFeature()->getTitle()}::{$scope->getScenario()->getTitle()}");
    }

    /**
     * @AfterScenario
     */
    public function stopCoverage(AfterScenarioScope $scope): void
    {
        static::$coverage->stop();
    }
}
