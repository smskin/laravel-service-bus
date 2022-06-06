<?php

namespace SMSkin\ServiceBus\Controllers;

use Illuminate\Support\Facades\Log;
use SMSkin\LaravelSupport\BaseController;
use SMSkin\LaravelSupport\BaseRequest;
use SMSkin\ServiceBus\Exceptions\PackageConsumerNotExists;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Requests\ConsumeRequest;
use SMSkin\ServiceBus\Support\PackageDecoder;
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
        $package = (new PackageDecoder)->decode($this->request->json);
        
        try {
            $processor = $package->getProcessor();
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
     * @return BasePackage|null
     */
    public function getResult(): ?BasePackage
    {
        return parent::getResult();
    }
}
