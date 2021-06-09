<?php

namespace Rbncha\ExtendedCms\Controller\Adminhtml\Templates;

use \Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Framework\Data\Form\FormKey\Validator;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\View\Result\Page;
use \Magento\Backend\App\Action;
use \Magento\MediaStorage\Model\File\UploaderFactory;
use \Magento\Framework\Controller\ResultFactory;
use \Rbncha\ExtendedCms\Helper\Data as Rhelper;

class View extends Action
{
    protected $uploaderFactory;
    protected $filesystem;
    protected $mediaDirectory;
    protected $rHelper;
    protected $_resultPageFactory;
    protected $_storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Validator $formKeyValidator,
        PageFactory $resultPageFactory,
        UploaderFactory $uploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        Rhelper $rHelper   
    ) {
        $this->rHelper = $rHelper;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        
        parent::__construct(
            $context
        );
    }

    public function execute()
    {
	        $result = ['success' => false];
	    	$templateId = $this->getRequest()->getPost('templateid');
	    	$layout = $this->_view->getLayout();

    	try{
	    	
	    	$templates = [
	    		'template-1' => [
	    			'id' => 'template-1', 
	    			'thumbnail' => $this->rHelper->getMediaUrl('extendedcms/template-1.png'),
	    			'content' => $layout->createBlock('\Magento\Framework\View\Element\Template')->setTemplate('Rbncha_ExtendedCms::templates/template-1.phtml')->toHtml()
	    		],
	    		'template-2' => [
	    			'id' => 'template-2', 
	    			'thumbnail' => $this->rHelper->getMediaUrl('services/Captivate_Services_Globe.png'),
	    			'content' => $layout->createBlock('\Magento\Framework\View\Element\Template')->setTemplate('Rbncha_ExtendedCms::templates/template-2.phtml')->toHtml()
	    		]
	    	];
	    	
	    	if(!empty($templateId) && isset($templates[$templateId])){
	    		$result['success'] = true;
	    		$result['template'] = $templates[$templateId];
	    	}else{
	    		$result['success'] = false; 
	    		$result['error'] = 'Please reload the page. Something went wrong. I will try to fix it in next load.';
	    	}
	    	
	    }catch(\Exception $e){
	    	$result = ['success' => false, 'error' => $e->getMessage()];
	    }
	    	
		return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData(json_encode($result));
    }
}
