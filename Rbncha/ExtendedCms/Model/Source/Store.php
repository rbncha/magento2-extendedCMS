<?php

namespace Rbncha\ExtendedCms\Model\Source;

class Store extends \Magento\Eav\Model\Entity\Attribute\Source\Store
{

	/**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory, $storeCollectionFactory);
    }

	public function getAllOptions($withEmpty = true, $defaultValues = false)
	{
		$options = parent::getAllOptions($withEmpty, $defaultValues);
		array_unshift($options, ['value' => 0, 'label' => '---']);

		return $options;
	}
}