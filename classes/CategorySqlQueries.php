<?php

/**
 * Class CategorySqlQueries
 *
 * @author Ali, Muamar
 */
class CategorySqlQueries
{
    /**
     * Return category from database.
     *
     * @param int $id | id of the current category.
     *
     * @throws PrestaShopDatabaseException
     * @throws Exception
     * @author Ali, Muamar
     *
     * @return array
     */
    public function getCategory(int $id)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'category_lang 
            WHERE `id_category` =' . pSQL($id)
        );
    }

    /**
     * Update category image.
     *
     * @param int $id | current id of the category
     * @param string|null $banner | category banner.
     * @param string|null $hover | category hover.
     *
     * @author Ali, Muamar
     *
     * @return bool
     */
    public function updateSql(int $id, string $banner = null, string $hover = null)
    {
        return Db::getInstance()->execute(
            'UPDATE ' ._DB_PREFIX_. 'category_lang 
                SET `banner` = "' . pSQL($banner) . '", 
                `hover` = "' . pSQL($hover) . '" 
                WHERE `id_category` =' . pSQL($id)
        );
    }
}