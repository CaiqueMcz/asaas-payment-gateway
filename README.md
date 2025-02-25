# AsaasPaymentGateway
Uma biblioteca **não-oficial** para integração com a plataforma Asaas, desenvolvida no estilo **Laravel-like**. Proporciona uma interface fluida, intuitiva e orientada a objetos para gerenciar cobranças, assinaturas, clientes e outras funcionalidades, seguindo os padrões e a experiência de uso característicos do Laravel.
> **Nota Importante**: Esta é uma implementação não-oficial da API Asaas. Para informações oficiais, consulte a [documentação oficial da API Asaas](https://docs.asaas.com/).
## Instalação

```bash
composer require caique-marcelino/asaas-payment-gateway
```

## Inicialização

Para começar a usar a biblioteca, é necessário inicializar o Gateway com suas credenciais:

```php
use AsaasPaymentGateway\Gateway;

// Inicializa o Gateway (ambiente: 'sandbox' ou 'production')
Gateway::init('sua_api_key', 'seu_webhook_token', 'sandbox');
```

## Modelos Disponíveis

### Cliente (Customer)

```php
use AsaasPaymentGateway\Model\Customer;

// Criar um cliente
$customer = Customer::create([
    'name' => 'João Silva',
    'cpfCnpj' => '12345678901',
    'email' => 'joao@exemplo.com',
    'phone' => '4738010919',
    'mobilePhone' => '47998781877',
    'address' => 'Rua Teste',
    'addressNumber' => '123',
    'complement' => 'Apto 101',
    'province' => 'Centro',
    'postalCode' => '01001001',
    'externalReference' => 'CLIENTE123',
    'notificationDisabled' => false,
    'additionalEmails' => 'outro@exemplo.com',
    'municipalInscription' => '46683695908',
    'stateInscription' => '646.168.807',
    'observations' => 'Cliente VIP'
]);

// Obter cliente por ID
$customer = Customer::getById('cus_000123456789');

// Atualizar cliente
$customer->setName('João Silva Junior');
$customer->update([
    'name' => 'João Silva Junior',
    'email' => 'joao.jr@exemplo.com'
]);

// Listar clientes
$customers = Customer::all();
$customers = Customer::get(['name' => 'João']);
$customers = Customer::where('email', 'joao@exemplo.com')->get();

// Excluir cliente
$customer->delete();

// Restaurar cliente excluído
$customer->restore();

// Tokenizar cartão de crédito para o cliente
$creditCard = new \AsaasPaymentGateway\ValueObject\Payments\CreditCard(
    '4111111111111111',
    'João Silva',
    '12',
    '2030',
    '123'
);

$holderInfo = new \AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo(
    'João Silva',
    'joao@exemplo.com',
    '12345678901',
    '01001001',
    '123',
    'Apto 101',
    '4738010919',
    '47998781877'
);

$tokenizedCard = $customer->tokenizeCreditCard($creditCard, $holderInfo, '127.0.0.1');
```

### Cobrança (Payment)

```php
use AsaasPaymentGateway\Model\Payment;
use AsaasPaymentGateway\Enums\Payments\BillingType;
use AsaasPaymentGateway\Enums\Payments\DiscountType;
use AsaasPaymentGateway\ValueObject\Payments\Discount;
use AsaasPaymentGateway\ValueObject\Payments\Fine;
use AsaasPaymentGateway\ValueObject\Payments\Interest;
use AsaasPaymentGateway\ValueObject\Payments\SplitList;
use AsaasPaymentGateway\Model\Split;

// Criar uma cobrança simples
$payment = Payment::create([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::BOLETO(),
    'value' => 100.00,
    'dueDate' => '2025-12-31',
    'description' => 'Cobrança de teste'
]);

// Criar cobrança com desconto
$payment = Payment::create([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::BOLETO(),
    'value' => 100.00,
    'dueDate' => '2025-12-31',
    'description' => 'Cobrança com desconto',
    'discount' => new Discount(10.0, 5, DiscountType::PERCENTAGE())
]);

// Criar cobrança com multa e juros por atraso
$payment = Payment::create([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::BOLETO(),
    'value' => 100.00,
    'dueDate' => '2025-12-31',
    'description' => 'Cobrança com multa e juros',
    'fine' => new Fine(5.0, \AsaasPaymentGateway\Enums\Payments\FineType::PERCENTAGE()),
    'interest' => new Interest(2.5)
]);

// Criar cobrança com split (divisão de pagamentos)
$splitList = new SplitList();
$split = Split::fromArray([
    'walletId' => '7bafd95a-e783-4a62-9be1-23999af742c6',
    'percentualValue' => 20 // 20% do valor líquido
]);
$splitList->addSplit($split);

$payment = Payment::create([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::BOLETO(),
    'value' => 100.00,
    'dueDate' => '2025-12-31',
    'description' => 'Cobrança com split',
    'split' => $splitList
]);

// Criar cobrança com cartão de crédito
$creditCard = new \AsaasPaymentGateway\ValueObject\Payments\CreditCard(
    '4111111111111111',
    'João Silva',
    '12',
    '2030',
    '123'
);

$holderInfo = new \AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo(
    'João Silva',
    'joao@exemplo.com',
    '12345678901',
    '01001001',
    '123',
    'Apto 101',
    '4738010919',
    '47998781877'
);

$payment = Payment::createWithCreditCard([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => 100.00,
    'dueDate' => '2025-12-31',
    'description' => 'Pagamento com cartão',
    'creditCard' => $creditCard,
    'creditCardHolderInfo' => $holderInfo,
    'remoteIp' => '127.0.0.1'
]);

// Criar cobrança com cartão de crédito tokenizado
$payment = Payment::createWithCreditCardTokenized([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => 100.00,
    'dueDate' => '2025-12-31',
    'description' => 'Pagamento com cartão tokenizado',
    'creditCardToken' => 'token_123456789',
    'remoteIp' => '127.0.0.1'
]);

// Pagar uma cobrança existente com cartão de crédito
$pendingPayment = Payment::getById('pay_000123456789');
$paidPayment = $pendingPayment->payWithCreditCard($creditCard, $holderInfo);

// Pagar com cartão tokenizado
$paidPayment = $pendingPayment->payWithCreditCardTokenized('token_123456789');

// Pré-autorização de pagamento com cartão
$preAuthPayment = Payment::createWithCreditCard([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => 100.00,
    'dueDate' => '2025-12-31',
    'description' => 'Pagamento pré-autorizado',
    'creditCard' => $creditCard,
    'creditCardHolderInfo' => $holderInfo,
    'remoteIp' => '127.0.0.1',
    'authorizeOnly' => true
]);

// Capturar pagamento pré-autorizado
$capturedPayment = $preAuthPayment->captureAuthorizedPayment();

// Confirmar recebimento em dinheiro
$payment->confirmReceiveInCash(date('Y-m-d'), 100.00, true);

// Desfazer confirmação de recebimento em dinheiro
$payment->undoConfirmReceiveInCash();

// Estornar pagamento
$payment->refund();
// Com valor parcial
$payment->refund(50.00, 'Estorno parcial');

// Obter QR Code PIX
$pixQrCode = $payment->getPixQrCode();

// Obter informações de visualização
$viewingInfo = $payment->getViewingInfo();

// Obter dados bancários para identificação
$identification = $payment->getIdentificationField();

// Obter informações de pagamento
$billingInfo = $payment->getBillingInfo();

// Simular opções de pagamento
$simulationOptions = Payment::simulate(1000.00, 12, ['CREDIT_CARD', 'BOLETO', 'PIX']);

// Obter limites de pagamento
$limits = Payment::getLimits();

// Gerenciar documentos
$document = $payment->uploadDocument('INVOICE', '/caminho/para/arquivo.pdf', true);
$documentList = $payment->getDocuments();
$updatedDocument = $payment->updateDocument('doc_123456789', true, 'CONTRACT');
$payment->deleteDocument('doc_123456789');

// Estornar pagamento via boleto bancário
$payment->bankSlipRefund();
```

### Parcelamento (Installment)

```php
use AsaasPaymentGateway\Model\Installment;
use AsaasPaymentGateway\Enums\Payments\BillingType;

// Criar um parcelamento
$installment = Installment::create([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => 100.00, // Valor de cada parcela
    'installmentCount' => 3, // Número de parcelas
    'dueDate' => '2023-12-31', // Data de vencimento da primeira parcela
    'description' => 'Parcelamento de teste',
    'externalReference' => 'PARCELA123'
]);

// Criar parcelamento com cartão de crédito
$installment = Installment::create([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => 100.00,
    'installmentCount' => 3,
    'dueDate' => '2025-12-31',
    'description' => 'Parcelamento com cartão',
    'creditCard' => $creditCard,
    'creditCardHolderInfo' => $holderInfo,
    'remoteIp' => '127.0.0.1'
]);

// Obter pagamentos de um parcelamento
$payments = $installment->getPayments();
$payments = $installment->getPayments('PENDING'); // Filtrar por status

// Obter carnê de pagamento
$paymentBookUrl = $installment->getPaymentBook();

// Atualizar divisões (splits) do parcelamento
$splitList = new SplitList();
$splitList->addSplit(Split::fromArray([
    'walletId' => '7bafd95a-e783-4a62-9be1-23999af742c6',
    'percentualValue' => 30
]));
$updatedSplits = $installment->updateSplits($splitList);

// Estornar parcelamento
$installment->refund();
```

### Assinatura (Subscription)

```php
use AsaasPaymentGateway\Model\Subscription;
use AsaasPaymentGateway\Enums\Payments\BillingType;
use AsaasPaymentGateway\Enums\Subscriptions\SubscriptionCycle;
use AsaasPaymentGateway\Enums\Subscriptions\SubscriptionStatus;

// Criar uma assinatura
$subscription = Subscription::create([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => 99.90,
    'nextDueDate' => '2023-12-31',
    'cycle' => SubscriptionCycle::MONTHLY(),
    'description' => 'Assinatura mensal',
    'externalReference' => 'ASSINATURA123',
    'maxPayments' => 12 // Limite de cobranças
]);

// Criar assinatura com cartão de crédito
$subscription = Subscription::createWithCreditCard([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => 99.90,
    'nextDueDate' => '2023-12-31',
    'cycle' => SubscriptionCycle::MONTHLY(),
    'description' => 'Assinatura com cartão',
    'creditCard' => $creditCard,
    'creditCardHolderInfo' => $holderInfo,
    'remoteIp' => '127.0.0.1'
]);

// Criar assinatura com cartão tokenizado
$subscription = Subscription::createWithCreditCardTokenized([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => 99.90,
    'nextDueDate' => '2023-12-31',
    'cycle' => SubscriptionCycle::MONTHLY(),
    'description' => 'Assinatura com cartão tokenizado',
    'creditCardToken' => 'token_123456789',
    'remoteIp' => '127.0.0.1'
]);

// Atualizar assinatura
$subscription->update([
    'value' => 119.90,
    'description' => 'Assinatura atualizada',
    'status' => SubscriptionStatus::INACTIVE()
]);

// Obter cobranças da assinatura
$payments = $subscription->getPayments();
$payments = $subscription->getPayments(['status' => 'CONFIRMED']);

// Obter carnê de pagamento
$paymentBookUrl = $subscription->getPaymentBook();
```

### Split (Divisão de Pagamentos)

```php
use AsaasPaymentGateway\Model\Split;

// Obter split por ID
$split = Split::getPaid('split_123456789');
$split = Split::getReceived('split_987654321');

// Listar splits
$paidSplits = Split::getAllPaid();
$receivedSplits = Split::getAllReceived();

// Filtrar splits
$filteredSplits = Split::getAllPaid(['status' => 'DONE']);
```

## Exemplos Completos

### Gerenciar Clientes e Cobranças

```php
// Inicializar o Gateway
Gateway::init('api_key', null, 'sandbox');

// Criar um cliente
$customer = Customer::create([
    'name' => 'José da Silva',
    'cpfCnpj' => '12345678901',
    'email' => 'jose@exemplo.com',
    'phone' => '4738010919',
    'mobilePhone' => '47998781877',
    'address' => 'Rua das Flores',
    'addressNumber' => '100',
    'complement' => 'Casa',
    'province' => 'Jardim Primavera',
    'postalCode' => '89000100'
]);

// Criar uma cobrança com desconto, multa, juros e split
$discount = new Discount(10.0, 5, DiscountType::PERCENTAGE()); // 10% de desconto se pagar até 5 dias antes
$fine = new Fine(2.0, \AsaasPaymentGateway\Enums\Payments\FineType::PERCENTAGE()); // 2% de multa por atraso
$interest = new Interest(1.0); // 1% de juros ao mês por atraso

$splitList = new SplitList();
$split = Split::fromArray([
    'walletId' => '7bafd95a-e783-4a62-9be1-23999af742c6',
    'percentualValue' => 10 // 10% do valor líquido
]);
$splitList->addSplit($split);

$payment = Payment::create([
    'customer' => $customer->getId(),
    'billingType' => BillingType::BOLETO(),
    'value' => 299.99,
    'dueDate' => date('Y-m-d', strtotime('+30 days')),
    'description' => 'Fatura #12345',
    'externalReference' => 'FATURA12345',
    'discount' => $discount,
    'fine' => $fine,
    'interest' => $interest,
    'split' => $splitList
]);

// Link para boleto
echo "URL do boleto: " . $payment->getBankSlipUrl() . "\n";

// QR Code PIX (se disponível)
$pixInfo = $payment->getPixQrCode();
if ($pixInfo) {
    echo "Payload PIX: " . $pixInfo->getPayload() . "\n";
    echo "QR Code: " . $pixInfo->getEncodedImage() . "\n";
}
```

### Assinatura Recorrente

```php
// Criar cliente
$customer = Customer::create([
    'name' => 'Maria Oliveira',
    'cpfCnpj' => '98765432109',
    'email' => 'maria@exemplo.com',
    'mobilePhone' => '11987654321'
]);

// Tokenizar cartão
$creditCard = new \AsaasPaymentGateway\ValueObject\Payments\CreditCard(
    '5555555555554444',
    'Maria Oliveira',
    '01',
    '2030',
    '123'
);

$holderInfo = new \AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo(
    'Maria Oliveira',
    'maria@exemplo.com',
    '98765432109',
    '01001001',
    '100',
    'Apto 200',
    '1138010919',
    '11987654321'
);

$tokenizedCard = $customer->tokenizeCreditCard($creditCard, $holderInfo, '127.0.0.1');

// Criar assinatura com cartão tokenizado
$subscription = Subscription::createWithCreditCardTokenized([
    'customer' => $customer->getId(),
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => 49.90,
    'nextDueDate' => date('Y-m-d'),
    'cycle' => SubscriptionCycle::MONTHLY(),
    'description' => 'Plano Básico Mensal',
    'creditCardToken' => $tokenizedCard->getCreditCardToken(),
    'remoteIp' => '127.0.0.1'
]);

// Obter cobranças geradas
$payments = $subscription->getPayments();
foreach ($payments->getRows() as $payment) {
    echo "Cobrança: " . $payment->getId() . " - Valor: " . $payment->getValue() . " - Status: " . $payment->getStatus() . "\n";
}
```

### Parcelamento com Cartão de Crédito

```php
// Criar parcelamento com cartão
$creditCard = new \AsaasPaymentGateway\ValueObject\Payments\CreditCard(
    '4111111111111111',
    'Carlos Pereira',
    '12',
    '2030',
    '123'
);

$holderInfo = new \AsaasPaymentGateway\ValueObject\Payments\CreditCardHolderInfo(
    'Carlos Pereira',
    'carlos@exemplo.com',
    '11122233344',
    '01001001',
    '123',
    'Casa',
    '1138010919',
    '11987654321'
);

// Calculando valor total com juros
$valorTotal = 1200.00;
$numParcelas = 6;
$valorParcela = $valorTotal / $numParcelas;

$installment = Installment::create([
    'customer' => 'cus_000123456789',
    'billingType' => BillingType::CREDIT_CARD(),
    'value' => $valorParcela,
    'installmentCount' => $numParcelas,
    'dueDate' => date('Y-m-d'),
    'description' => 'Compra parcelada',
    'externalReference' => 'COMPRA12345',
    'creditCard' => $creditCard,
    'creditCardHolderInfo' => $holderInfo,
    'remoteIp' => '127.0.0.1'
]);

// Obter pagamentos do parcelamento
$payments = $installment->getPayments();
```

## Tratamento de Erros

```php
use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Exception\AsaasValidationException;
use AsaasPaymentGateway\Exception\AsaasPageNotFoundException;

try {
    $customer = Customer::getById('cus_id_inexistente');
} catch (AsaasPageNotFoundException $e) {
    echo "Cliente não encontrado: " . $e->getMessage();
} catch (AsaasValidationException $e) {
    echo "Erro de validação: " . $e->getMessage();
    $errors = $e->getErrors();
    foreach ($errors as $error) {
        echo "- " . $error['description'] . "\n";
    }
} catch (AsaasException $e) {
    echo "Erro na API Asaas: " . $e->getMessage();
} catch (\Exception $e) {
    echo "Erro geral: " . $e->getMessage();
}
```

## Ciclos de Assinatura Disponíveis

- `WEEKLY`: Semanal
- `BIWEEKLY`: Quinzenal
- `MONTHLY`: Mensal
- `BIMONTHLY`: Bimestral
- `QUARTERLY`: Trimestral
- `SEMIANNUALLY`: Semestral
- `YEARLY`: Anual

## Formas de Pagamento

- `BOLETO`: Boleto Bancário
- `CREDIT_CARD`: Cartão de Crédito
- `PIX`: Pagamento Instantâneo PIX
- `DEPOSIT`: Depósito Bancário
- `UNDEFINED`: Indefinido

## Status de Pagamento

- `PENDING`: Pendente
- `AUTHORIZED`: Autorizado
- `CONFIRMED`: Confirmado
- `RECEIVED`: Recebido
- `OVERDUE`: Vencido
- `REFUNDED`: Estornado
- `RECEIVED_IN_CASH`: Recebido em dinheiro
- `REFUND_REQUESTED`: Estorno solicitado
- `REFUND_IN_PROGRESS`: Estorno em andamento
- `CHARGEBACK_REQUESTED`: Chargeback solicitado
- `CHARGEBACK_DISPUTE`: Disputa de chargeback
- `AWAITING_CHARGEBACK_REVERSAL`: Aguardando reversão de chargeback
- `DUNNING_REQUESTED`: Cobrança solicitada
- `DUNNING_RECEIVED`: Cobrança recebida
- `AWAITING_RISK_ANALYSIS`: Aguardando análise de risco

## Requisitos

- PHP 7.4 ou superior
- Extensão cURL habilitada
- Extensão JSON habilitada

## Licença

Esta biblioteca é distribuída sob a licença MIT.
