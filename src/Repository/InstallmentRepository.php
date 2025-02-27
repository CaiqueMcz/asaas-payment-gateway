<?php

namespace CaiqueMcz\AsaasPaymentGateway\Repository;

use CaiqueMcz\AsaasPaymentGateway\Exception\AsaasException;
use CaiqueMcz\AsaasPaymentGateway\Helpers\Utils;
use CaiqueMcz\AsaasPaymentGateway\Model\Installment;
use CaiqueMcz\AsaasPaymentGateway\Model\Payment;
use CaiqueMcz\AsaasPaymentGateway\Model\Split;
use CaiqueMcz\AsaasPaymentGateway\Response\ListResponse;
use CaiqueMcz\AsaasPaymentGateway\ValueObject\Payments\SplitList;
use GuzzleHttp\Exception\GuzzleException;

class InstallmentRepository extends AbstractRepository
{
    /**
     * @throws AsaasException
     */
    public function getPayments(string $id, string $status = null, int $offset = 0, int $limit = 10): ?ListResponse
    {
        $data = ['offset' => $offset, 'limit' => $limit];
        if (!is_null($status)) {
            $data = ['status' => $status];
        }
        $response = $this->client->get($this->endpoint . '/' . $id . '/payments', $data);
        $response['modelClass'] = Payment::class;
        return ListResponse::fromArray($response);
    }

    /**
     * @throws AsaasException
     */
    public function getPaymentBook(string $id, string $sort = null, string $order = null): ?string
    {
        $data = [];
        if (!is_null($sort)) {
            $data['status'] = $sort;
        }
        if (!is_null($order)) {
            $data['order'] = $order;
        }
        $response = $this->client->get($this->endpoint . '/' . $id . '/paymentBook', $data);
        $responseText = $response[0];
        if (Utils::strContains("PDF", $responseText)) {
            return $responseText;
        }
        return null;
    }

    /**
     * @throws AsaasException|GuzzleException
     */
    public function refund(string $id): ?Installment
    {
        $response = $this->client->post($this->endpoint . '/' . $id . '/refund', []);

        return Installment::fromArray($response);
    }

    /**
     * @throws AsaasException
     */
    public function updateSplits(string $id, SplitList $splitList): ?SplitList
    {

        $splits = $splitList->toArray();

        $response = $this->client->put($this->endpoint . '/' . $id . '/splits', ['splits' => $splits]);
        if (isset($response['splits']) && is_array($response['splits'])) {
            $splitList = new SplitList();
            foreach ($response['splits'] as $split) {
                $splitList->addSplit(Split::fromArray($split));
            }
            return $splitList;
        }
        return null;
    }
}
