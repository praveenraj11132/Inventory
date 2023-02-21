<?php
namespace Wheelpros\Inventory\Block;

use Magento\Framework\Registry;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Model\GetSourceCodesBySkusInterface;

class Inventory extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var SourceItemInterface
     */
    protected $sourceiteminterface;
    /**
     * @var SourceInterface
     */
    protected $sourceInterface;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    protected $sourceitembyskuinterface;
    /**
     * @var SourceRepositoryInterface
     */
    protected $sourcerepositoryinterface;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param ProductRepositoryInterface $productRepository
     * @param SourceItemInterface $sourceiteminterface
     * @param SourceInterface $sourceInterface
     * @param SourceRepositoryInterface $sourcerepositoryinterface
     * @param GetSourceItemsBySkuInterface $sourceitembyskuinterface
     * @param GetSourceCodesBySkusInterface $description
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        SourceItemInterface $sourceiteminterface,
        SourceInterface $sourceInterface,
        SourceRepositoryInterface $sourcerepositoryinterface,
        GetSourceItemsBySkuInterface $sourceitembyskuinterface,
        GetSourceCodesBySkusInterface $description,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->productRepository = $productRepository;
        $this->sourceiteminterface = $sourceiteminterface;
        $this->sourceInterface = $sourceInterface;
        $this->sourcerepositoryinterface = $sourcerepositoryinterface;
        $this->sourceitembyskuinterface = $sourceitembyskuinterface;
        $this->description = $description;
        parent::__construct($context, $data);
        $this->context = $context;
    }

    /**
     * Get the current product's inventory
     *
     * @return array
     */
    public function getCurrentProductInventory()
    {
        $product = $this->registry->registry('current_product');
        $productSku = $product->getSku();

            // Get the source items for the product SKU
        $sourceItems = $this->sourceitembyskuinterface->execute($productSku);

            // If there are no source items, return an empty array
        if (empty($sourceItems)) {
        return [];
        }

        // Initialize vkariables
        $inventory = [];

        // Iterate over the source items and calculate the total quantity
        foreach ($sourceItems as $sourceItem) {
            $quantity = $sourceItem->getQuantity();
            $sourceCode = $sourceItem->getSourceCode();
            $source = $this->sourcerepositoryinterface->get($sourceCode);
            $description = $source->getDescription();

            $inventory[] = [
                'quantity' => $quantity,
                'description' => $description,
            ];
        };

        // Return the inventory array
        return $inventory;
    }
}
