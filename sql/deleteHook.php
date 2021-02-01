<?php

/**
 * Automatically create table if not exist in the database.
 *
 * @author Ali, Muamar
 *
 * @return bool
 */
return (!Db::getInstance()->Execute(
    'DELETE FROM ' . _DB_PREFIX_ . 'hook 
        WHERE `name` IN ("displayBanner", "displayHover")'
)) ? false : true;