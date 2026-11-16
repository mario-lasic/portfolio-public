# Deployment and maintenance

## Status and release gate

This is a future deployment runbook, prepared on 9 November 2026. No Railway deployment has been
activated or verified. Finish the separate showcase project, add its real content and screenshots to
the portfolio, and verify the resulting version before publishing. Hosting configuration changes belong
to the later deployment branch; this document does not imply that they already exist in the code.

The planned host is Railway Hobby, one application instance, without buying a domain initially.
Recheck pricing, available features, resource limits and account-wide usage at deployment time. The
previously accepted resource budget is a 10 USD cap with an earlier usage warning; confirm what that cap
covers before activating billing. Do not treat this document as a current price quote.

## Production prerequisites

- A reviewed commit on the chosen deployment branch, with tests and the Vite production build passing.
- A compatible PHP runtime, required extensions, Node build tooling and committed lock files.
- A public HTTPS address, a persistent storage plan and a proven startup command for the chosen builder.
- A functioning Brevo HTTPS API mail transport and verified sender. It is not implemented in this
  repository yet: the current mail configuration has no Brevo transport or API credential mapping.
- Actual receipt of a contact message, including a correct Reply-To, before calling the public release complete.

Railway documents that SMTP is unavailable on Hobby; use the planned HTTPS API rather than SMTP.
See [outbound networking](https://docs.railway.com/networking/outbound-networking). Laravel supports
[additional Symfony mail transports](https://laravel.com/framework/docs/13.x/mail#additional-symfony-transports),
and Brevo documents its [transactional email API](https://developers.brevo.com/docs/send-a-transactional-email).
Implement and test the adapter in the deployment phase before setting a production mailer name.

## Configuration inventory

Set runtime values in Railway's environment settings. The repository and documentation contain no keys.

| Variable | Intended production setting or purpose |
| --- | --- |
| APP_NAME | Portfolio display name |
| APP_ENV | production |
| APP_DEBUG | false |
| APP_KEY | Generate once, keep private and preserve across deployments |
| APP_URL | Final public HTTPS origin; drives canonical, sharing and sitemap URLs |
| APP_LOCALE / APP_FALLBACK_LOCALE | en |
| SESSION_DRIVER | file for the planned one-instance deployment |
| SESSION_SECURE_COOKIE | true after HTTPS/proxy handling is verified |
| SESSION_DOMAIN | Leave unset unless a specific domain requires it |
| CACHE_STORE | file, with persistent limiter state |
| QUEUE_CONNECTION | sync; the current controller sends synchronously |
| LOG_CHANNEL / LOG_LEVEL | stderr / warning, with Railway log capture verified |
| MAIL_MAILER | Name of the implemented HTTPS API transport, not log |
| MAIL_FROM_ADDRESS / MAIL_FROM_NAME | Verified application sender and its display name |
| MAIL_CONTACT_TO | Intended receiving inbox |
| Provider credential variable | Choose its exact name when implementing the API transport; inject only in hosting |

The local database defaults support sessions and cache. With file sessions/cache and synchronous mail,
current public request paths do not require a content database. Verify this on the deployment candidate;
if a technical database is retained instead, provision persistent storage and run its required migrations.
Do not blindly run database operations against an unconfigured or ephemeral SQLite file.

## Storage and startup

The planned single instance needs persistent `storage/framework/sessions`, `storage/framework/cache/data`
and cache lock storage. The current app derives these paths from Laravel's storage directory. Configure
and verify the volume mount and runtime paths during deployment; the repository has no volume/startup
configuration yet. A candidate is mounting the application storage directory, but confirm the builder's
absolute application path first.

A new volume can hide image-provided directories. At runtime, after the mount exists, create required
storage directories, establish correct ownership and regenerate compiled views. Keep `bootstrap/cache`
writable. Do not persist stale compiled Blade views as the only copy of current templates.

Static screenshots, favicon and sharing image live under `public` and ship with the release image.
They do not need an upload volume. Build assets belong to each release, not a shared mutable volume.
Never remove a volume as a routine release step. Check session and limiter persistence across restart
and redeploy. Use one instance until shared-state storage has been deliberately designed.

Railway volumes are mounted at runtime, not during build or pre-deploy work, and volume-backed services
have deployment constraints. Follow the current [volume documentation](https://docs.railway.com/volumes)
when selecting mount paths, initialization timing and backups.

## Build and release sequence

1. Review the candidate commit, run `npm run build` followed by `php artisan test` in the development/check environment.
2. Preserve the current successful deployment identifier, commit and environment-variable inventory.
   Back up persistent data before any storage or schema change.
3. Build from the committed lock files: `composer install --no-dev --prefer-dist --optimize-autoloader`,
   `npm ci`, then `npm run build`. Ensure the image contains `public/build` and its manifest, font assets,
   static files, Markdown content and application dependencies. Font generation needs network access.
4. Configure the selected Railway builder and a production web server with document root `public`, listening
   on the supplied PORT and an external interface. Do not use the development `php artisan serve` command
   as the production server. Record the exact tested start command in this runbook during deployment.
5. Provide runtime variables and mounted storage. Initialize writable directories after the mount is available.
   Generate APP_KEY only for the first installation, never on every startup.
6. Refresh release-specific configuration and views after runtime variables are available, for example
   `php artisan config:cache` and `php artisan view:cache`. If using `route:cache`, verify all routes on the
   deployed runtime. Do not bake a local APP_URL or local .env into production caches.
7. If the chosen deployment retains a technical database, apply reviewed migrations with `--force` only after
   connection, backup and compatibility have been checked. File-only session/cache deployment needs no such migration.
8. Verify the server, permissions, HTTPS redirects, trusted proxy behavior and secure cookies. Configure an
   appropriate health check and inspect startup logs. The site must not request Vite's development server.
9. Perform the public checks below before declaring success. Record the release commit and exact working commands.

Follow [Laravel deployment guidance](https://laravel.com/framework/docs/13.x/deployment) for the public
web root, writable directories, runtime configuration and disabled production debug output.

## Public acceptance checks

- Home, Projects, every real project URL, refresh, Back/Forward, mobile navigation and CV request link work.
- Unknown pages return 404 with the styled page and Back to home link.
- Initial HTML and Inertia navigation have one title, description and canonical link, with the public origin.
- Sitemap excludes placeholders; robots points to its public URL; sharing image and favicon are accessible.
- A real contact message reaches the configured inbox or spam folder, with the expected sender and Reply-To.
  An API acceptance response alone is not proof of inbox delivery.
- Validation, transport/network failure and limiting retain input and do not show false success.
- A restart/redeploy preserves the intended session and limiter state. No secrets or debug details appear publicly.
- Review logs and resource usage, then confirm the agreed spending controls.

## Recovering a failed release

1. Identify whether the failure is code, configuration, mail delivery or storage. Record the failing commit
   and deployment ID. Prevent repeated automatic deployment of the same known-bad change.
2. In Railway, select an available known-good deployment and use Rollback, then verify the resulting image
   and variables. Availability depends on retained deployment images; see
   [deployment actions](https://docs.railway.com/deployments/deployment-actions).
3. If that deployment is unavailable, rebuild the known-good commit through Railway's supported workflow.
   The user controls Git changes; record any corrective commit or revert on the tracked branch so later
   automatic deployment cannot reintroduce the failure.
4. A code rollback does not restore volume contents or reverse database changes. Restore data only from a
   deliberate backup recovery plan. Confirm old code remains compatible with current data before switching.
5. Preserve APP_KEY, sender setup and required environment values. Recheck runtime configuration caches,
   storage permissions, routes, CV and one real contact message after recovery.
6. Record the cause, recovered version and any follow-up fix. Keep backup restoration and release rollback
   distinct; do not clear all session/cache state as an automatic recovery step.

## Maintaining content

- Add a project following the README instructions. Use truthful status and working source/demo links;
  include screenshot dimensions, alt text and captions. Replace Coming soon when the showcase is ready.
- Replace the public CV at the existing path and update its text source in `docs/cv.md`. Review the PDF
  for contact privacy, language, dates and alignment with the site before committing it.
- Update shared contact addresses consistently in content and mail configuration. Visitor email remains Reply-To.
- For each change, run relevant tests/build and review the rendered pages before the user commits and merges.
- Review Railway/Brevo account usage, delivery failures and error logs periodically. Retain logs only as needed;
  contact messages contain visitor-provided personal information. Never publish logs or provider keys.
- Recheck this future runbook against actual platform settings when deployment begins and replace provisional
  runtime, volume and provider details with the tested final configuration.
