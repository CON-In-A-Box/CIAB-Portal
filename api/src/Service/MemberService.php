<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\MemberRepository;

class MemberService
{

    protected $memberRepository;


    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;

    }


    public function getMembersByIds($memberIds)
    {
        $result = $this->memberRepository->findMembersByIds($memberIds);

        $formatted = [];
        foreach ($result as $value) {
            $output = [];
            $output["id"] = $value["AccountID"];
            $output["legal_first_name"] = $value["LegalFirstName"];
            $output["legal_last_name"] = $value["LegalLastName"];
            $output["middle_name"] = $value["MiddleName"];
            $output["suffix"] = $value["Suffix"];
            $output["email"] = $value["Email"];
            $output["email2"] = $value["Email2"];
            $output["email3"] = $value["Email3"];
            $output["phone"] = $value["Phone"];
            $output["phone2"] = $value["Phone2"];
            $output["address_line1"] = $value["AddressLine1"];
            $output["address_line2"] = $value["AddressLine2"];
            $output["city"] = $value["AddressCity"];
            $output["state"] = $value["AddressState"];
            $output["zip_code"] = $value["AddressZipCode"];
            $output["zip_plus4"] = $value["AddressZipCodeSuffix"];
            $output["country"] = $value["AddressCountry"];
            $output["province"] = $value["AddressProvince"];
            $output["preferred_first_name"] = $value["PreferredFirstName"];
            $output["preferred_last_name"] = $value["PreferredLastName"];
            $output["deceased"] = $value["Deceased"];
            $output["do_not_contact"] = $value["DoNotContact"];
            $output["email_optout"] = $value["EmailOptOut"];
            $output["birthdate"] = $value["Birthdate"];
            $output["gender"] = $value["Gender"];
            $output["concom_display_phone"] = $value["DisplayPhone"];
            $output["dependent_on"] = $value["DependentOnID"];
            $output["pronouns"] = $value["Pronouns"];
            $output["first_name"] = $value["FirstName"];
            $output["last_name"] = $value["LastName"];
            $output["duplicates"] = $value["Duplicates"];

            $formatted[$output["id"]] = $output;
        }

        return $formatted;

    }


    /* End MemberService */
}
