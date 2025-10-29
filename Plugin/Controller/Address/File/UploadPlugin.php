<?php
namespace AquiveMedia\DisableCustomerFileUpload\Plugin\Controller\Address\File;

use Magento\Customer\Controller\Address\File\Upload;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;

/**
 * Plugin that disables the Upload controller by returning a 403 JSON response and not calling the original controller.
 */
class UploadPlugin
{
/** @var JsonFactory */
    private JsonFactory $resultJsonFactory;

/** @var LoggerInterface */
    private LoggerInterface $logger;

    public function __construct(
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
    }

/**
 * Around plugin for execute(). This will short-circuit the controller and return a 403 JSON.
 * @param Upload $subject
 * @param callable $proceed
 * @return Json
 */
    public function aroundExecute(Upload $subject, callable $proceed): Json
    {
        try {
            $this->logger->warning('Disabled endpoint');
        } catch (\Throwable $t) {
        }

        $result = $this->resultJsonFactory->create();
        $result->setHttpResponseCode(403);
        $result->setData([
            'error' => true,
            'message' => 'This endpoint has been disabled for security reasons.',
        ]);

        return $result;
    }
}
