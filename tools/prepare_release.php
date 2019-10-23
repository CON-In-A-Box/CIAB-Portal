<?php

/*.
    require_module 'standard';
.*/

require_once __DIR__."/../functions/functions.inc";

$dry_run = true;


function getGitVersion()
{
    $dirty = false;
    $build = intval(exec('git rev-list HEAD|wc -l'));
    if ($build > 0) {
        $output = exec('git rev-parse --short HEAD', $data, $result);
        if ($result != 0) {
            $rev = "unknown";
        } else {
            $rev = $output;
        }
        exec('git rev-list -n 1 HEAD --not --remotes=origin', $data, $result);
        $dirty = ($data == 0);
        if (!$dirty) {
            exec(
                'git update-index --refresh --unmerged -q >/dev/null',
                $data,
                $result
            );
            $dirty = ($result != 0);
        }
        if (!$dirty) {
            exec(
                'git diff-index --ignore-submodules=untracked --quiet HEAD',
                $data,
                $result
            );
            $dirty = ($result != 0);
        }
    }
    return [$build, $dirty];

}


function updateVersion($gitver)
{
    global $BASEDIR, $dry_run;
    $version = parse_ini_file($BASEDIR."/version.ini");

    if ($gitver[0] != $version['build']) {
        $gitver[0] = $gitver[0] + 1;
        print "-- Updating build number from ".$version['build']." to ".$gitver[0]."\n";
        $version['build'] = $gitver[0];
        $output = <<<E
[Version]
major={$version['major']}
minor={$version['minor']}
build={$version['build']}
tag={$version['tag']}
E;
        if (!$dry_run) {
            @file_put_contents($BASEDIR."/version.ini", $output);
            exec("git add $BASEDIR/version.ini");
            exec('git commit -m "Update version.ini for '.$gitver[0].' build"');
        }
    }
    return $version;

}


function tagSource($version)
{
    global $dry_run;

    $tag = exec("git describe --exact-match --tags $(git log -n1 --pretty='%h') 2>/dev/null");

    if ($tag == "") {
        $newtag = "Release-v".$version['major'].".".$version['minor'].".".$version['build']."-CVG-".date("Y");
        print "-- Tagging release ".$newtag;
        if (!$dry_run) {
            exec("git tag -a \"$newtag\" -m \"Tagging Release $newtag\"");
        }
        print "\n";
        print "--  If you are happy with this tag, push it with:\n";
        print "       git push origin \"$newtag\"\n";
        print "--  Otherwise delete it *before it has been pushed*, with:\n";
        print "       git tag -d \"$newtag\"\n";
    }
    return $newtag;

}


function release($tag)
{
    $file = "/tmp/$tag.tar.gz";
    $root = exec("git rev-parse --show-toplevel");
    $cwd = @getcwd();
    @chdir($root);
    exec("git archive --format=tar.gz --prefix ciab/ --output $file HEAD");
    @chdir($cwd);
    print "Archive stored '$file'\n";

}


$dry_run = (in_array('--dry-run', $argv));

if ($dry_run) {
    print " ===============  DRY RUN ================= \n";
} else {
    print " ===============  Prepearing Release ================= \n";
}

$gitver = getGitVersion();
if (intval($gitver[1]) > 0) {
    print "** Failing to prepare release: Local tree is unclean\n";
    if (!$dry_run) {
        exit(1);
    }
}
$version = updateVersion($gitver);
$tag = tagSource($version);
print " ===============  Archiving ================= \n";
release($tag);
print " ===============  DONE ================= \n";
