<?php

namespace SMSkin\ServiceBus\Http\Controllers;

use SMSkin\ServiceBus\ServiceBus;
use SMSkin\ServiceBus\Exceptions\PackageConsumerNotExists;
use SMSkin\ServiceBus\Http\Requests\ConsumeRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ConsumerController extends Controller
{
    /**
     * @throws PackageConsumerNotExists
     * @throws Throwable
     */
    public function __invoke(ConsumeRequest $request): Response
    {
        $response = (new ServiceBus())->consume(
            (new \SMSkin\ServiceBus\Requests\ConsumeRequest)->setJson(
                json_encode($request->input('package'), JSON_PRETTY_PRINT)
            )
        );

        if ($response === null) {
            return response()->noContent();
        }
        return response()->json([
            'package' => $response->toArray()
        ]);
    }
}
