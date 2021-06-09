<?php

namespace Rbncha\ExtendedCms\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{

	protected $_storeManager;
	protected $_objectManager;
	protected $_request;

	public function __construct(
		StoreManagerInterface $storeManager,
		ObjectManagerInterface $objectManager,
		\Magento\Framework\App\Request\Http $request
	){
		$this->_storeManager = $storeManager;
		$this->_objectManager = $objectManager;
		$this->_request = $request;

	}

	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Returns array of ids or collection
	 * 
	 * @param $period string month|year|day
	 * @return array
	 */
	public function getBestSellingProducts($period = 'month', $returnIds = false)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$productCollection = $objectManager->create('Magento\Reports\Model\ResourceModel\Report\Collection\Factory'); 
		$collection = $productCollection->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection'); 
		$collection = $objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Collection');
		//$collection->setPeriod($period);

		if($returnIds) return $collection->getAllIds();

		return $collection;
	}

	public function getNewProducts($dateStart = null, $dateEnd = null, $returnIds = true)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$collection = $objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Collection');

	}

	/**
	 * One of :
	 *		<div class="card__badge card__badge--award">Award winning</div>
			<div class="card__badge card__badge--new">New</div>
			<div class="card__badge card__badge--best">Best Seller</div>
	 */
	public function getProductLabel(\Magento\Catalog\Model\Product $product, $attributeCode)
	{
		$label = strtolower($product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product));

		switch($label){
			case 'new':
				return '<div class="card__badge card__badge--new">New</div>';
				break;
			case 'best seller':
				return '<div class="card__badge card__badge--best">Best Seller</div>';
				break;
			case 'award winning':
				return '<div class="card__badge card__badge--award">Award Winning</div>';
				break;
			default:
				return '<div></div>';
		}

		return null;
	}

	/**
	 * Returns only the website's base url without/with store code
	 * 
	 * @param $storeCode string|bool store code
	 * @return string
	 */
	public function getBaseUrl($storeCode = false)
	{
		$stores = $this->getStoreCodes();
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl();

		foreach($stores as $store){
			$code = $store->getCode();
			$baseUrl = rtrim(str_replace($code, '', $baseUrl), '/') . '/';
		}

		if($storeCode){
			$baseUrl .= $storeCode . '/';
		}

		return $baseUrl;
	}

	public function getBaseBaseUrl($type = null)
	{
		$stores = $this->getStoreCodes();
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl($type);

		foreach($stores as $store){
			$code = $store->getCode();
			$baseUrl = rtrim(str_replace($code, '', $baseUrl), '/') . '/';
		}

		return $baseUrl;
	}

	public function getStoreCodes($returnArray = false)
	{
		$stores = $this->_storeManager->getStores($withDefault = false);

		if($returnArray){
			$storesArray = [];
			foreach($stores as $store){
				$storesArray[$store->getCode()] = $store->toArray();
			}

			return $storesArray;
		}

		return $stores;
	}
	
	/**
	 * Returns only the website's base url without/with store code
	 * 
	 * @param $storeCode string|bool store code
	 * @return string
	 */
	public function getMediaUrl($file)
	{
		$stores = $this->getStoreCodes();
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

		foreach($stores as $store){
			$code = $store->getCode();
			$baseUrl = rtrim(str_replace($code, '', $baseUrl), '/') . '/';
		}
		
		$baseUrl .= $file;
		
		return $baseUrl;
	}

	public function getStoreCode()
	{
		return $this->_storeManager->getStore()->getCode();
	}

	public function getStore()
	{
		return $this->_storeManager->getStore();
	}

	public function getInstance($class)
	{
		return $this->_objectManager->get($class);
	}

	public function getCompareList()
	{
		$skus = explode(',', $this->getRequest()->getParam('pids'));
		$collection = $this->_objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\Collection');
	    $collection->addAttributeToSelect('*')
	    	->addFieldToFilter('sku', ['in' => $skus]);

		$items = [];

		foreach($collection as $p){
			$items[] = $p->getEntityId();
		}

		$list = $this->_objectManager->get('\Magento\Catalog\Model\Product\Compare\ListCompare');

        if (count($items)) {
            $list->addProducts($items);
        }

        return $list;
	}

	/**
	 * $obj = \Magento\Framework\App\ObjectManager::getInstance();
     * $helper = $obj->create('\Rbnha\ExtendedCms\Helper\Data');
     * $helper->debug(__FILE__, 'saveorder.log', 'error');
     */
	public function debug($message, $filename = 'rbncha.log', $type = 'error', $logDir = BP . '/var/log/')
	{
		$type = $type == 'error' ? 'err' : $type;
		$writer = new \Zend\Log\Writer\Stream($logDir . $filename);
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->$type($message);
	}
	
	
	
}