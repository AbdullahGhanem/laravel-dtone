<?php

namespace Ghanem\Dtone;

class DtoneController
{
    public function services(?int $page = null, ?int $per_page = null): array
    {
        return Request::services($page, $per_page);
    }

    public function serviceById(int $id): array
    {
        return Request::serviceById($id);
    }

    public function countries(?int $page = null, ?int $per_page = null): array
    {
        return Request::countries($page, $per_page);
    }

    public function countryByIsoCode(string $iso_code): array
    {
        return Request::countryByIsoCode($iso_code);
    }

    public function operators(?string $country_iso_code = null, ?int $page = null, ?int $per_page = null): array
    {
        return Request::operators($country_iso_code, $page, $per_page);
    }

    public function operatorById(int $id): array
    {
        return Request::operatorById($id);
    }

    public function balances(): array
    {
        return Request::balances();
    }

    public function products(
        ?string $type = null,
        ?int $service_id = null,
        ?string $country_iso_code = null,
        array $benefit_types = [],
        ?int $page = null,
        ?int $per_page = null
    ): array {
        return Request::products($type, $service_id, $country_iso_code, $benefit_types, $page, $per_page);
    }

    public function transactions(?int $page = null, ?int $per_page = null): array
    {
        return Request::transactions($page, $per_page);
    }

    public function productById(int $id): array
    {
        return Request::productById($id);
    }

    public function lookupOperatorsByMobileNumber(string $mobile_number): array
    {
        return Request::lookupOperatorsByMobileNumber($mobile_number);
    }

    public function createTransaction(string $external_id, int $product_id, array $credit_party_identifier, bool $auto_confirm = false): array
    {
        return Request::createTransaction($external_id, $product_id, $credit_party_identifier, $auto_confirm);
    }

    public function confirmTransaction(int $transaction_id): array
    {
        return Request::confirmTransaction($transaction_id);
    }
}
