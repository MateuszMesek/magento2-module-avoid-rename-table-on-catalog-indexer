<?php declare(strict_types=1);

namespace MateuszMesek\AvoidRenameTableOnCatalogIndexer\Plugin;

use Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher;
use Magento\Framework\DB\Adapter\AdapterInterface;
use MateuszMesek\DatabaseDataTransfer\Api\Command\TransferDataInterface;
use MateuszMesek\DatabaseDataTransfer\Api\Data\TableFactoryInterface;

class OnActiveTableSwitcher
{
    public function __construct(
        private readonly TableFactoryInterface $tableFactory,
        private readonly TransferDataInterface $transferData
    )
    {
    }

    public function aroundSwitchTable(
        ActiveTableSwitcher $activeTableSwitcher,
        callable $proceed,
        AdapterInterface $connection,
        array $tableNames
    ): void
    {
        foreach ($tableNames as $tableName) {
            $replicaTableName = $activeTableSwitcher->getAdditionalTableName($tableName);

            $this->transferData->execute(
                $this->tableFactory->create($tableName),
                $this->tableFactory->create($replicaTableName)
            );
        }
    }
}
