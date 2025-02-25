<?php

namespace AsaasPaymentGateway\Repository;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Exception\AsaasValidationException;
use AsaasPaymentGateway\Model\Payment;
use AsaasPaymentGateway\Response\ListResponse;
use AsaasPaymentGateway\ValueObject\Payments\Limits\PaymentLimitsResponse;
use AsaasPaymentGateway\ValueObject\Payments\PaymentDocument;
use AsaasPaymentGateway\ValueObject\Payments\PixQrcode;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Payment Repository
 * @method  Payment create(array $data)
 */
class PaymentRepository extends AbstractRepository
{
    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function captureAuthorizedPayment(string $id): ?Payment
    {
        $response = $this->client->post($this->endpoint . '/' . $id . '/captureAuthorizedPayment', []);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function payWithCreditCard(string $id, array $data): ?Payment
    {
        $data = $this->prepareSendData($data);
        $response = $this->client->post($this->endpoint . '/' . $id . '/payWithCreditCard', $data);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function createWithCreditCard(array $data): ?Payment
    {
        return $this->create($data);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function createWithCreditCardTokenized(array $data): ?Payment
    {
        return $this->create($data);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function getViewingInfo(string $id): ?array
    {
        return $this->client->get($this->endpoint . '/' . $id . '/viewingInfo');
    }


    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function getIdentificationField(string $id): ?array
    {
        return $this->client->get($this->endpoint . '/' . $id . '/identificationField');
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function getRefunds(string $id, int $offset = 0, int $limit = 10): ?ListResponse
    {
        $data = ['offset' => $offset, 'limit' => $limit];
        $refunds = $this->client->get($this->endpoint . '/' . $id . '/refunds', $data);
        return ListResponse::fromArray($refunds);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function refund(string $id, array $data): ?Payment
    {
        $refund = $this->client->post($this->endpoint . '/' . $id . '/refund', $data);
        return call_user_func([$this->modelClass, 'fromArray'], $refund);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function getPixQrCode(string $id): ?PixQrcode
    {
        $response = $this->client->get($this->endpoint . '/' . $id . '/pixQrCode');
        return PixQrcode::fromArray($response);
    }


    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function confirmReceiveInCash(string $id, array $data): ?Payment
    {
        $response = $this->client->post($this->endpoint . '/' . $id . '/receiveInCash', $data);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function undoConfirmReceiveInCash(string $id): ?Payment
    {
        $response = $this->client->post($this->endpoint . '/' . $id . '/undoReceivedInCash', []);
        return call_user_func([$this->modelClass, 'fromArray'], $response);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function getBillingInfo(string $id): ?array
    {
        return $this->client->get($this->endpoint . '/' . $id . '/billingInfo');
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function simulate(float $value, ?int $installmentCount, ?array $billingTypes): ?array
    {
        $requestData = [];
        $requestData['value'] = $value;
        if (!is_null($installmentCount)) {
            $requestData['installmentCount'] = $installmentCount;
        }
        if (!is_null($billingTypes)) {
            $requestData['billingTypes'] = $billingTypes;
        }
        return $this->client->post($this->endpoint . '/simulate', $requestData);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     * @throws AsaasValidationException
     */
    public function uploadDocument(string $id, array $data): ?PaymentDocument
    {
        if (!isset($data['file']) || !file_exists($data['file'])) {
            throw new AsaasException('File not found');
        }
        $response = $this->client->postWithFile($this->endpoint . '/' . $id . '/documents', $data);
        return PaymentDocument::fromArray($response);
    }

    /**
     * @throws AsaasException
     */
    public function getDocuments(string $id, int $offset = 0, int $limit = 10): ?ListResponse
    {
        $params = ['offset' => $offset, 'limit' => $limit];
        $response = $this->client->get($this->endpoint . '/' . $id . '/documents', $params);
        $response['modelClass'] = PaymentDocument::class;
        return ListResponse::fromArray($response);
    }

    /**
     * @throws AsaasException
     */
    public function updateDocument(string $id, string $documentId, array $data): ?PaymentDocument
    {
        $response = $this->client->put($this->endpoint . '/' . $id . '/documents/' . $documentId, $data);
        return PaymentDocument::fromArray($response);
    }

    /**
     * @throws AsaasException
     */
    public function getDocument(string $id, string $documentId): ?PaymentDocument
    {
        $response = $this->client->get($this->endpoint . '/' . $id . '/documents/' . $documentId, []);
        return PaymentDocument::fromArray($response);
    }

    /**
     * @throws AsaasException
     */
    public function deleteDocument(string $id, string $documentId): bool
    {
        $response = $this->client->delete($this->endpoint . '/' . $id . '/documents/' . $documentId, []);
        return isset($response['deleted']) && $response['deleted'] === true;
    }

    /**
     * @throws AsaasException
     */
    public function getLimits(): ?PaymentLimitsResponse
    {
        $response = $this->client->get($this->endpoint . '/limits');
        return PaymentLimitsResponse::fromArray($response);
    }

    /**
     * @throws AsaasException
     * @throws GuzzleException
     */
    public function bankSlipRefund(string $id): ?string
    {
        $response = $this->client->post($this->endpoint . '/' . $id . 'bankSlip/refund', []);
        return $response['requestUrl'] ?? null;
    }
}
