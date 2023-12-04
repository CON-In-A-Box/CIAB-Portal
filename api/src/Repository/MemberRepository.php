<?php declare(strict_types=1);

namespace App\Repository;

use Atlas\Query\Select;

class MemberRepository
{

    protected $db;


    public function __construct($db)
    {
        $this->db = $db;

    }


    public function findMembersByIds($accountIds)
    {
        $select = Select::new($this->db);
        $select->columns(
            'AccountID',
            'FirstName as LegalFirstName',
            'MiddleName',
            'LastName as LegalLastName',
            'Suffix',
            'Email',
            'Email2',
            'Email3',
            'Phone',
            'Phone2',
            'AddressLine1',
            'AddressLine2',
            'AddressCity',
            'AddressState',
            'AddressZipCode',
            'AddressZipCodeSuffix',
            'AddressCountry',
            'AddressProvince',
            'PreferredFirstName',
            'PreferredLastName',
            'Deceased',
            'DoNotContact',
            'EmailOptOut',
            'Birthdate',
            'Gender',
            'DisplayPhone',
            'dependentOnID as DependentOnID',
            'Pronouns',
            '(CASE WHEN PreferredFirstName IS NOT NULL THEN PreferredFirstName ELSE FirstName END) as FirstName',
            '(CASE WHEN PreferredLastName IS NOT NULL THEN PreferredLastName ELSE LastName END) as LastName',
            '(SELECT GROUP_CONCAT(AccountID SEPARATOR \', \') FROM Members sqMembers WHERE sqMembers.Email = members.Email AND NOT sqMembers.AccountID = members.AccountID) as Duplicates'
        )
            ->from('Members as members')
            ->where('AccountID IN ', $accountIds);

        return $select->fetchAll();

    }


    /* End MemberRepository */
}
