<?php

namespace SMSkin\ServiceBus\Controllers;

use Illuminate\Support\Facades\Log;
use SMSkin\LaravelSupport\BaseController;
use SMSkin\LaravelSupport\BaseRequest;
use SMSkin\ServiceBus\Events\EPackageConsumed;
use SMSkin\ServiceBus\Events\EPackageProcessed;
use SMSkin\ServiceBus\Exceptions\NotProcessablePackage;
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

    protected string|null $requestClass = ConsumeRequest::class;

    /**
     * @return $this
     * @throws PackageConsumerNotExists
     * @throws Throwable
     */
    public function execute(): static
    {
        $package = (new PackageDecoder)->decode(json_decode($this->request->getJson(), true));
        $this->registerConsumedEvent($package);
        try {
            $processor = $package->getProcessor();
            $this->result = $processor->execute();
            $this->registerProcessedEvent($package);
        } catch (NotProcessablePackage) {

        } catch (Throwable $exception) {
            Log::error('Consume failed', [
                'json' => $this->request->getJson(),
                'exception' => $exception
            ]);
            throw $exception;
        }
        return $this;
    }

    /**
     * @return BasePackage|null
     */
    public function getResult(): BasePackage|null
    {
        return parent::getResult();
    }

    private function registerConsumedEvent(BasePackage $package)
    {
        event(new EPackageConsumed($package));
    }

    private function registerProcessedEvent(BasePackage $package)
    {
        event(new EPackageProcessed($package));
    }
}
