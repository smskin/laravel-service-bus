<?php

namespace SMSkin\ServiceBus\Controllers;

use SMSkin\ServiceBus\Enums\Models\HostsItem;
use SMSkin\ServiceBus\Exceptions\ApiTokenNotDefined;
use SMSkin\ServiceBus\ServiceBus;
use SMSkin\ServiceBus\Exceptions\PackageConsumerNotExists;
use SMSkin\ServiceBus\Requests\ConsumeRequest;
use SMSkin\ServiceBus\Requests\SyncPublishRequest;
use SMSkin\ServiceBus\Support\ApiClient;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function execute(): static
    {
        $response = $this->submitRequest();
        if ($response->getStatusCode() === 200) {
            $json = $response->getBody()->getContents();
            $data = json_decode($json, true);
            $this->processResponse(json_encode($data['package']));
            return $this;
        }
        return $this;
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
        $apiToken = config('smskin.service-bus.connections.sync')[$this->request->host]['api_token'];
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

    /**
     * @param string $body
     * @throws ApiTokenNotDefined
     * @throws GuzzleException
     * @throws PackageConsumerNotExists
     * @throws ValidationException
     */
    private function processResponse(string $body): void
    {
        $result = app(ServiceBus::class)->consume(
            (new ConsumeRequest)->setJson($body)
        );
        if (is_null($result)) {
            return;
        }

        (new self(
            (new SyncPublishRequest)
                ->setPackage($result)
                ->setHost($this->request->host)
        ))->execute();
    }
}
