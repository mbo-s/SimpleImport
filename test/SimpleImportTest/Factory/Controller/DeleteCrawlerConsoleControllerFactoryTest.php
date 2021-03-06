<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace SimpleImportTest\Factory\Controller;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use SimpleImport\Factory\Controller\DeleteCrawlerConsoleControllerFactory;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \SimpleImport\Factory\Controller\DeleteCrawlerConsoleControllerFactory
 * 
 * @covers \SimpleImport\Factory\Controller\DeleteCrawlerConsoleControllerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class DeleteCrawlerConsoleControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = DeleteCrawlerConsoleControllerFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testInvokationCreatesController()
    {
        $crawlerRepoMock = $this->getMockBuilder(\SimpleImport\Repository\Crawler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $jobRepository = $this->getMockBuilder(\Jobs\Repository\Job::class)->disableOriginalConstructor()->getMock();
        $repositories = $this->createPluginManagerMock([
            'SimpleImport/Crawler' => ['service' => $crawlerRepoMock, 'count_get' => 1],
            'Jobs' => ['service' => $jobRepository, 'count_get' => 1],
        ]);

        $services = $this->getServiceManagerMock();
        $services->setService('repositories', $repositories);
        $services->setExpectedCallCount('get', 'repositories', 1);

        $controller = $this->target->__invoke($services, 'irrelevant');

        $this->assertInstanceOf(\SimpleImport\Controller\DeleteCrawlerConsoleController::class, $controller);
    }
}
