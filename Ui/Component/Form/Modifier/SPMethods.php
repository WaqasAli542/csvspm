<?php

namespace WMZ\CSVSPM\Ui\Component\Form\Modifier;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use WMZ\CSVSPM\Model\Source\PaymentMethod;
use WMZ\CSVSPM\Model\Source\ShippingMethod;
use WMZ\CSVSPM\Model\Source\StoreView;
use Magento\Store\Model\StoreManagerInterface;
use WMZ\CSVSPM\Model\CSVSPMConfigurationRepository;

class SPMethods implements ModifierInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var PaymentMethod
     */
    private $paymentMethod;

    /**
     * @var ShippingMethod
     */
    private $shippingMethod;

    /**
     * @var StoreView
     */
    private $storeView;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CSVSPMConfigurationRepository
     */
    private $repository;

    /**
     * SPMethods constructor.
     * @param RequestInterface $request
     * @param PaymentMethod $paymentMethod
     * @param ShippingMethod $shippingMethod
     * @param StoreView $storeView
     * @param StoreManagerInterface $storeManager
     * @param CSVSPMConfigurationRepository $repository
     */
    public function __construct(
        RequestInterface $request,
        PaymentMethod $paymentMethod,
        ShippingMethod $shippingMethod,
        StoreView $storeView,
        StoreManagerInterface $storeManager,
        CSVSPMConfigurationRepository $repository
    ) {
        $this->request = $request;
        $this->paymentMethod = $paymentMethod;
        $this->shippingMethod = $shippingMethod;
        $this->storeView = $storeView;
        $this->storeManager = $storeManager;
        $this->repository = $repository;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     * @throws NoSuchEntityException
     */
    public function modifyMeta(array $meta)
    {
        $csvspmId = $this->request->getParam('csvspm_id');
        if (isset($csvspmId) && $csvspmId) {
            $storeViewId =  $this->repository->getById($csvspmId)->getStoreView();
            $meta = array_replace_recursive(
                $meta,
                [
                    'csvspm_configuration' => [
                        'children' => [
                            'store_view' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'label' => __('Store View'),
                                            'formElement' => Field::NAME,
                                            'componentType' => Select::NAME,
                                            'dataScope' => CSVSPMConfigurationInterface::STORE_VIEW,
                                            'dataType' => Text::NAME,
                                            'options' => $this->storeView->getAllStoreView(),
                                            'disabled' => true,
                                            'sortOrder' => 30
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'csvspm_configuration' => [
                        'children' => [
                            'payment_methods' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'label' => __('Payment Method'),
                                            'formElement' => Field::NAME,
                                            'componentType' => MultiSelect::NAME,
                                            'dataScope' => CSVSPMConfigurationInterface::PAYMENT_METHODS,
                                            'dataType' => Text::NAME,
                                            'options' => $this->paymentMethod->getPaymentMethodList($storeViewId),
                                            'sortOrder' => 50
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'csvspm_configuration' => [
                        'children' => [
                            'shipping_methods' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'label' => __('Shipping Method'),
                                            'formElement' => Field::NAME,
                                            'componentType' => MultiSelect::NAME,
                                            'dataScope' => CSVSPMConfigurationInterface::SHIPPING_METHOD,
                                            'dataType' => Text::NAME,
                                            'options' => $this->shippingMethod->getShippingMethodList($storeViewId),
                                            'sortOrder' => 60
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        } else {
            $meta = array_replace_recursive(
                $meta,
                [
                    'csvspm_configuration' => [
                        'children' => [
                            'store_view' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'label' => __('Store View'),
                                            'formElement' => Field::NAME,
                                            'componentType' => Select::NAME,
                                            'dataScope' => CSVSPMConfigurationInterface::STORE_VIEW,
                                            'dataType' => Text::NAME,
                                            'options' => $this->storeView->getAllStoreView(),
                                            'disabled' => false,
                                            'sortOrder' => 30
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        }
        return $meta;
    }
}
