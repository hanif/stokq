<?php

namespace Stokq\Validator;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Class RecordExists
 * @package Stokq\Validator
 */
class RecordExists extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_EXISTS = 'noRecordExists';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = [
        self::ERROR_NO_RECORD_EXISTS => "The given value is invalid.",
    ];

    protected $options = [
        'entity_manager' => null,
        'entity' => null,
        'field' => null,
        'excludes' => []
    ];

    /**
     * @param null $options
     * @throws \InvalidArgumentException
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        if (!$this->getOption('entity_manager') instanceof EntityManager) {
            throw new \InvalidArgumentException('`entity_manager` option must be instance of EntityManager.');
        }

        if (!$this->getOption('entity')) {
            throw new \InvalidArgumentException('`entity` option must be set.');
        }

        if (!$this->getOption('field')) {
            throw new \InvalidArgumentException('`field` option must be set.');
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        if (is_array($value)) {
            $result = true;
            foreach ($value as $elem) {
                if (!$this->_isValid($elem)) {
                    $result = false;
                    break;
                }
            }
            return $result;
        }
        return $this->_isValid($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function _isValid($value)
    {
        /** @var EntityManager $objectManager */
        $objectManager = $this->getOption('entity_manager');
        $qb = $objectManager->createQueryBuilder();
        $qb->select('a')
            ->from($this->getOption('entity'), 'a')
            ->where(sprintf('a.%s = :value', $this->getOption('field')))
            ->setParameter('value', $value);

        $excludes = $this->getOption('excludes');
        if (is_array($excludes) && !empty($excludes)) {
            foreach ($excludes as $field => $toExclude) {
                if (is_scalar($toExclude)) {
                    $qb->andWhere(sprintf('a.%s != :to_exclude', $field));
                    $qb->setParameter('to_exclude', $toExclude);
                } else if (is_array($toExclude) && !empty($toExclude)) {
                    $qb->andWhere(sprintf('a.%s NOT IN (:to_exclude)', $field));
                    $qb->setParameter('to_exclude', $toExclude, Connection::PARAM_STR_ARRAY);
                } else if (is_null($toExclude)) {
                    $qb->andWhere(sprintf('a.%s IS NOT NULL', $field));
                }
            }
        }

        try {
            $found = $qb->getQuery()->getResult();
            if (empty($found)) {
                $this->error(self::ERROR_NO_RECORD_EXISTS, $value);
                return false;
            } else {
                return true;
            }
        } catch (\Exception $e) {
            $this->error(self::ERROR_NO_RECORD_EXISTS, $value);
            return false;
        }
    }
}