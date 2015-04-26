<?php

namespace Stokq\Stdlib\Hydrator;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineObjectHydrator;
use Zend\Filter\FilterInterface;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Class DoctrineObject
 * @package Stokq\Stdlib\Hydrator
 */
class DoctrineObject extends DoctrineObjectHydrator
{
    /**
     * @var FilterInterface
     */
    protected $methodNameFilter;

    /**
     * @param  array  $data
     * @param  object $object
     * @throws \RuntimeException
     * @return object
     */
    protected function hydrateByValue(array $data, $object)
    {
        $tryObject = $this->tryConvertArrayToObject($data, $object);
        $metadata  = $this->metadata;

        if (is_object($tryObject)) {
            $object = $tryObject;
        }

        foreach ($data as $field => $value) {
            $value  = $this->handleTypeConversions($value, $metadata->getTypeOfField($field));
            $setter = 'set' . ucfirst($field);

            if (!method_exists($object, $setter)) {
                $setter = $this->getMethodNameFilter()->filter($setter);
            }

            if ($metadata->hasAssociation($field)) {
                $target = $metadata->getAssociationTargetClass($field);

                if ($metadata->isSingleValuedAssociation($field)) {
                    if (!method_exists($object, $setter)) {
                        continue;
                    }

                    $value = $this->toOne($target, $this->hydrateValue($field, $value, $data));

                    if (null === $value && !current($metadata->getReflectionClass()->getMethod($setter)->getParameters())->allowsNull()) {
                        continue;
                    }

                    $object->$setter($value);
                } elseif ($metadata->isCollectionValuedAssociation($field)) {
                    $this->toMany($object, $field, $target, $value);
                }
            } else {
                if (!method_exists($object, $setter)) {
                    continue;
                }

                $object->$setter($this->hydrateValue($field, $value, $data));
            }
        }

        return $object;
    }

    /**
     * @return FilterInterface|UnderscoreToCamelCase
     */
    public function getMethodNameFilter()
    {
        if (!$this->methodNameFilter instanceof FilterInterface) {
            $this->methodNameFilter = new UnderscoreToCamelCase();
        }
        return $this->methodNameFilter;
    }
}