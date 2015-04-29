<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Supplier;
use Stokq\InputFilter\SupplierInput;

/**
 * Class SupplierController
 * @package Stokq\Controller\Web
 */
class SupplierController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('purchase');

        $keyword        = $this->getRequestQuery('q');
        $minPurchase    = $this->getRequestQuery('min_purchase');
        $maxPurchase    = $this->getRequestQuery('max_purchase');
        $minTransaction = $this->getRequestQuery('min_transaction');
        $maxTransaction = $this->getRequestQuery('max_transaction');
        $cbFilter       = $this->filterList($keyword, $minPurchase, $maxPurchase, $minTransaction, $maxTransaction);
        $query          = $this->getPurchaseService()->getSupplierWithPurchaseQuery($cbFilter);
        $page           = $this->getRequestQuery('page', 1);
        $pageSize       = $this->getRequestQuery('pageSize', 20);
        $pages          = $this->mapper(Supplier::class)->paginate($query, $page, $pageSize);
        $user           = $this->user();
        $account        = $this->account();

        return compact('pages', 'page', 'user', 'account') +
        [
            'keyword'        => $keyword,
            'minPurchase'    => $minPurchase,
            'maxPurchase'    => $maxPurchase,
            'minTransaction' => $minTransaction,
            'maxTransaction' => $maxTransaction,
        ];
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     * @throws \Doctrine\DBAL\DBALException
     */
    public function autocompleteAction()
    {
        $this->ensure('get');
        $stmt = $this->db()->prepare("select" . " s.name from suppliers s where s.name like :name");
        $stmt->bindValue('name', sprintf('%%%s%%', $this->getRequestQuery('q')));
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->jsonOk($results);
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');

        $input = $this->autoFilledInputFilter(SupplierInput::class);

        if ($input->isValid()) {
            $mapper = $this->mapper(Supplier::class);
            $mapper->insert($input->getValues());
            return $this->jsonOk([], 201);
        }

        return $this->jsonError($input->getMessages(), 400);
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function editAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('purchase');

        try {
            $supplier = $this->mapper(Supplier::class)->one($this->getRequestQuery('id'));
            return compact('supplier');
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function updateAction()
    {
        $this->ensure('post');

        try {
            $supplier = $this->mapper(Supplier::class)->one($this->getRequestQuery('id'));
            $input = $this->autoFilledInputFilter(SupplierInput::class, $supplier);
            if ($input->isValid()) {
                $this->mapper(Supplier::class)->getHydrator()->hydrate($input->getValues(), $supplier);
                $this->persist($supplier)->commit();
                return $this->jsonOk([], 200);
            }
            return $this->jsonError($input->getMessages(), 400);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function deleteAction()
    {
        $this->ensure('delete', 'post');
        $this->mapper(Supplier::class)->delete($this->getRequestQuery('id'));
        return $this->ok(204);
    }

    /**
     * @param $keyword
     * @param $minPurchase
     * @param $maxPurchase
     * @param $minTransaction
     * @param $maxTransaction
     * @return callable
     */
    private function filterList($keyword, $minPurchase, $maxPurchase, $minTransaction, $maxTransaction)
    {
        return function(QueryBuilder $builder) use($keyword, $minPurchase, $maxPurchase, $minTransaction, $maxTransaction) {
            if (trim($keyword)) {
                $builder->andWhere('(
                    s.name like :keyword or s.website LIKE :keyword or
                    s.address LIKE :keyword or s.note LIKE :keyword or
                    s.email LIKE :keyword
                )');
                $builder->setParameter('keyword', '%' . $keyword . '%');
            }

            if (is_numeric($minPurchase)) {
                $builder->andHaving('COUNT(DISTINCT p.id) >= :min_purchase');
                $builder->setParameter('min_purchase', $minPurchase);
            }

            if (is_numeric($maxPurchase)) {
                $builder->andHaving('COUNT(DISTINCT p.id) <= :max_purchase');
                $builder->setParameter('max_purchase', $maxPurchase);
            }

            if (is_numeric($minTransaction)) {
                $builder->andHaving('SUM(i.total) >= :min_transaction');
                $builder->setParameter('min_transaction', $minTransaction);
            }

            if (is_numeric($maxTransaction)) {
                $builder->andHaving('SUM(i.total) <= :max_transaction');
                $builder->setParameter('max_transaction', $maxTransaction);
            }

            $builder->orderBy('s.name', 'asc');
        };
    }

}