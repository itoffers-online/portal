<?php

declare (strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer;

use Doctrine\DBAL\Connection;
use HireInSocial\Application\Offer\Offer;
use HireInSocial\Application\Offer\Offers;

final class DBALOffers implements Offers
{
    public const TABLE_NAME = 'his_job_offer';
    public const FIELD_ID = 'id';
    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_COMPANY_NAME = 'company_name';
    public const FIELD_COMPANY_URL = 'company_url';
    public const FIELD_COMPANY_DESCRIPTION = 'company_description';
    public const FIELD_POSITION_NAME = 'position_name';
    public const FIELD_POSITION_DESCRIPTION = 'position_description';
    public const FIELD_LOCATION_REMOTE = 'location_remote';
    public const FIELD_LOCATION_NAME = 'location_name';
    public const FIELD_SALARY_MIN = 'salary_min';
    public const FIELD_SALARY_MAX = 'salary_max';
    public const FIELD_SALARY_CURRENCY = 'salary_currency';
    public const FIELD_SALARY_NET = 'salary_net';
    public const FIELD_CONTRACT_TYPE = 'contract_type';
    public const FIELD_DESCRIPTION_REQUIREMENTS = 'description_requirements';
    public const FIELD_DESCRIPTION_BENEFITS = 'description_benefits';
    public const FIELD_CONTACT_EMAIL = 'contact_email';
    public const FIELD_CONTACT_NAME = 'contact_name';
    public const FIELD_CONTACT_PHONE = 'contact_phone';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function add(Offer $offer): void
    {
        $this->connection->insert(
            self::TABLE_NAME,
            [
                self::FIELD_ID => $offer->id()->toString(),
                self::FIELD_CREATED_AT => $offer->createdAt(),
                self::FIELD_COMPANY_NAME => $offer->company()->name(),
                self::FIELD_COMPANY_URL => $offer->company()->url(),
                self::FIELD_COMPANY_DESCRIPTION => $offer->company()->description(),
                self::FIELD_POSITION_NAME => $offer->position()->name(),
                self::FIELD_POSITION_DESCRIPTION => $offer->position()->description(),
                self::FIELD_LOCATION_REMOTE => $offer->location()->isRemote(),
                self::FIELD_LOCATION_NAME => $offer->location()->name(),
                self::FIELD_SALARY_MIN => $offer->salary()->min(),
                self::FIELD_SALARY_MAX => $offer->salary()->max(),
                self::FIELD_SALARY_CURRENCY => $offer->salary()->currencyCode(),
                self::FIELD_SALARY_NET => $offer->salary()->isNet(),
                self::FIELD_CONTRACT_TYPE => $offer->contract()->type(),
                self::FIELD_DESCRIPTION_REQUIREMENTS => $offer->description()->requirements(),
                self::FIELD_DESCRIPTION_BENEFITS => $offer->description()->benefits(),
                self::FIELD_CONTACT_EMAIL => $offer->contact()->email(),
                self::FIELD_CONTACT_NAME => $offer->contact() ->name(),
                self::FIELD_CONTACT_PHONE => $offer->contact()->phone()
            ],
            [
                self::FIELD_CREATED_AT => 'datetime_immutable',
                self::FIELD_SALARY_NET => 'boolean',
                self::FIELD_LOCATION_REMOTE => 'boolean'
            ]
        );
    }
}