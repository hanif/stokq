<?php

namespace Stokq\Service;

use Stokq\Entity\Log;
use Stokq\Entity\Purchase;
use Stokq\Entity\Sale;
use Stokq\Entity\User;

/**
 * Class LogService
 * @package Stokq\Service
 */
class LogService extends AbstractService
{
    /**
     * @param User $user
     * @param Sale $sale
     * @param array $data
     * @return Log
     */
    public function logCreateSale(User $user, Sale $sale, array $data)
    {
        return $this->log($user, Sale::class, $sale->getId(), 'create', $data);
    }

    /**
     * @param User $user
     * @param Sale $sale
     * @param array $data
     * @return Log
     */
    public function logUpdateSale(User $user, Sale $sale, array $data)
    {
        return $this->log($user, Sale::class, $sale->getId(), 'update', $data);
    }

    /**
     * @param User $user
     * @param Purchase $purchase
     * @param array $data
     * @return Log
     */
    public function logCreatePurchase(User $user, Purchase $purchase, array $data)
    {
        return $this->log($user, Purchase::class, $purchase->getId(), 'create', $data);
    }

    /**
     * @param User $user
     * @param Purchase $purchase
     * @param string $from
     * @param string $to
     * @return Log
     */
    public function logChangePurchaseStatus(User $user, Purchase $purchase, $from, $to)
    {
        return $this->log($user, Purchase::class, $purchase->getId(), 'change_status', compact('from', 'to'));
    }

    /**
     * @param User $user
     * @param Purchase $purchase
     * @param array $data
     * @return Log
     */
    public function logUpdatePurchase(User $user, Purchase $purchase, array $data)
    {
        return $this->log($user, Purchase::class, $purchase->getId(), 'update', $data);
    }

    /**
     * @param Sale $sale
     * @return Log[]
     */
    public function findBySale(Sale $sale)
    {
        $dql = "select l, u from ent:Log l left join l.user u where l.resource_id = :id and l.resource_type = :type";
        $query = $this->em()->createQuery($dql);
        $query->setParameters([
            'id' => $sale->getId(),
            'type' => Sale::class
        ]);

        return $query->getResult();
    }

    /**
     * @param Purchase $purchase
     * @return Log[]
     */
    public function findByPurchase(Purchase $purchase)
    {
        $dql = "select l, u from ent:Log l left join l.user u where l.resource_id = :id and l.resource_type = :type";
        $query = $this->em()->createQuery($dql);
        $query->setParameters([
            'id' => $purchase->getId(),
            'type' => Purchase::class
        ]);

        return $query->getResult();
    }

    /**
     * @param User $user
     * @param $resourceType
     * @param $resourceId
     * @param $action
     * @param array $data
     * @param null $message
     * @return Log
     */
    private function log(User $user, $resourceType, $resourceId, $action, array $data, $message = null)
    {
        $log = new Log();
        $log->setUser($user);
        $log->setUserName($user->getName());
        $log->setResourceId($resourceId);
        $log->setResourceType($resourceType);
        $log->setAction($action);
        $log->setData(json_encode($data));
        $log->setMessage($message ? $message : '');
        $this->em()->persist($log);
        $this->em()->flush();
        return $log;
    }
}