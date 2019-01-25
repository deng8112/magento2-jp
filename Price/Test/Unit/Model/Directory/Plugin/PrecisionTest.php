<?php
declare(strict_types=1);

namespace MagentoJapan\Price\Test\Unit\Model\Directory\Plugin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class PrecisionTest extends TestCase
{
    /**
     * @var \MagentoJapan\Price\Model\Directory\Plugin\Precision
     */
    private $precisionPlugin;

    /**
     * @var \Closure
     */
    private $closure;

    /**
     * @var \MagentoJapan\Price\Model\Config\System|\PHPUnit_Framework_MockObject_MockObject
     */
    private $systemMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->systemMock = $this->getMockBuilder(\MagentoJapan\Price\Model\Config\System::class)
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new ObjectManager($this);
        $this->precisionPlugin = $objectManager->getObject(
            \MagentoJapan\Price\Model\Directory\Plugin\Precision::class,
            ['system' => $this->systemMock]
        );

        $this->closure = function () {
            return '<span class="price">￥100</span>';
        };
    }

    /**
     * Test for aroundFormatPrecision to JPY
     *
     * @return void
     */
    public function testJpyAroundFormatPrecision()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Directory\Model\Currency $currency */
        $currency = $this->getMockBuilder(\Magento\Directory\Model\Currency::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency->expects($this->atLeastOnce())
            ->method('getCode')->willReturn('JPY');
        $this->systemMock->expects($this->atLeastOnce())
            ->method('getIntegerCurrencies')->willReturn(['JPY']);


        $this->assertEquals(
            '<span class="price">￥100</span>',
            $this->precisionPlugin
                ->aroundFormatPrecision(
                    $currency,
                    $this->closure,
                    100.49,
                    [],
                    true,
                    false
                )
        );
    }

    /**
     * Test for aroundFormatPrecision to others
     *
     * @return void
     */
    public function testNonJpyAroundFormatPrecision()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Directory\Model\Currency $currency */
        $currency = $this->getMockBuilder(\Magento\Directory\Model\Currency::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency->expects($this->atLeastOnce())
            ->method('getCode')->willReturn('USD');
        $this->systemMock->expects($this->atLeastOnce())
            ->method('getIntegerCurrencies')->willReturn(['JPY']);

        $this->assertNotEquals(
            '<span class="price">￥100.49</span>',
            $this->precisionPlugin
                ->aroundFormatPrecision(
                    $currency,
                    $this->closure,
                    100.49,
                    [],
                    true,
                    false
                )
        );
    }
}
