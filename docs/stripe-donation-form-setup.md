# Stripe Donation Form Setup (Issue #11)

## Status
Donation form built and tested locally end-to-end. Stripe live/test API key
connection is blocked pending real credentials for Stephanie's account
(tracked in issue #26).

## What was done
- Created a GiveWP Campaign: "Stephanie Goertz Municipal Campaign Donations"
- Configured donation form (Form ID #8) with fields:
  - First name, Last name (required/optional as GiveWP default)
  - Email Address (required)
  - Billing Address: Country, Address Line 1, Address Line 2, City,
    State/Province, Postal Code (required)
- Created a "Donate" page (page_id=11) with the form embedded, matching
  URL pattern of reference sites (leannecaron.ca/donate, gpo.ca/donate)
- Full test donation completed successfully using GiveWP's Test Donation
  gateway: Payment Status "Completed", $250.00 test transaction, donor
  name/email/address all captured correctly

## Local environment
- WordPress multisite converted locally (was previously single-site only)
- `.env` updated with MULTISITE, SUBDOMAIN_INSTALL, DOMAIN_CURRENT_SITE
  config to match live server setup

## Remaining work
- Connect real Stripe account (test + live keys) once available from
  William/Maria (see issue #26)
- Migrate form/campaign config to Stephanie's live site once Stripe is
  connected
