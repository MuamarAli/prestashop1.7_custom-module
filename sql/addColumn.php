<?php

/**
 * Automatically create table if not exist in the database.
 *
 * @author Ali, Muamar
 *
 * @return bool
 */
$sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'category_lang
        ADD COLUMN `banner` VARCHAR(255) NOT NULL AFTER `description`,
        ADD COLUMN `hover` VARCHAR(255) NOT NULL AFTER `banner`';

return (!Db::getInstance()->Execute($sql)) ? false : true;