<?php

namespace AsaasPaymentGateway\Tests\Feature\Model;

interface RepositoryTestInterface
{
    public function getRandomData(): array;

    public function getFieldInfos(): array;

    public function getModelClass(): string;
    public function getRepositoryClass(): string;
}
