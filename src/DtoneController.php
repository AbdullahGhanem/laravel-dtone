<?php

namespace Ghanem\Dtone;

use Ghanem\Dtone\Dto\Balance;
use Ghanem\Dtone\Dto\BenefitType;
use Ghanem\Dtone\Dto\Campaign;
use Ghanem\Dtone\Dto\Country;
use Ghanem\Dtone\Dto\Operator;
use Ghanem\Dtone\Dto\PaginatedResponse;
use Ghanem\Dtone\Dto\Product;
use Ghanem\Dtone\Dto\Promotion;
use Ghanem\Dtone\Dto\Service;
use Ghanem\Dtone\Dto\Transaction;

class DtoneController
{
    // -------------------------------------------------------------------------
    // Services
    // -------------------------------------------------------------------------

    /**
     * @return PaginatedResponse
     */
    public function services(?int $page = null, ?int $per_page = null)
    {
        return PaginatedResponse::fromArray(Request::services($page, $per_page), Service::class);
    }

    /**
     * @return Service
     */
    public function serviceById(int $id)
    {
        return Service::fromArray(Request::serviceById($id));
    }

    // -------------------------------------------------------------------------
    // Countries
    // -------------------------------------------------------------------------

    /**
     * @return PaginatedResponse
     */
    public function countries(?int $page = null, ?int $per_page = null)
    {
        return PaginatedResponse::fromArray(Request::countries($page, $per_page), Country::class);
    }

    /**
     * @return Country
     */
    public function countryByIsoCode(string $iso_code)
    {
        return Country::fromArray(Request::countryByIsoCode($iso_code));
    }

    // -------------------------------------------------------------------------
    // Operators
    // -------------------------------------------------------------------------

    /**
     * @return PaginatedResponse
     */
    public function operators(?string $country_iso_code = null, ?int $page = null, ?int $per_page = null)
    {
        return PaginatedResponse::fromArray(Request::operators($country_iso_code, $page, $per_page), Operator::class);
    }

    /**
     * @return Operator
     */
    public function operatorById(int $id)
    {
        return Operator::fromArray(Request::operatorById($id));
    }

    // -------------------------------------------------------------------------
    // Balances
    // -------------------------------------------------------------------------

    /**
     * @return Balance[]
     */
    public function balances()
    {
        return array_map([Balance::class, 'fromArray'], Request::balances());
    }

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

    /**
     * @return PaginatedResponse
     */
    public function products(
        ?string $type = null,
        ?int $service_id = null,
        ?string $country_iso_code = null,
        array $benefit_types = [],
        ?int $page = null,
        ?int $per_page = null
    ) {
        return PaginatedResponse::fromArray(
            Request::products($type, $service_id, $country_iso_code, $benefit_types, $page, $per_page),
            Product::class
        );
    }

    /**
     * @return Product
     */
    public function productById(int $id)
    {
        return Product::fromArray(Request::productById($id));
    }

    // -------------------------------------------------------------------------
    // Campaigns
    // -------------------------------------------------------------------------

    /**
     * @return PaginatedResponse
     */
    public function campaigns(?int $page = null, ?int $per_page = null)
    {
        return PaginatedResponse::fromArray(Request::campaigns($page, $per_page), Campaign::class);
    }

    /**
     * @return Campaign
     */
    public function campaignById(int $id)
    {
        return Campaign::fromArray(Request::campaignById($id));
    }

    // -------------------------------------------------------------------------
    // Promotions
    // -------------------------------------------------------------------------

    /**
     * @return PaginatedResponse
     */
    public function promotions(?int $page = null, ?int $per_page = null)
    {
        return PaginatedResponse::fromArray(Request::promotions($page, $per_page), Promotion::class);
    }

    /**
     * @return Promotion
     */
    public function promotionById(int $id)
    {
        return Promotion::fromArray(Request::promotionById($id));
    }

    // -------------------------------------------------------------------------
    // Benefit Types
    // -------------------------------------------------------------------------

    /**
     * @return PaginatedResponse
     */
    public function benefitTypes(?int $page = null, ?int $per_page = null)
    {
        return PaginatedResponse::fromArray(Request::benefitTypes($page, $per_page), BenefitType::class);
    }

    // -------------------------------------------------------------------------
    // Transactions
    // -------------------------------------------------------------------------

    /**
     * @return PaginatedResponse
     */
    public function transactions(?int $page = null, ?int $per_page = null)
    {
        return PaginatedResponse::fromArray(Request::transactions($page, $per_page), Transaction::class);
    }

    /**
     * @return Transaction
     */
    public function transactionById(int $id)
    {
        return Transaction::fromArray(Request::transactionById($id));
    }

    /**
     * @return Transaction
     */
    public function createTransaction(string $external_id, int $product_id, array $credit_party_identifier, bool $auto_confirm = false)
    {
        return Transaction::fromArray(Request::createTransaction($external_id, $product_id, $credit_party_identifier, $auto_confirm));
    }

    /**
     * @return Transaction
     */
    public function createTransactionSync(string $external_id, int $product_id, array $credit_party_identifier, bool $auto_confirm = false)
    {
        return Transaction::fromArray(Request::createTransactionSync($external_id, $product_id, $credit_party_identifier, $auto_confirm));
    }

    /**
     * @return Transaction
     */
    public function confirmTransaction(int $transaction_id)
    {
        return Transaction::fromArray(Request::confirmTransaction($transaction_id));
    }

    /**
     * @return Transaction
     */
    public function confirmTransactionSync(int $transaction_id)
    {
        return Transaction::fromArray(Request::confirmTransactionSync($transaction_id));
    }

    /**
     * @return Transaction
     */
    public function cancelTransaction(int $transaction_id)
    {
        return Transaction::fromArray(Request::cancelTransaction($transaction_id));
    }

    // -------------------------------------------------------------------------
    // Lookups
    // -------------------------------------------------------------------------

    /**
     * @return PaginatedResponse
     */
    public function lookupOperatorsByMobileNumber(string $mobile_number)
    {
        return PaginatedResponse::fromArray(Request::lookupOperatorsByMobileNumber($mobile_number), Operator::class);
    }

    /**
     * @return array
     */
    public function statementInquiry(int $product_id, array $credit_party_identifier)
    {
        return Request::statementInquiry($product_id, $credit_party_identifier);
    }

    /**
     * @return array
     */
    public function creditPartyBenefits(int $product_id, array $credit_party_identifier)
    {
        return Request::creditPartyBenefits($product_id, $credit_party_identifier);
    }

    /**
     * @return array
     */
    public function creditPartyStatus(int $product_id, array $credit_party_identifier)
    {
        return Request::creditPartyStatus($product_id, $credit_party_identifier);
    }
}
