<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

include ('classes/CategoryManager.php');

/**
 * Class displaycustomfield
 *
 * @author Ali, Muamar
 */
class displaycustomfield extends Module
{
    /**
     * Error message in image.
     */
    const IMAGE_VALIDATION_MESSAGE = 'Image format not recognized, allowed formats are: .gif, .jpg, .png';

    /**
     * Extension for the image file.
     */
    const JPEG_EXTENSION = '.jpg';

    /**
     * @var CategoryManager
     */
    private $categoryManager;

    /**
     * displaycustomfield constructor.
     *
     * @param CategoryManager $categoryManager
     *
     * @author Ali, Muamar
     */
	public function __construct(CategoryManager $categoryManager)
	{
		$this->name = 'displaycustomfield';
		$this->tab = 'others';
		$this->version = '1.0';
		$this->author = 'Muamar Ali';
		$this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7.6', 'max' => _PS_VERSION_);
		$this->bootstrap = true;

	 	parent::__construct();

		$this->displayName = $this->l('Add: Category Banner and Hover');
		$this->description = $this->l(
        'This module allow to display additional field for the uploading of 
                banner and hover in the back office of category page.'
        );

		$this->categoryManager = $categoryManager;
	}

    /**
     * Allow module to be install.
     *
     * @author Ali, Muamar
     *
     * @return bool
     */
	public function install()
	{
        $addColumn = include_once ($this->getLocalPath().'sql/addColumn.php');
        $insertHook = include_once ($this->getLocalPath().'sql/insertHook.php');

		if (
		    !parent::install() OR
			!$addColumn OR
			!$insertHook OR
			!$this->registerHook('actionCategoryFormBuilderModifier') OR
            !$this->registerHook('actionAfterCreateCategoryFormHandler') OR
            !$this->registerHook('actionAfterUpdateCategoryFormHandler') OR
            !$this->registerHook('actionCategoryDelete') OR
            !$this->registerHook('displayBanner') OR
            !$this->registerHook('displayHover')
        )
			return false;
		return true;
	}

    /**
     * Uninstall the module installed.
     *
     * @author Ali, Muamar
     *
     * @return bool
     */
	public function uninstall()
	{
        $dropColumn = include_once ($this->getLocalPath().'sql/dropColumn.php');
        $deleteHook = include_once ($this->getLocalPath().'sql/deleteHook.php');

        return (!parent::uninstall() OR !$dropColumn OR !$deleteHook) ? false : true;
	}

    /**
     * Allows to add new field using symfony form.
     *
     * @param array $params
     *
     * @author Ali, Muamar
     */
    public function hookActionCategoryFormBuilderModifier(array $params)
    {
        $formBuilder = $params['form_builder'];

        $formBuilder
            ->add('banner',
                FileType::class, [
                    'required' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/jpg'
                            ],
                            'mimeTypesMessage' => self::IMAGE_VALIDATION_MESSAGE
                        ])
                    ]
                ])
            ->add('hover',
                FileType::class, [
                    'required' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/jpg'
                            ],
                            'mimeTypesMessage' => self::IMAGE_VALIDATION_MESSAGE
                        ])
                    ]
                ]);

        $formBuilder->setData($params['data']);
    }

    /**
     * Allow to handle the creation of category.
     *
     * @param array $params | form data.
     *
     * @throws PrestaShopDatabaseException
     * @author Ali, Muamar
     */
    public function hookActionAfterCreateCategoryFormHandler(array $params)
    {
        if (
            is_uploaded_file($_FILES['category']['tmp_name']['banner']) ||
            is_uploaded_file($_FILES['category']['tmp_name']['hover'])
        ) {
            $this->categoryManager->updateImages($params, $params['id']);
        }
    }

    /**
     * Allow to handle the edition of category.
     *
     * @param array $params | form data.
     *
     * @throws PrestaShopDatabaseException
     * @author Ali, Muamar
     */
    public function hookActionAfterUpdateCategoryFormHandler(array $params)
    {
        if (
            is_uploaded_file($_FILES['category']['tmp_name']['banner']) ||
            is_uploaded_file($_FILES['category']['tmp_name']['hover'])
        ) {
            $this->categoryManager->updateImages($params, $params['id']);
        }
    }

    /**
     * Deletion of category with banner and hover image.
     *
     * @param array $params | form data.
     *
     * @author Ali, Muamar
     *
     * @return bool
     */
    public function hookActionCategoryDelete(array $params)
    {
        $path = _PS_CAT_IMG_DIR_ . $params['category']->getName();

        $images = [
            $path . '-banner' . self::JPEG_EXTENSION,
            $path . '-hover' . self::JPEG_EXTENSION
        ];

        foreach ($images as $image) {
            if (file_exists($image)) {
                $result = $this->unlinkImage($image);
            } else {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Allow to display banner field in the back office of the category.
     *
     * @param array $params
     *
     * @throws PrestaShopDatabaseException
     * @author Ali, Muamar
     *
     * @return mixed
     */
    public function hookDisplayBanner(array $params)
    {
        if ($this->isSymfonyContext() && $params['route'] === 'admin_categories_edit') {
            if (empty($params['request']->get('categoryId'))) {
                $result = false;
            } else {
                $result = $this->get('twig')->render('@PrestaShop/templates/hook/custombanner.html.twig', [
                    'banner' => $this->getCategory($params['request']->get('categoryId'))[0]['banner'],
                    'imgPath' => _PS_BASE_URL_ . DIRECTORY_SEPARATOR . 'img' .
                        DIRECTORY_SEPARATOR . 'c' . DIRECTORY_SEPARATOR
                ]);
            }

            return $result;
        }
    }

    /**
     * Allow to display hover field in the back office of the category.
     *
     * @param array $params
     *
     * @throws PrestaShopDatabaseException
     * @author Ali, Muamar
     *
     * @return mixed
     */
    public function hookDisplayHover(array $params)
    {
        if ($this->isSymfonyContext() && $params['route'] === 'admin_categories_edit') {
            if (empty($params['request']->get('categoryId'))) {
                $result = false;
            } else {
                $result = $this->get('twig')->render('@PrestaShop/templates/hook/customhover.html.twig', [
                    'hover' => $this->getCategory($params['request']->get('categoryId'))[0]['hover'],
                    'imgPath' => _PS_BASE_URL_ . DIRECTORY_SEPARATOR . 'img' .
                        DIRECTORY_SEPARATOR . 'c' . DIRECTORY_SEPARATOR
                ]);
            }

            return $result;
        }
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
     * @return CategoryManager
     */
    public function updateImages(array $params, int $id)
    {
        return $this->categoryManager->updateImages(
            $params,
            $id
        );
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
        return $this->categoryManager->unlinkImage($image);
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
        return $this->categoryManager->getCategory($id);
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
        return $this->categoryManager->updateSql($id, $banner, $hover);
    }
}