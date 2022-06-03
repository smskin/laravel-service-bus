<?php

namespace SMSkin\ServiceBus\Packages\Messages;

class TestMessage extends BaseMessage
{
    public string $string1;
    public string $string2;

    public function toArray(): array
    {
        return [
            'string1' => $this->string1,
            'string2' => $this->string2
        ];
    }

    public function fromArray(array $data): static
    {
        $this->string1 = $data['string1'];
        $this->string2 = $data['string2'];
        return $this;
    }

    /**
     * @param string $string1
     * @return TestMessage
     */
    public function setString1(string $string1): TestMessage
    {
        $this->string1 = $string1;
        return $this;
    }

    /**
     * @param string $string2
     * @return TestMessage
     */
    public function setString2(string $string2): TestMessage
    {
        $this->string2 = $string2;
        return $this;
    }
}
