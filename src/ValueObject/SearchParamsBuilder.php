<?php

namespace CaiqueMcz\AsaasPaymentGateway\ValueObject;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Model\AbstractModel;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;

class SearchParamsBuilder
{
    protected string $modelClass;
    protected array $wheres = [];

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function where(string $field, string $value): self
    {
        $this->wheres[$field] = $value;
        return $this;
    }

    public function get(): ?ListResponse
    {

        return $this->getList();
    }

    private function getList(): ?ListResponse
    {
        return call_user_func([$this->modelClass, 'get'], $this->wheres);
    }

    public function first(): ?AbstractModel
    {
        $response = $this->getList();
        if (is_null($response)) {
            return null;
        }
        if ($response->getTotalCount() > 0) {
            return $response->getRows()[0];
        }
        return null;
    }

    public function last(): ?AbstractModel
    {
        $response = $this->getList();
        if (is_null($response)) {
            return null;
        }
        $rows = $response->getRows();
        $rowsQuantity = count($rows);
        if ($rowsQuantity <= 0) {
            return null;
        }
        $lastRowIndex = $rowsQuantity - 1;
        if (isset($rows[$lastRowIndex])) {
            return $rows[$lastRowIndex];
        }
        return null;
    }
}
