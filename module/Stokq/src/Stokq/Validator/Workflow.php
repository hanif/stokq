<?php

namespace Stokq\Validator;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Class Workflow
 * @package Stokq\Validator
 */
class Workflow extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_WORKFLOW_NOT_ALLOWED = 'workflowNotAllowed';

    /**
     * Error constants
     */
    const ERROR_INVALID_WORKFLOW = 'invalidWorkflow';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = [
        self::ERROR_WORKFLOW_NOT_ALLOWED => "Workflow not allowed",
        self::ERROR_INVALID_WORKFLOW => "Invalid workflow.",
    ];

    /**
     * @param null $options
     * @throws \InvalidArgumentException
     */
    public function __construct($options = null)
    {
        parent::__construct($options + ['from' => null, 'allow_current' => true]);

        if (!$this->getOption('workflow')) {
            throw new \InvalidArgumentException('`workflow` option must be set.');
        }

        if (!$this->getOption('allow_jumps')) {
            throw new \InvalidArgumentException('`allow_jumps` option must be set.');
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        if (is_null($value) || $value === false) {
            $this->error(self::ERROR_INVALID_WORKFLOW);
            return false;
        }

        if (!in_array($value, $this->getOption('workflow'))) {
            $this->error(self::ERROR_INVALID_WORKFLOW);
            return false;
        }

        $fromIndex = array_search($this->getOption('from'), $this->getOption('workflow'));
        $index = array_search($value, $this->getOption('workflow'));

        if ($this->getOption('allow_current') && ($fromIndex === $index)) {
            return true;
        }

        if (!$this->getOption('from')) {
            if ($this->getOption('allow_jumps')) {
                return true;
            } else {
                if ((int)$index === 0) {
                    return true;
                } else {
                    $this->error(self::ERROR_INVALID_WORKFLOW);
                    return false;
                }
            }
        }

        if (($fromIndex < $index) && ($this->getOption('allow_jumps'))) {
            return true;
        } else {
            if ($index - $fromIndex === 1) {
                return true;
            }

            $this->error(self::ERROR_WORKFLOW_NOT_ALLOWED);
            return false;
        }
    }
}