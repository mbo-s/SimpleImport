<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.30
 */

namespace SimpleImportTest\Validator;

use SimpleImport\Validator\OrganizationExists;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\Validator\AbstractValidator;
use Organizations\Repository\Organization as OrganizationRepository;
use Organizations\Entity\Organization;

/**
 * @coversDefaultClass \SimpleImport\Validator\OrganizationExists
 */
class OrganizationExistsTest extends \PHPUnit_Framework_TestCase
{

    use TestInheritanceTrait;

    /**
     * @var OrganizationExists
     */
    private $target;

    /**
     * @var OrganizationRepository
     */
    private $repository;

    /**
     * @see TestInheritanceTrait
     *
     * @var array
     */
    private $inheritance = [AbstractValidator::class];

    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->repository = $this->getMockBuilder(OrganizationRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->target = new OrganizationExists($this->repository);
    }

    /**
     * @covers ::isValid()
     * @covers ::__construct()
     */
    public function testIsValidNotExists()
    {
        $value = 'nonExistingId';

        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->identicalTo($value))
            ->willReturn(null);

        $this->assertFalse($this->target->isValid($value));
    }

    /**
     * @covers ::isValid()
     * @covers ::__construct()
     */
    public function testIsValidExists()
    {
        $value = 'existingId';

        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->identicalTo($value))
            ->willReturn(new Organization());

        $this->assertTrue($this->target->isValid($value));
    }
}
