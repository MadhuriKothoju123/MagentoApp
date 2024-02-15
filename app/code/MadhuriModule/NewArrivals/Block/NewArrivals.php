<?php

namespace MadhuriModule\NewArrivals\Block;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Helper\Image as ImageHelper;


class NewArrivals extends Template
{
    protected $productRepository;
    protected $productCollectionFactory;
    protected $timezone;
    protected $imageHelper;
    public function __construct(
        Template\Context $context,
        ProductRepositoryInterface $productRepository,
        ProductCollectionFactory $productCollectionFactory,
        ImageHelper $imageHelper,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->timezone = $timezone;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    public function getNewArrivals()
    {
        $newArrivals = [];
        $twoDaysAgo = $this->timezone->date(strtotime('-7 days'))->format('Y-m-d H:i:s'); 

        $productCollection = $this->productCollectionFactory->create();
        
        $productCollection->addAttributeToSelect('*');
        $productCollection->addAttributeToFilter(
            [
                ['attribute' => 'created_at', 'gteq' => $twoDaysAgo],
                ['attribute' => 'updated_at', 'gteq' => $twoDaysAgo],
            ]
        );

        foreach ($productCollection as $product) {
            $newArrivals[] = $product;
        }
        return $newArrivals;
    }

    public function getProductImages($productId)
    {
        try {
            $product = $this->productRepository->getById($productId);
            
            // Retrieve product images
            $images = $this->getImagesFromProduct($product);

            return $images;
        } catch (\Exception $e) {
            // Handle exception if product is not found
            return [];
        }
    }

      private function getImagesFromProduct(ProductInterface $product)
    {
        $images = [];

        // Get base image
        $baseImage = $product->getMediaGalleryEntries()[0]->getFile();
        $images['base_image'] = $baseImage;

        // Get additional images
        $additionalImages = [];
        foreach ($product->getMediaGalleryEntries() as $mediaGalleryEntry) {
            if ($mediaGalleryEntry->getFile() !== $baseImage) {
                $additionalImages[] = $mediaGalleryEntry->getFile();
            }
        }
        $images['additional_images'] = $additionalImages;

        return $images;
    }


    public function getRuntimeProductImageUrl($product, $imageType = 'image')
    {
        try {
        
            $imageUrl =$this->imageHelper->init($product, $imageType)->setImageFile($product->getImage())->getUrl();
            return $imageUrl;
        } catch (\Exception $e) {
            return null;
        }
    }

}