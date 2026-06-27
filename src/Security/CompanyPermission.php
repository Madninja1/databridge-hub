<?php
declare(strict_types=1);

namespace App\Security;

final class CompanyPermission
{
    public const VIEW = 'COMPANY_VIEW';
    public const MANAGE = 'COMPANY_MANAGE';
    public const MEMBERS = 'COMPANY_MEMBERS';
    public const API_TOKENS = 'COMPANY_API_TOKENS';

    private function __construct()
    {

    }
}
