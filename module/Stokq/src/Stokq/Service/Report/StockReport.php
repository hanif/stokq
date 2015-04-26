<?php

namespace Stokq\Service\Report;

use Stokq\Service\AbstractService;

/**
 * Class StockReport
 * @package Stokq\Service\Report
 */
class StockReport extends AbstractService
{
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param callable $callback
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLevelChangesByType(callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('c.type AS type, COUNT(DISTINCT c.id) AS change_count');
        $builder->from('level_changes', 'c');
        $builder->groupBy('type');

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($builder->getParameters());
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}