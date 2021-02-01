<?php

include ('categorySql.php');

/**
 * Class CategoryManager
 *
 * @author Ali, Muamar
 */
class CategoryManager
{
    /**
     * Extension for the image file.
     */
    const JPEG_EXTENSION = '.jpg';

    /**
     * @var $categorySql
     */
    private $categorySql;

    /**
     * CategoryManager constructor.
     *
     * @param CategorySqlQueries $categorySql
     *
     * @author Ali, Muamar
     */
    public function __construct(CategorySqlQueries $categorySql)
    {
        $this->categorySql = $categorySql;
    }

    /**
     * Updating of images.
     *
     * @param array $params | form data.
     * @param int $id | id of the current category.
     *
     * @throws PrestaShopDatabaseException
     * @author Ali, Muamar
     *
     * @return $this
     */
    public function updateImages(array $params, int $id)
    {
        $this->categorySql->updateSql(
            $id,
            $this->uploadBannerImage($params, $id),
            $this->uploadHoverImage($params, $id)
        );

        return $this;
    }

    /**
     * Uploading of banner image.
     *
     * @param array $params
     * @param int $id
     *
     * @throws PrestaShopDatabaseException
     * @author Ali, Muamar
     *
     * @return string
     */
    public function uploadBannerImage(array $params, int $id)
    {
        if (!empty($_FILES['category']['tmp_name']['banner'])) {
            $banner = $params['form_data']['name'][1] . '-banner' . self::JPEG_EXTENSION;
            $this->isImageUploaded($this->getImageValues('banner'), $banner);
        } else {
            $banner = $this->checkImage($this->categorySql->getCategory($id)[0]['banner']);
        }

        return $banner;
    }

    /**
     * Uploading of hover image.
     *
     * @param array $params
     * @param int $id
     *
     * @throws PrestaShopDatabaseException
     * @author Ali, Muamar
     *
     * @return string
     */
    public function uploadHoverImage(array $params, int $id)
    {
        if (!empty($_FILES['category']['tmp_name']['hover'])) {
            $hover = $params['form_data']['name'][1] . '-hover' . self::JPEG_EXTENSION;
            $this->isImageUploaded($this->getImageValues('hover'), $hover);
        } else {
            $hover = $this->checkImage($this->categorySql->getCategory($id)[0]['hover']);
        }

        return $hover;
    }

    /**
     * Check if there's an existing image for updating image.
     *
     * @param string $image | image name.
     *
     * @author Ali, Muamar
     *
     * @return string
     */
    public function checkImage(string $image)
    {
        $output = (empty($old = $image)) ? null : $old;

        return $output;
    }

    /**
     * Check if the image is uploaded in the image path.
     *
     * @param array $image | image details.
     * @param string $name | the name of the image.
     *
     * @author Ali, Muamar
     *
     * @return $this
     */
    public function isImageUploaded(array $image, string $name)
    {
        if (
            !move_uploaded_file($image['tmp_name'], _PS_CAT_IMG_DIR_ . $name)
        ) {
            $this->displayError($this->l('An error occurred while attempting to upload the file.'));
        }

        return $this;
    }

    /**
     * Get the values inside images.
     *
     * @param string $image | image field name.
     *
     * @author Ali, Muamar
     *
     * @return array
     */
    public function getImageValues(string $image)
    {
        $fileObject = array();

        $fileObject['name'] = $_FILES['category']['name'][$image];
        $fileObject['type'] = $_FILES['category']['type'][$image];
        $fileObject['tmp_name'] = $_FILES['category']['tmp_name'][$image];
        $fileObject['error'] = $_FILES['category']['error'][$image];
        $fileObject['size'] = $_FILES['category']['size'][$image];

        return $fileObject;
    }

    /**
     * Delete file in the image directory.
     *
     * @param string $image | path and filename of the file.
     *
     * @author Ali, Muamar
     *
     * @return bool
     */
    public function unlinkImage(string $image)
    {
        return unlink($image);
    }

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
        return $this->categorySql->getCategory($id);
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
    public function updateSql(
        int $id,
        string $banner = null,
        string $hover = null
    )
    {
        return $this->categorySql->updateSql($id, $banner, $hover);
    }
}