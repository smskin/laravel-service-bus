<?php

namespace SMSkin\ServiceBus\Controllers;

use Illuminate\Support\Facades\Log;
use SMSkin\LaravelSupport\BaseController;
use SMSkin\LaravelSupport\BaseRequest;
use SMSkin\ServiceBus\Enums\Models\PackageItem;
use SMSkin\ServiceBus\Exceptions\PackageConsumerNotExists;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Packages\IncomingPackage;
use SMSkin\ServiceBus\Packages\Messages\BaseMessage;
use SMSkin\ServiceBus\Packages\Processors\BaseProcessor;
use SMSkin\ServiceBus\Requests\ConsumeRequest;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Throwable;

class CConsume extends BaseController
{
    use ClassFromConfig;

    protected ConsumeRequest|BaseRequest|null $request;

    protected ?string $requestClass = ConsumeRequest::class;

    /**
     * @return $this
     * @throws PackageConsumerNotExists
     * @throws Throwable
     */
    public function execute(): static
    {
        $data = json_decode($this->request->json, true);
        $tempPackage = $this->parsePackage($data);
        $packageEnumItem = $this->getPackageEnumItemByMessageType($tempPackage->getPackage());
        if (!$packageEnumItem) {
            throw new PackageConsumerNotExists();
        }
        
        try {
            $package = $this->getPackageContext($packageEnumItem)->fromArray($data);
            $processor = $this->getProcessorContext($packageEnumItem, $package);
            $this->result = $processor->execute();
        } catch (Throwable $exception) {
            Log::error('Consume failed', [
                'json' => $this->request->json,
                'exception' => $exception
            ]);
            throw $exception;
        }
        return $this;
    }

    /**
     * @return BaseMessage|null
     */
    public function getResult(): ?BaseMessage
    {
        return parent::getResult();
    }

    private function parsePackage(array $data): IncomingPackage
    {
        return (new IncomingPackage)->fromArray($data);
    }

    private function getPackageEnumItemByMessageType(string $messageType): ?PackageItem
    {
        $packages = self::getPackagesEnum()::items();
        return $packages->filter(function (PackageItem $package) use ($messageType) {
            return $package->id === $messageType;
        })->first();
    }

    private function getPackageContext(PackageItem $package): BasePackage
    {
        return new $package->class();
    }

    private function getProcessorContext(PackageItem $packageEnumItem, BasePackage $package): BaseProcessor
    {
        $processor = $packageEnumItem->processor;
        return new $processor($package);
    }
}
