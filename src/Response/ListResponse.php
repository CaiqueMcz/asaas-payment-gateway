<?php

namespace AsaasPaymentGateway\Response;

use AsaasPaymentGateway\Model\AbstractModel;

class ListResponse
{
    private string $object;
    private bool $hasMore;
    private int $totalCount;
    private int $limit;
    private int $offset;
    private array $data;
    private string $modelClass;
    private array $parsedRows;

    private function __construct(
        string $modelClass,
        string $object,
        bool $hasMore,
        int $totalCount,
        int $limit,
        int $offset,
        array $data
    ) {
        $this->object = $object;
        $this->hasMore = $hasMore;
        $this->totalCount = $totalCount;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->data = $data;
        $this->modelClass = $modelClass;
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['modelClass'],
            $array['object'] ?? 'list',
            $array['hasMore'] ?? false,
            $array['totalCount'] ?? 0,
            $array['limit'] ?? 0,
            $array['offset'] ?? 0,
            $array['data'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'object' => $this->object,
            'hasMore' => $this->hasMore,
            'totalCount' => $this->totalCount,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'data' => $this->data,
        ];
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function hasMore(): bool
    {
        return $this->hasMore;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getFirstRow()
    {
        if ($this->getTotalCount() > 0) {
            $rows = $this->getRows();
            return $rows[0];
        }
        return [];
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getRows(): array
    {
        if (empty($this->parsedRows)) {
            $this->parsedRows = AbstractModel::parseRows($this->modelClass, $this->data);
        }
        return $this->parsedRows;
    }
}
