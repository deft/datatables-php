<?php

namespace Deft\DataTables\Tests\DataSource\ORM;

use Deft\DataTables\DataSource\ORM\PropertyPathMappingFactory;
use Doctrine\Tests\Mocks\ConnectionMock;
use Doctrine\Tests\Mocks\DriverMock;
use Doctrine\Tests\Mocks\EntityManagerMock;

class PropertyPathMappingFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PropertyPathMappingFactory
     */
    protected $factory;

    /**
     * @var EntityManagerMock
     */
    protected $em;

    public function setUp()
    {
        $this->factory = new PropertyPathMappingFactory();
        $this->em = EntityManagerMock::create(new ConnectionMock([], new DriverMock()));
    }

    public function testCreate_simple()
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from('Product', 'p')
        ;
        $fieldMapping = ['p.name', 'p.price'];

        $propertyPathMapping = $this->factory->createPropertyPathMapping($qb, $fieldMapping);
        $this->assertEquals(['name', 'price'], $propertyPathMapping);
    }

    public function testCreate_join()
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p, c')
            ->from('Product', 'p')
            ->join('p.category', 'c')
        ;
        $fieldMapping = ['p.name', 'c.name'];

        $propertyPathMapping = $this->factory->createPropertyPathMapping($qb, $fieldMapping);
        $this->assertEquals(['name', 'category.name'], $propertyPathMapping);
    }

    public function testCreate_multijoin()
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p, c')
            ->from('Product', 'p')
            ->join('p.category', 'c')
            ->join('c.parent', 'cp')
        ;
        $fieldMapping = ['p.name', 'c.name', 'cp.name'];

        $propertyPathMapping = $this->factory->createPropertyPathMapping($qb, $fieldMapping);
        $this->assertEquals(['name', 'category.name', 'category.parent.name'], $propertyPathMapping);
    }
}
