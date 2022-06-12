<?php

namespace WMZ\CSVSPM\Block\Adminhtml\Configuration\Edit;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use WMZ\CSVSPM\Model\CSVSPMConfiguration;
use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * GenericButton constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $context->getRequest();
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        $params = $this->request->getParams();
        if (isset($params[CSVSPMConfiguration::CSVSPM_ID])) {
            return $this->request->getParam(CSVSPMConfiguration::CSVSPM_ID);
        }
        return null;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
