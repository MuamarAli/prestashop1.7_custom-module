<?php

/**
 * Automatically create table if not exist in the database.
 *
 * @author Ali, Muamar
 *
 * @return bool
 */
return (!Db::getInstance()->Execute(
    'INSERT INTO ' . _DB_PREFIX_ . 'hook (`name`, `title`, `description`) VALUES 
        ("displayBanner", "displayBanner", "Add banner field in the back office of category"),
        ("displayHover", "displayHover", "Add hover field in the back office of category")'
)) ? false : true;