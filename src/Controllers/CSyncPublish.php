<?php

namespace SMSkin\ServiceBus\Controllers;

use SMSkin\ServiceBus\Enums\Models\HostsItem;
use SMSkin\ServiceBus\Events\EPackageSubmitted;
use SMSkin\ServiceBus\Exceptions\ApiTokenNotDefined;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Exceptions\PackageConsumerNotExists;
use SMSkin\ServiceBus\Requests\SyncPublishRequest;
use SMSkin\ServiceBus\Support\ApiClient;
use SMSkin\ServiceBus\Support\PackageDecoder;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use SMSkin\LaravelSupport\BaseController;
use SMSkin\LaravelSupport\BaseRequest;
use SMSkin\LaravelSupport\Models\EnumItem;

class CSyncPublish extends BaseController
{
    use ClassFromConfig;

    protected SyncPublishRequest|BaseRequest|null $request;

    protected string|null $requestClass = SyncPublishRequest::class;

    /**
     * @return $this
     * @throws ApiTokenNotDefined
     * @throws GuzzleException
     * @throws PackageConsumerNotExists
     */
    public function execute(): static
    {
        $response = $this->submitRequest();
        $this->registerSubmittedEvent();
        if ($response->getStatusCode() === 200) {
            $json = json_decode($response->getBody()->getContents(), true);
            $package = (new PackageDecoder)->decode($json['package']);
            $this->result = $package;
        }

        return $this;
    }

    /**
     * @return ?BasePackage
     */
    public function getResult(): BasePackage|null
    {
        return parent::getResult();
    }

    private function getHostEnum(): HostsItem|EnumItem
    {
        return self::getHostsEnum()::getById($this->request->getHost());
    }

    private function getApi(): ApiClient
    {
        return app(ApiClient::class);
    }

    /**
     * @return string
     * @throws ApiTokenNotDefined
     */
    private function getApiToken(): string
    {
        $apiToken = config('service-bus.connections.sync')[$this->request->getHost()]['api_token'];
        if (!$apiToken) {
            throw new ApiTokenNotDefined();
        }
        return $apiToken;
    }

    /**
     * @return ResponseInterface
     * @throws GuzzleException
     * @throws ApiTokenNotDefined
     */
    private function submitRequest(): ResponseInterface
    {
        return $this->getApi()->post(
            $this->getHostEnum()->host,
            [
                'package' => $this->request->getPackage()->toArray()
            ],
            [
                'X-API-TOKEN' => $this->getApiToken()
            ]
        );
    }

    private function registerSubmittedEvent()
    {
        event(new EPackageSubmitted(
            $this->request->getPackage(),
            null,
            $this->request->getHost(),
        ));
    }
}
