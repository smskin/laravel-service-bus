<?php

namespace SMSkin\ServiceBus\Support;

use SMSkin\ServiceBus\Enums\Models\PackageItem;
use SMSkin\ServiceBus\Exceptions\PackageConsumerNotExists;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Packages\IncomingPackage;
use SMSkin\ServiceBus\Traits\ClassFromConfig;

class PackageDecoder
{
    use ClassFromConfig;

    /**
     * @param string $json
     * @return BasePackage
     * @throws PackageConsumerNotExists
     */
    public function decode(string $json): BasePackage
    {
        $data = json_decode($json, true);
        $tempPackage = $this->parsePackage($data);
        $packageEnumItem = $this->getPackageEnumItemByMessageType($tempPackage->getPackage());
        if (!$packageEnumItem) {
            throw new PackageConsumerNotExists();
        }
        return $this->getPackageContext($packageEnumItem)->fromArray($data);
    }

    private function getPackageContext(PackageItem $package): BasePackage
    {
        return new $package->class();
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
}