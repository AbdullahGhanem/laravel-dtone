<?php

namespace Ghanem\Dtone;

class DtoneController
{
    // -------------------------------------------------------------------------
    // Services
    // -------------------------------------------------------------------------

    public function services(?int $page = null, ?int $per_page = null): array
    {
        return Request::services($page, $per_page);
    }

    public function serviceById(int $id): array
    {
        return Request::serviceById($id);
    }

    // -------------------------------------------------------------------------
    // Countries
    // -------------------------------------------------------------------------

    public function countries(?int $page = null, ?int $per_page = null): array
    {
        return Request::countries($page, $per_page);
    }

    public function countryByIsoCode(string $iso_code): array
    {
        return Request::countryByIsoCode($iso_code);
    }

    // -------------------------------------------------------------------------
    // Operators
    // -------------------------------------------------------------------------

    public function operators(?string $country_iso_code = null, ?int $page = null, ?int $per_page = null): array
    {
        return Request::operators($country_iso_code, $page, $per_page);
    }

    public function operatorById(int $id): array
    {
        return Request::operatorById($id);
    }

    // -------------------------------------------------------------------------
    // Balances
    // -------------------------------------------------------------------------

    public function balances(): array
    {
        return Request::balances();
    }

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

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

    public function productById(int $id): array
    {
        return Request::productById($id);
    }

    // -------------------------------------------------------------------------
    // Campaigns
    // -------------------------------------------------------------------------

    public function campaigns(?int $page = null, ?int $per_page = null): array
    {
        return Request::campaigns($page, $per_page);
    }

    public function campaignById(int $id): array
    {
        return Request::campaignById($id);
    }

    // -------------------------------------------------------------------------
    // Promotions
    // -------------------------------------------------------------------------

    public function promotions(?int $page = null, ?int $per_page = null): array
    {
        return Request::promotions($page, $per_page);
    }

    public function promotionById(int $id): array
    {
        return Request::promotionById($id);
    }

    // -------------------------------------------------------------------------
    // Benefit Types
    // -------------------------------------------------------------------------

    public function benefitTypes(?int $page = null, ?int $per_page = null): array
    {
        return Request::benefitTypes($page, $per_page);
    }

    // -------------------------------------------------------------------------
    // Transactions
    // -------------------------------------------------------------------------

    public function transactions(?int $page = null, ?int $per_page = null): array
    {
        return Request::transactions($page, $per_page);
    }

    public function transactionById(int $id): array
    {
        return Request::transactionById($id);
    }

    public function createTransaction(string $external_id, int $product_id, array $credit_party_identifier, bool $auto_confirm = false): array
    {
        return Request::createTransaction($external_id, $product_id, $credit_party_identifier, $auto_confirm);
    }

    public function createTransactionSync(string $external_id, int $product_id, array $credit_party_identifier, bool $auto_confirm = false): array
    {
        return Request::createTransactionSync($external_id, $product_id, $credit_party_identifier, $auto_confirm);
    }

    public function confirmTransaction(int $transaction_id): array
    {
        return Request::confirmTransaction($transaction_id);
    }

    public function confirmTransactionSync(int $transaction_id): array
    {
        return Request::confirmTransactionSync($transaction_id);
    }

    public function cancelTransaction(int $transaction_id): array
    {
        return Request::cancelTransaction($transaction_id);
    }

    // -------------------------------------------------------------------------
    // Lookups
    // -------------------------------------------------------------------------

    public function lookupOperatorsByMobileNumber(string $mobile_number): array
    {
        return Request::lookupOperatorsByMobileNumber($mobile_number);
    }

    public function statementInquiry(int $product_id, array $credit_party_identifier): array
    {
        return Request::statementInquiry($product_id, $credit_party_identifier);
    }

    public function creditPartyBenefits(int $product_id, array $credit_party_identifier): array
    {
        return Request::creditPartyBenefits($product_id, $credit_party_identifier);
    }

    public function creditPartyStatus(int $product_id, array $credit_party_identifier): array
    {
        return Request::creditPartyStatus($product_id, $credit_party_identifier);
    }
}
