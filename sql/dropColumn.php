<?php

/**
 * Drop the table created if the module is uninstalled.
 *
 * @return bool
 *
 * @author Ali, Muamar
 */
$sql =  'ALTER TABLE ' . _DB_PREFIX_ . 'category_lang
        DROP `banner`,
        DROP `hover`';

return (
    !Db::getInstance()->Execute($sql)
) ? false : true;