<?php
/**
 *
 * Adyen Payment module (https://www.adyen.com/)
 *
 * Copyright (c) 2023 Adyen N.V. (https://www.adyen.com/)
 * See LICENSE.txt for license details.
 *
 * Author: Adyen <magento@adyen.com>
 */

namespace Adyen\Payment\Test\Unit\Helper;

use Adyen\Payment\Helper\StateData;
use Adyen\Payment\Model\ResourceModel\StateData as StateDataResourceModel;
use Adyen\Payment\Model\ResourceModel\StateData\Collection as StateDataCollection;
use Adyen\Payment\Test\Unit\AbstractAdyenTestCase;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Adyen\Payment\Model\StateDataFactory;

class StateDataTest extends AbstractAdyenTestCase
{
    private $stateDataHelper;
    private $stateDataCollectionMock;
    private $stateDataFactoryMock;
    private $stateDataResourceModelMock;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->stateDataCollectionMock = $this->createMock(StateDataCollection::class);
        $this->stateDataResourceModelMock = $this->createMock(StateDataResourceModel::class);

        $stateDataMock = $this->createMock(\Adyen\Payment\Model\StateData::class);

        $this->stateDataFactoryMock = $this->createGeneratedMock(StateDataFactory::class, ['create']);
        $this->stateDataFactoryMock->method('create')->willReturn($stateDataMock);

        $this->stateDataHelper = $this->objectManager->getObject(StateData::class, [
            'stateDataCollection' => $this->stateDataCollectionMock,
            'stateDataResourceModel' => $this->stateDataResourceModelMock,
            'stateDataFactory' => $this->stateDataFactoryMock
        ]);
    }

    public function testSaveStateDataSuccessful()
    {
        $stateData = '{"stateData":"dummyData"}';
        $quoteId = 1;

        $stateDataMock = $this->createConfiguredMock(\Adyen\Payment\Model\StateData::class, [
            'getData' => ['entity_id' => 1, 'quote_id' => 1]
        ]);

        $this->stateDataCollectionMock->method('addFieldToFilter')->willReturnSelf();
        $this->stateDataCollectionMock->method('getFirstItem')->willReturn($stateDataMock);
        $this->stateDataResourceModelMock->expects($this->once())->method('save');

        $this->stateDataHelper->saveStateData($stateData, $quoteId);
    }

    public function testRemoveStateDataSuccessful()
    {
        $stateDataId = 1;
        $quoteId = 1;

        $stateDataMock = $this->createConfiguredMock(\Adyen\Payment\Model\StateData::class, [
            'getData' => ['entity_id' => 1, 'quote_id' => 1]
        ]);

        $this->stateDataCollectionMock->method('addFieldToFilter')->willReturnSelf();
        $this->stateDataCollectionMock->method('getFirstItem')->willReturn($stateDataMock);

        $this->assertTrue($this->stateDataHelper->removeStateData($stateDataId, $quoteId));
    }

    public function testRemoveStateDataException()
    {
        $this->expectException(NoSuchEntityException::class);

        $stateDataId = 1;
        $quoteId = 1;

        $stateDataMock = $this->createConfiguredMock(\Adyen\Payment\Model\StateData::class, [
            'getData' => null
        ]);

        $this->stateDataCollectionMock->method('addFieldToFilter')->willReturnSelf();
        $this->stateDataCollectionMock->method('getFirstItem')->willReturn($stateDataMock);

        $this->stateDataHelper->removeStateData($stateDataId, $quoteId);
    }
}
