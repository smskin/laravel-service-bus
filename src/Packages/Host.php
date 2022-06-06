<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Contracts\Arrayable;

class Host implements Arrayable
{
    public ?string $machineName = null;
    public ?string $processName = null;
    public ?int $processId = null;
    public ?string $assembly = null;
    public ?string $assemblyVersion = null;
    public ?string $frameworkVersion = null;
    public ?string $massTransitVersion = null;
    public ?string $operatingSystemVersion = null;

    public function toArray(): array
    {
        return [
            'machineName' => $this->machineName,
            'processName' => $this->processName,
            'processId' => $this->processId,
            'assembly' => $this->assembly,
            'assemblyVersion' => $this->assemblyVersion,
            'frameworkVersion' => $this->frameworkVersion,
            'massTransitVersion' => $this->massTransitVersion,
            'operatingSystemVersion' => $this->operatingSystemVersion
        ];
    }

    public function fromArray(array $data): static
    {
        $this->machineName = $data['machineName'];
        $this->processName = $data['processName'];
        $this->processId = $data['processId'];
        $this->assembly = $data['assembly'];
        $this->assemblyVersion = $data['assemblyVersion'];
        $this->frameworkVersion = $data['frameworkVersion'];
        $this->massTransitVersion = $data['massTransitVersion'];
        $this->operatingSystemVersion = $data['operatingSystemVersion'];
        return $this;
    }
}
