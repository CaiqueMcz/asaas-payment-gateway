<?php

namespace AsaasPaymentGateway\Tests\Traits;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Gateway;
use AsaasPaymentGateway\Http\Client;
use AsaasPaymentGateway\Model\Split;
use AsaasPaymentGateway\ValueObject\Payments\CreditCard;
use AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo;
use Faker\Factory;
use Random\RandomException;

trait GatewayTrait
{
    public $faker;

    public function generateCreditCardData(): array
    {
        $name = $this->faker->name;
        $cpf = str_replace([",", ".", "-"], "", $this->faker->cpf);
        $creditCardHolderInfo = new CreditCardHolderInfo(
            $name,
            $this->faker->unique()->safeEmail,
            $cpf,
            "01452-001",
            "1600",
            "Apto 101",
            "11988877665",
            "11988877665"
        );
        $year = date("Y") + 2;
        $creditCard = new CreditCard($this->faker->creditCardNumber, $name, $this->faker->month, $year, 595);
        return ['creditCard' => $creditCard, 'creditCardHolderInfo' => $creditCardHolderInfo];
    }

    /**
     * @throws AsaasException
     * @throws RandomException
     */
    public function getRandSplit()
    {
        return Split::fromArray(['walletId' => getenv("ASAAS_SPLIT_WALLET_ID"),
            'percentualValue' => random_int(1, 20)]);
    }

    public function addInterceptor(string $method, string $endpoint, array $response)
    {
        call_user_func([$this->getModelClass(), 'resetRepository'], $this->getModelClass());
        //  Client::$interceptors = [];
        Client::addInterceptor($method, $endpoint, $response);
    }

    protected function initGateway()
    {

        Gateway::init($this->getGatewayApiKey(), $this->getWebhookAccessToken(), $this->getApiEnvironment());
        $this->faker = Factory::create('pt_BR');
    }

    public function getGatewayApiKey()
    {
        return getenv('ASAAS_SANDBOX_API_TOKEN');
    }

    public function getWebhookAccessToken(): ?string
    {
        return getenv('ASAAS_SANDBOX_WEBHOOK_TOKEN');
    }

    public function getApiEnvironment(): string
    {
        return getenv("APP_ENV");
    }
}
