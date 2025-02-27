<?php

namespace CaiqueMcz\AsaasPaymentGateway\Tests\Integration\Model;

interface ModelInterface
{
    public function getRandomData(): array;

    public function getFieldInfos(): array;

    public function getModelClass(): string;
}
