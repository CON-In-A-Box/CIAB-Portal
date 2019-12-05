# Configuration Options

The following database related options can only exist in environment or `.env` file. They are not configurable via the system itself.

* **DBHOST**              `Where the DB Runs`
* **DBNAME**              `The Database Name`
* **DBPASS**              `Database user's password in *PLAIN TEXT*`
* **DBUSER**              `The Database Username`
* **DB_BACKEND**          `MyPDO module used (mysqpdo.inc)`

All of these operational configuration options may exist in environment, `.env` file or the configuration table in the database. If present in multiple locations  then Environment trumps `.env` file which trumps the database. Only the database values are configurable via the system itself. The reason for this is to allow for containerized implementations to be able to set values here externally.

* **ADMINACCOUNTS**       `Comma separated list of account IDs that are admins`
* **ADMINEMAIL**          `Email address for admin e-mails, fallback for other email`
* **CONCOMHOURS**         `Volunteer hours awarded to ConCom`
* **CONHOST**             `Name of the hosting organization and thus the web site`
* **CONSITENAME**         `Name of this site in the hosting organization`
* **DISABLEDMODULES**     `Modules disabled for this instance`
* **DUMPSECURE**          `??? Unknown related to deep links`
* **EMAIL_BACKEND**       `Mail module used (mail.inc, null.inc, sendgrid.inc)`
* **FEEDBACK_EMAIL**      `Email address for feedback e-mails`
* **G_CLIENT_SECRET**     `Google drive client secret`
* **G_ROOTFOLDER**        `Google drive client root folder`
* **HELP_EMAIL**          `Help email address`
* **MAXLOGINFAIL**        `Maximum logins failures before account is locked`
* **NEONID**              `Id for Neon CRM`
* **NEONKEY**             `Key for Neon CRM`
* **NEONTRIAL**           `Is this a Neon trial account`
* **NOREPLY_EMAIL**       `Email address for the no-reply email address`
* **PASSWORDEXPIRE**      `Duration between password expiration`
* **PASSWORDRESET**       `How long temporary passwords are valid`
* **SECURITY_EMAIL**      `Email address for security email`
* **SENDGRID_API_KEY**    `API key for sendgrid`
* **SENDGRID_MAIL_FROM**  `Sendgrid configuration`
* **TIMEZONE**            `The timezone for the convention`

There are a few options that appear in the configuration tables that should not be manually set by the administrator and instead there are other facilities that these options control and the settings should be made via those facilities.

* **DBSchemaMD5**     `Value for tracking the Database state`
* **DBSchemaVersion** `Value for tracking the Database upgrades`
* **G_CLIENT_CRED**   `Google drive client credential`
* **NEON_HOOK_NAME**  `Auto-generated name for the Neon Hooks when generated`
* **NEON_HOOK_URL**   `Base url for the presently installed Neon hooks`
