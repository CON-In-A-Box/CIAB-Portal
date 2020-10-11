# Windows Setup

1.  *Optional*: Install the new Windows Terminal from the Microsoft Store

2.  Install [Windows Subsystem for Linux 2](https://docs.microsoft.com/en-us/windows/wsl/install-win10)
    NOTE: All our scripts and stuff assume a Linux shell is available. Nothing we do is compatible with a native Windows environment. We have no idea if WAMP, for example, will work.

3.  Install Ubuntu via the Microsoft Store when you get to Step 6 in the
    document above (It doesn't HAVE to be Ubuntu, but that's what Mikey knows best and can help you the most with.)

4.  Install [Docker Desktop](https://docs.docker.com/docker-for-windows/install/#:~:text=Install%20Docker%20Desktop%20on%20Windows%20%F0%9F%94%97%201%20Double-click,complete%20dialog%20and%20launch%20the%20Docker%20Desktop%20application) NOTE: Since you're running under a Linux environment anyway, you COULD run this without docker. Running it with docker, however, allows us to easily encapsulate all the dependencies! The docker containers will "mount" your source code in such a way that, when you make changes, you'll see those changes when you refresh pages in the browser.

    a.  When prompted, you do want to install the components for
        integrating with WSL2
        
    b.  The install does not require a reboot, but does require you to
        log out and log back in

5.  Configure Docker Desktop to use Ubuntu with WSL2:

    a.  On the task bar, over on the right side, click on the arrow that
        pops up hidden icons and select the Docker whale. A single click
        should bring the Docker App up\
        ![](media/media/image1.png){width="2.864983595800525in"
        height="3.229617235345582in"}

    b.  Select the Gear icon in the top right of the Docker window

    c.  Select Resources -\> WSL
        INTEGRATION![](media/media/image2.png){width="6.5in"
        height="4.105555555555555in"}

    d.  Turn 'Ubuntu' on.

    e.  Click 'Apply & Restart'

6.  To verify this all worked

    a.  Start a WSL2 session. You can do this by starting any PowerShell
        or CMD session and typing `wsl`, or you can select Ubuntu from
        the Start menu. I recommend starting a Windows Terminal session
        and typing `wsl`, personally.

    b.  Run `docker ps`. It should look like this\
        ![](media/media/image3.png){width="6.5in"
        height="0.22430555555555556in"}

7.  Optional: Install Visual Studio Code. NOTE: If you're a Unixhead who likes vi, you can also just run vi in a WSL window. You do need to be in your WSL home directory, however, and not in a Windows-hosted directory, to check out and run code. VSCode offers nice tools for this, as you'll see in a minute.

    a.  Once installed, select the extensions icon from the left hand
        side, and search for "Remote -- WSL". All the other extensions
        you need are probably installed.

    b.  In the bottom left corner, you should see a little green icon
        that looks like this\
        ![](media/media/image4.png){width="1.0939031058617672in"
        height="0.7605227471566054in"}

    c.  Click that, and select "Remote-WSL: New Window"

    d.  The bottom green icon in the new window should say "WSL:
        Ubuntu". You can verify you're in the Ubuntu universe by
        selecting "Terminal -\> New Terminal", you should see a Bash
        prompt. You can use this going forward instead of Windows Terminal if you prefer. It doesn't really matter.

8.  Generate an SSH key

    a.  However you choose to do it, get yourself to an Ubuntu bash
        prompt. Make sure you're in your Ubuntu home directory by typing
        `cd`. Without an argument, `cd` in Linux will always go to
        your home.

    b.  Type `ssh-keygen -t ed25519`. Hit return through all the
        prompts (unless you want to call it something different or
        assign it a password).

    c.  When it's done, you'll have a directory called `.ssh`, and it
        will have files named `id_ed25519` and `id_ed25519.pub`.

    d.  Navigate to Github in a web browser, log in if you aren't
        already, and select the menu under your profile picture (top
        right corner). Select Settings

    e.  Select SSH & GPG Keys

    f.  Select New SSH Key

    g.  Back at your prompt type `more .ssh/id_ed25519.pub`

    h.  Copy the line it outputs

    i.  Back to your browser, paste the key and give it a name, as
        prompted in the Github page

    j.  Click Add SSH Key to finish up.

9.  Check out the code. At a WSL bash prompt:

    a.  I like to isolate code projects in a `projects` subdirectory
        of my home directory, so all my examples assume that.

    b.  `mkdir projects` if you don't have one

    c.  `cd projects`

    d.  `git clone git@github.com:CON-In-A-Box/CIAB-Portal.git`

10. If you're using Visual Studio Code as recommended, open the project there

    a. If you haven't already, in the VSCode window that's already hooked up to "Remote - WSL", open a Terminal window (Terminal -> New Terminal)

    b. cd `~/projects/CIAB-Portal`

    c. `code .`
        You should now see a new Code window, and there should be a sidebar showing you all the files in the project. You can now close the earlier, empty Remote WSL window. This is your new home.

10. Start the server!

    a.  Make sure your current directory is `CIAB-Portal`

    b.  Type `touch .env`. That's the word touch, then a space, then
        `.env`, with the leading period. This is a file that can
        potentially contain environment variables we use to configure
        certain functionality. It can be empty, but it must be present.

    c.  Type `./docker_instance.sh up`. This will do a whole bunch of
        setup. Just sit back and watch it happen.

    d.  You'll know it's done when it says `ciab-portal_composer_1
        exited with code 0`

11. In a browser, navigate to localhost:8080

    a.  If everything so far has gone according to plan, you'll see some
        innocuous nerdtext about some database updates, after which, you
        can click Proceed

    b.  You should see the standard Portal login when you do so. There
        won't be any way to log in, yet, however!

    c.  Now, go read
        <https://github.com/CON-In-A-Box/CIAB-Portal/blob/master/FirstSetup.md>
        and run through items 4-7 to populate seed data!
        Here's an example of configure_system.php filled out
        ![](media/media/image5.png){width="6.5in" height="2.4472222222222224in"}
