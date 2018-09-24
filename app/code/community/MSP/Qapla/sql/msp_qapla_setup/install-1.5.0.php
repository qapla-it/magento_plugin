<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$tableName = $installer->getTable('msp_qapla_queue');

$table = $connection->newTable($tableName);

$table->addColumn(
    'msp_qapla_queue_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ],
    'Id'
)->addColumn(
    'entity_type',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    null,
    [
        'nullable' => false
    ],
    'Entity type'
)->addColumn(
    'entity_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    [
        'nullable' => false,
        'unsigned' => true
    ],
    'referenced entity id'
)->addColumn(
    'created_at',
    Varien_Db_Ddl_Table::TYPE_DATETIME,
    [
        'nullable' => false,
    ],
    'queue item creation date'
)->addColumn(
    'processed_at',
    Varien_Db_Ddl_Table::TYPE_DATETIME,
    [
        'nullable' => true,
    ],
    'processing date'
)->addColumn(
    'status',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    [
        'nullable' => false,
    ],
    'status'
)->addColumn(
    'message',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    [
        'nullable' => true,
    ],
    'error message'
);

$connection->createTable($table);

$installer->endSetup();