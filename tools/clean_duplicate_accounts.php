<?php
/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions/functions.inc");

$dependsField = 'dependentOnID';
$do_update = false;


function find_duplicates()
{
    $sql = <<<SQL
        SELECT
            *
        FROM
            `Members` AS base
        WHERE
            (
            SELECT
                COUNT(AccountID) AS COUNT
            FROM
                `Members`
            WHERE
                Email = base.Email
        ) > 1
        ORDER BY
            `base`.`Email` ASC;
SQL;
    $sth = \DB::run($sql);
    $data = $sth->fetchAll();
    if (empty($data)) {
        return null;
    }

    $result = [];
    foreach ($data as $entry) {
        $email = strtolower($entry['Email']);
        if (array_key_exists($email, $result)) {
            $result[$email][$entry['AccountID']] = $entry;
        } else {
            $result[$email] = [];
            $result[$email][$entry['AccountID']] = $entry;
        }
    }

    return $result;

}


function score_entry(/*.array.*/$entry)
{
    $score = 0;
    if (!empty($entry['Login'])) {
        $score += 1;
    }
    if (!empty($entry['AddressLine1'])) {
        $score += 1;
    }

    return $score;

}


function find_prime_account(/*.array.*/$entries)
{
    if (count($entries) < 2) {
        return array_keys($entries)[0];
    }

    $scores = [];
    foreach ($entries as $key => $entry) {
        $scores[$key] = score_entry($entry);
    }
    $top = array_keys($scores, max($scores));

    return min($top);

}


function cleanup_duplicates(/*.array.*/$entries, /*.int.*/ $prime)
{
    global $dependsField, $do_update;

    print("Base accountID ".$prime."(".$entries[$prime]['Email'].") dependent accounts: [");

    foreach ($entries as $id => $entry) {
        if ($id == $prime) {
            continue;
        }
        print($id.' ');
        if ($do_update) {
            $sql = <<<SQL
    UPDATE `Members`
    SET
        Email = NULL,
        $dependsField = $prime
    WHERE
        AccountID = $id
SQL;
            \DB::run($sql);
            $sql = <<<SQL
    DELETE FROM `Authentication`
    WHERE AccountID = $id
SQL;
            \DB::run($sql);
        }
    }
    print("]\n");

}


if (array_key_exists('update', $_GET) &&
    intval($_GET['update']) == 1) {
    $do_update = true;
}

print("<pre><======== Begin duplicate cleanup ");
if (!$do_update) {
    print("(DRYRUN)");
}
print(" ========>\n");
$dups = find_duplicates();
print("Cleaning ".count($dups)." entries\n");
foreach ($dups as $dup) {
    $acc = find_prime_account($dup);
    cleanup_duplicates($dup, $acc);
}
print("<======== End duplicate cleanup ========></pre>\n");
