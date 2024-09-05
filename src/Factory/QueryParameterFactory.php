<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Factory;

use BackedEnum;
use DateTimeInterface;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\FilterOperator;
use NickSmit\OpenF1Api\Filter\InputFilter;

class QueryParameterFactory
{
    public function createParameters(array $rawParams): array
    {
        $queryParams = [];

        foreach ($rawParams as $name => $value) {
            if ($value === null) {
                continue;
            }

            if ($value instanceof InputFilter) {
                $queryParams = $this->handleInputFilter($name, $value, $queryParams);
            } elseif ($value instanceof DateFilter) {
                $queryParams = $this->handleDateFilter($value, $queryParams);
            } elseif ($value instanceof DateTimeInterface) {
                $queryParams[$name] = $value->format(DateTimeInterface::ATOM);
            } elseif ($value instanceof BackedEnum) {
                $queryParams[$name] = $value->value;
            } else {
                $queryParams[$name] = $value;
            }
        }

        return $queryParams;
    }

    private function handleInputFilter(string $queryParam, InputFilter $inputFilter, array $queryParams): array
    {
        $paramName = match ($inputFilter->getFilterOperator()) {
            FilterOperator::Equal => $queryParam,
            FilterOperator::GreaterThan, FilterOperator::LessThan => $queryParam . $inputFilter->getFilterOperator()->value,
        };

        $queryParams[$paramName] = $inputFilter->getValue();
        return $queryParams;
    }

    private function handleDateFilter(?DateFilter $dateFilter, array $queryParams): array
    {
        if ($dateFilter === null) {
            return $queryParams;
        }

        if ($dateFilter->exactDate !== null) {
            $queryParams['date'] = $dateFilter->exactDate->format(DateTimeInterface::ATOM);
            return $queryParams;
        }

        if ($dateFilter->afterDate !== null) {
            $queryParams['date_start>'] = $dateFilter->afterDate->format(DateTimeInterface::ATOM);
        }

        if ($dateFilter->beforeDate !== null) {
            $queryParams['date_end<'] = $dateFilter->beforeDate->format(DateTimeInterface::ATOM);
        }

        return $queryParams;
    }
}