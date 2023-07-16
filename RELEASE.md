# Release Process for CIAB
This is an in-progress process and documentation. Expect things to change and update here as the process is more clearly defined and implemented. 

This is the release process for Con-in-a-Box used by CONvergence. It can be the baseline for other conventions or user if so desired, or simply used as a roadmap example of how Con-in-a-Box is released on one site.

There 2 to main development branches that are important. '**main**' and '**production**'. 

* **main** is the development tip and should be considered latest and greatest, as well as unstable. Development change sets are committed here.
* **production** is the branch used to build production releases. It is branched from **main** at stable points.

When a release is being prepared the first step is to update the **production** branch to the latest **main**. Then a proper release is tagged from the **production** branch.

There are tools that are in process and will live in the tools directory that can be installed onto the production web server that will be triggered by web hooks to automatically update a given web server to a release when it is done. 

## Tools

There is a basic release script located in `tools/prepare_release.php` This tool will update the version.ini and tag the branch with an automated tag name. It is again appropriate for CONvergence but may need modification or generalization for other events. 

This tool is meant to be run on the **production** branch to do the proper tagging. It can take a `--dry-run` option to see what is happening without actually taking any action. 

