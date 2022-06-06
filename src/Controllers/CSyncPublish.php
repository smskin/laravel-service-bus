<?php

namespace SMSkin\ServiceBus\Controllers;

use SMSkin\ServiceBus\Enums\Models\HostsItem;
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

    protected ?string $requestClass = SyncPublishRequest::class;

    /**
     * @return $this
     * @throws ApiTokenNotDefined
     * @throws GuzzleException
     * @throws PackageConsumerNotExists
     */
    public function execute(): static
    {
        $response = $this->submitRequest();
        if ($response->getStatusCode() === 200) {
            $json = $response->getBody()->getContents();
            $package = (new PackageDecoder)->decode($json);
            $this->result = $package;
            return $this;
        }
        return $this;
    }

    /**
     * @return ?BasePackage
     */
    public function getResult(): ?BasePackage
    {
        return parent::getResult();
    }

    private function getHostEnum(): HostsItem|EnumItem
    {
        return self::getHostsEnum()::getById($this->request->host);
    }

    private function getApi()
    {
        return app(ApiClient::class);
    }

    /**
     * @return string
     * @throws ApiTokenNotDefined
     */
    private function getApiToken(): string
    {
        $apiToken = config('service-bus.connections.sync')[$this->request->host]['api_token'];
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
                'package' => $this->request->package->toArray()
            ],
            [
                'X-API-TOKEN' => $this->getApiToken()
            ]
        );
    }
}
