<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Contracts\Arrayable;

class Host implements Arrayable
{
    public string|null $machineName = null;
    public string|null $processName = null;
    public int|null $processId = null;
    public string|null $assembly = null;
    public string|null $assemblyVersion = null;
    public string|null $frameworkVersion = null;
    public string|null $massTransitVersion = null;
    public string|null $operatingSystemVersion = null;

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
