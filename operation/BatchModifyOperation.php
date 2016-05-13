<?php
/**
 * @package   yii2-ldap
 * @author    @author Christopher Mota <chrmorandi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace chrmorandi\ldap\operation;

use chrmorandi\ldap\BatchModify\BatchCollection;
use chrmorandi\ldap\Utilities\LdapUtilities;

/**
 * Represents an operation to batch modify attribute values on an existing LDAP object .
 *
 * @author Christopher Mota <chrmorandi@gmail.com>
 * @since 1.0
 */
class BatchModifyOperation extends \yii\base\Object implements OperationInterface
{
    use OperationTrait;
    use ModOperationTrait;

    /**
     * @var array
     */
    protected $properties = [
        'dn' => null,
        'batch' => null,
    ];

    /**
     * @param string $dn The DN of the LDAP object to be modified.
     * @param BatchCollection|null $batch A BatchCollection object.
     */
    public function __construct($dn, BatchCollection $batch = null)
    {
        $this->properties['dn'] = $dn;
        $this->properties['batch'] = $batch;
    }

    /**
     * The distinguished name for an add, delete, or move operation.
     *
     * @return null|string
     */
    public function getDn()
    {
        return $this->properties['dn'];
    }

    /**
     * Set the distinguished name that the operation is working on.
     *
     * @param string $dn
     * @return $this
     */
    public function setDn($dn)
    {
        $this->properties['dn'] = $dn;

        return $this;
    }

    /**
     * The batch modifications array for a modify operation.
     *
     * @return BatchCollection|null
     */
    public function getBatchCollection()
    {
        return $this->properties['batch'];
    }

    /**
     * Set the batch modifications array for the operation.
     *
     * @param BatchCollection $batch
     * @return $this
     */
    public function setBatchCollection(BatchCollection $batch)
    {
        $this->properties['batch'] = $batch;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLdapFunction()
    {
        return 'ldap_modify_batch';
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return [
            $this->properties['dn'],
            $this->getBatchArray(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Batch Modify';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogArray()
    {
        $batch = LdapUtilities::maskBatchArray($this->getBatchArray());

        return $this->mergeLogDefaults([
            'DN' => $this->properties['dn'],
            'Batch' => print_r($batch, true),
        ]);
    }

    /**
     * Make sure to clone a BatchCollection instance.
     */
    public function __clone()
    {
        if ($this->properties['batch']) {
            $this->properties['batch'] = clone $this->properties['batch'];
        }
    }

    /**
     * @return array
     */
    protected function getBatchArray()
    {
        $batch = [];

        if (!is_null($this->properties['batch'])) {
            $batch = $this->properties['batch']->getBatchArray();
        }

        return $batch;
    }
}
