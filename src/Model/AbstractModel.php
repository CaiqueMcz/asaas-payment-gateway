<?php

namespace AsaasPaymentGateway\Model;

use AsaasPaymentGateway\Exception\AsaasException;
use AsaasPaymentGateway\Helpers\CDate;
use AsaasPaymentGateway\Repository\AbstractRepository;
use AsaasPaymentGateway\Response\ListResponse;
use AsaasPaymentGateway\Traits\Model\HasRequiredId;
use AsaasPaymentGateway\ValueObject\ArrayableInterface;
use AsaasPaymentGateway\ValueObject\SearchParamsBuilder;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use MyCLabs\Enum\Enum;
use ReflectionClass;

abstract class AbstractModel implements ArrayableInterface
{
    use HasRequiredId;

    private static ?array $repository = null;
    protected array $fields = [];
    protected array $requiredFields = [];
    protected array $casts = [];
    protected array $attributes = [];

    /**
     * @throws AsaasException
     */
    public function __construct(array $data)
    {
        foreach ($this->requiredFields as $field) {
            if (!isset($data[$field])) {
                throw AsaasException::requiredFieldException($field);
            }
        }
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fields)) {
                if (isset($this->casts[$key]) && !is_null($value)) {
                    $value = $this->castValue($value, $this->casts[$key]);
                }

                $this->attributes[$key] = $value;
            }
        }
    }

    /**
     * @throws AsaasException
     * @throws \DateMalformedStringException
     */
    protected function castValue($value, string $type)
    {
        if (class_exists($type)) {
            if ($value instanceof $type) {
                return $value;
            }
            if (is_array($value) && method_exists($type, 'fromArray')) {
                return $type::fromArray($value);
            }
            if (is_string($value) && method_exists($type, 'from')) {
                return $type::from($value);
            }
        } else {
            switch (strtolower($type)) {
                case 'int':
                case 'integer':
                    if (!is_numeric($value)) {
                        throw new AsaasException("Invalid integer value: " . json_encode($value));
                    }
                    return (int)$value;

                case 'float':
                case 'double':
                    if (!is_numeric($value)) {
                        throw new AsaasException("Invalid float value: " . json_encode($value));
                    }
                    return (float)$value;

                case 'bool':
                case 'boolean':
                    if (!in_array($value, [0, 1, '0', '1', true, false], true)) {
                        throw new AsaasException("Invalid boolean value: " . json_encode($value));
                    }
                    return (bool)$value;

                case 'string':
                    if (!is_string($value)) {
                        throw new AsaasException("Invalid string value: " . json_encode($value));
                    }
                    return (string)$value;
                case 'date':
                    if ($value instanceof CDate) {
                        return $value;
                    }
                    if (is_string($value)) {
                        return new CDate($value);
                    }
                    throw new AsaasException("Invalid datetime format");

                case 'datetime':
                    if ($value instanceof DateTime) {
                        return $value;
                    }
                    if (!is_string($value)) {
                        $msg = "Invalid datetime value: expected string or DateTime instance, got " . gettype($value);
                        throw new AsaasException($msg);
                    }
                    try {
                        return new DateTime($value);
                    } catch (Exception $e) {
                        throw new AsaasException("Invalid datetime format: " . $e->getMessage());
                    }

                default:
                    throw new AsaasException("Unsupported type conversion: '{$type}' is not a valid type.");
            }
        }
        throw new AsaasException("Unknown cast type $type");
    }

    /**
     * @throws AsaasException
     */
    public static function fromArray(array $data): self
    {
        return new static($data);
    }

    /**
     * @throws AsaasException
     */
    public static function all($filters = []): ListResponse
    {
        return self::getRepository()->list($filters);
    }

    /**
     * @throws AsaasException
     */
    public static function getRepository(): AbstractRepository
    {
        $index = static::class;
        if (isset(static::$repository[$index]) && !is_null(static::$repository[$index])) {
            return static::$repository[$index];
        }

        $repositoryClass = static::getRepositoryClass();
        if (class_exists($repositoryClass) && is_subclass_of($repositoryClass, AbstractRepository::class)) {
            self::$repository[$index] = new $repositoryClass($index);
            return self::$repository[$index];
        }

        throw new AsaasException('Repository not found.');
    }

    public static function getRepositoryClass(): string
    {
        $reflection = new ReflectionClass(static::class);
        return "\\AsaasPaymentGateway\\Repository\\{$reflection->getShortName()}Repository";
    }


    public static function parseRows(string $modelClass, array $response): array
    {
        $rows = $response['data'] ?? $response;
        return array_map(static function ($data) use ($modelClass) {
            return call_user_func([$modelClass, 'fromArray'], $data);
        }, $rows);
    }

    /**
     * @throws AsaasException
     */
    public static function get($filters = []): ListResponse
    {
        return static::getRepository()->list($filters);
    }

    public static function where(string $field, string $value): SearchParamsBuilder
    {
        return (new SearchParamsBuilder(static::class))->where($field, $value);
    }

    public static function injectRepository(string $index, ?AbstractRepository $repository): void
    {
        self::$repository[$index] = $repository;
    }

    public static function resetRepository(string $index): void
    {
        self::$repository[$index] = null;
    }

    /**
     * @throws AsaasException
     */
    public function refresh(): ?self
    {
        $this->hasIdOrFails();
        return self::getById($this->getId());
    }

    /**
     * @throws AsaasException
     */
    public static function getById(string $id): ?self
    {
        return self::getRepository()->getById($id);
    }

    /**
     * @throws AsaasException
     */
    public function __call(string $method, array $arguments)
    {
        // Handle boolean getters with 'is' prefix
        if (strpos($method, 'is') === 0) {
            $property = lcfirst(substr($method, 2));
            if (
                in_array($property, $this->fields, true) &&
                isset($this->casts[$property]) &&
                in_array(strtolower($this->casts[$property]), ['bool', 'boolean'])
            ) {
                return $this->__get($property);
            }
        }

        // Handle boolean setters with 'setIs' prefix
        if (strpos($method, 'setIs') === 0) {
            $property = lcfirst(substr($method, 5));
            if (
                in_array($property, $this->fields, true) &&
                isset($this->casts[$property]) &&
                in_array(strtolower($this->casts[$property]), ['bool', 'boolean'])
            ) {
                $this->__set($property, $arguments[0] ?? null);
                return $this;
            }
        }

        // Handle regular getters
        if (strpos($method, 'get') === 0) {
            $property = lcfirst(substr($method, 3));
            if (in_array($property, $this->fields, true)) {
                return $this->__get($property);
            }
        }

        // Handle regular setters
        if (strpos($method, 'set') === 0) {
            $property = lcfirst(substr($method, 3));
            if (in_array($property, $this->fields, true)) {
                $this->__set($property, $arguments[0] ?? null);
                return $this;
            }
        }

        throw new AsaasException("Method {$method} not defined in " . static::class);
    }

    /**
     * @throws AsaasException
     */
    public function __get(string $name)
    {
        if (in_array($name, $this->fields, true)) {
            return $this->attributes[$name] ?? null;
        }
        throw AsaasException::undefinedPropertyException($name);
    }

    /**
     * @throws AsaasException
     */
    public function __set(string $name, $value): void
    {
        if (in_array($name, $this->fields, true)) {
            if (isset($this->casts[$name])) {
                $value = $this->castValue($value, $this->casts[$name]);
            }
            $this->attributes[$name] = $value;
        } else {
            throw AsaasException::undefinedPropertyException($name);
        }
    }

    public function __isset(string $name): bool
    {
        return in_array($name, $this->fields, true) && isset($this->attributes[$name]);
    }

    /**
     * @throws GuzzleException
     * @throws AsaasException
     */
    public function save(): ?self
    {

        if ($this->getId() === null && method_exists($this, 'create')) {
            return $this->create($this->toArray());
        }
        if ($this->getId() !== null && method_exists($this, 'update')) {
            return $this->update($this->toArray());
        }
        throw new AsaasException('Method save not defined in ' . static::class);
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this->fields as $field) {
            $val = $this->attributes[$field] ?? null;
            if (is_null($val)) {
                unset($data[$field]);
            } else {
                $val = $this->parseToArray($val);
                $data[$field] = $val;
            }
        }
        return $data;
    }

    private function parseToArray($val)
    {
        if (is_object($val) and $val instanceof ArrayableInterface) {
            $val = $val->toArray();
        }
        if (is_object($val) and $val instanceof Enum) {
            $val = $val->getValue();
        }
        if (is_object($val) and $val instanceof CDate) {
            $val = (string)$val;
        }
        if (is_array($val)) {
            foreach ($val as $key => $value) {
                $val[$key] = $this->parseToArray($value);
            }
        }
        return $val;
    }

    public function getEndpoint(): ?string
    {

        return self::getRepository()->getEndpoint();
    }
}
