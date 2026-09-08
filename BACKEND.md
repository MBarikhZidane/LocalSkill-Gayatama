# LOCALSKILL HTML handoff

Open `index.html` directly. Tailwind CDN, DaisyUI v4, and Lucide require internet; no build or npm dependencies are needed. The original homepage styling is preserved. The connected pages use Tailwind utilities and DaisyUI components.

## Pages

| File | Purpose |
| --- | --- |
| `index.html` | Existing landing page, search, and entry links |
| `explore.html` | Six allowed categories; search example: `?q=slides&category=presentation-slides-design` |
| `service.html` | Skill and booking form selected by `?id=`; IDs listed below |
| `login.html`, `register.html` | Google account access plus existing email form previews; one account for booking and offering |
| `layout-public.html` | Runnable public layout for conversion to a server template |
| `layout-dashboard.html` | Combined student workspace, sample orders, and service form |
| `layout-admin.html` | Directly accessible demo moderation layout; not an authenticated admin area |

Read `UI-FLOW.md` for each screen, button destination, and the distinction between current preview behavior and future server behavior.

## Allowed skill categories

Use this exact allowlist in service creation, search, moderation, and server validation. Do not accept arbitrary new categories. Titles and descriptions must also stay within these services; a category field alone does not enforce the content scope.

| Category key | Display label | Sample service ID | Package price (IDR) |
| --- | --- | --- | --- |
| `design` | Design | `design-01` (Design consultation) | 150000 |
| `peer-tutoring` | Peer tutoring | `tutoring-01` | 75000 |
| `presentation-slides-design` | Presentation slides design | `slides-01` | 50000 |
| `graduation-event-photography` | Graduation and event photography | `photography-01` | 200000 |
| `graphic-design` | Graphic design | `graphic-01` | 100000 |
| `photocopy-services` | Photocopy services | `photocopy-01` | 10000 |

Design covers consultation, mood boards, and visual direction. Graphic design covers finished posters and graphics; presentation slides have their own category. Graduation and event photography is one combined category. Unknown category queries currently show zero results, and unknown service IDs show an unavailable message. There are no aliases for retired categories or IDs.

## Photocopy services

This category covers printing digital documents and photocopying physical originals for customers without a printer: essays, journals, thesis pages, and study materials. The sample package is **20 A4 black-and-white printed sides for Rp10.000**; it is illustrative pricing, not a per-page rate or a quote for every request.

Use the existing `service_id`, `scheduled_date`, and `brief` booking fields. The brief should specify print/copy, page and copy counts, paper size, colour preference, single/double-sided printing, deadline, and pickup arrangements. Confirm the actual scope and price with the provider before booking; other quantities or colour printing require agreement. No new endpoint is needed. Document uploads, file transfer, and pickup scheduling are not implemented; the explanatory section does not imply these features exist.

The homepage explanation is `index.html#photocopy`, its CTA opens `explore.html?category=photocopy-services`, and the sample detail page is `service.html?id=photocopy-01`.

## Google sign-in and sign-up

Both account pages load `assets/js/google-auth.js`. This integrates the official Google Identity Services button with redirect UX. It is separate from `data-demo` forms: once configured, it performs a real Google flow. With the client ID empty, or when opened through `file:`, it keeps the disabled preview button and does not contact Google. Email forms remain demo-only.

1. Implement POST `/auth/google/callback` on your chosen backend before enabling the button. There is no backend runtime or session implementation in this HTML repository.
2. Create a Google OAuth client of type **Web application**. Register the site's exact origin and the exact callback URL as an authorized redirect URI. Use HTTPS for deployment or HTTP localhost for development; direct file previews cannot authenticate. Follow [Google's client setup guide](https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid).
3. Set `GOOGLE_CLIENT_ID` in `assets/js/google-auth.js` to the public client ID. No client secret belongs in HTML or JavaScript. The callback URL is `/auth/google/callback` on the current origin; change it in the script and Google configuration together if your routes differ.
4. Google posts `credential` and `g_csrf_token` to the callback. Require matching CSRF cookie/body values, then use a supported verification library to check the ID token signature, audience, issuer, and expiry. Identify Google accounts by `sub`, not email. See [Google's token verification guide](https://developers.google.com/identity/gsi/web/guides/verify-google-id-token).
5. Look up the Google identity; sign in an existing linked user or create one dual-role account for a new identity. If an email account already exists, require proof of ownership before linking it. Create a secure server session and redirect with HTTP 303 to `layout-dashboard.html`. Never treat client-side profile data as authentication.
6. On failure, render a readable error on `login.html` and leave the user unauthenticated. Google does not supply university enrollment verification: collect missing campus details and verify student eligibility in a backend onboarding step before showing verified badges or enabling restricted actions. This additional onboarding screen is not implemented here.

The rendered button, Google account chooser, and redirect behavior follow the [Google button reference](https://developers.google.com/identity/gsi/web/reference/js-reference). This project does not request Drive, Calendar, or other Google account permissions.

## Server integration

HTML is intentionally rendered in the files, with compact complete elements and named form fields. Extract the repeated head, header, footer, and main content into your framework's partials. Replace sample cards/orders with server-side loops; `data-service-id` and `data-order-id` identify records. `assets/js/app.js` provides optional query filtering, sample detail selection, and demo feedback. Replace its catalog/detail mapping and filtering with server queries when integrating.

These are proposed form routes, not implemented APIs:

| Method / action | Fields |
| --- | --- |
| GET `explore.html` | `q`, `category` |
| POST `/auth/login` | `email`, `password` |
| POST `/auth/register` | `name`, `university`, `email`, `password` |
| POST `/auth/google/callback` | Google-issued `credential`, `g_csrf_token`; verify corresponding cookie |
| POST `/bookings` | `service_id`, `scheduled_date`, `brief` |
| POST `/services` | `title`, `category`, `campus_zone`, `price_idr`, `description` |
| POST `/admin/services/{id}/moderation` | `service_id`, `decision`, `reason` |

All locally authored POST forms have `data-demo`. Their submit buttons start disabled and are enabled only after the shared script installs a submit interceptor. They do not send or persist data. Remove `data-demo` and the disabled button attribute together only when real handlers are connected; update button labels and demo notices then. A noscript notice explains why preview actions require JavaScript. The configured Google button is handled separately by Google's library and must not use this interceptor.

Validate all fields on the server, enforce authenticated ownership/admin permissions, add CSRF protection, and escape rendered values. Derive booking price and provider from the service record, never from the client. Store integer IDR amounts; format for display with the Indonesian locale. Use the campus timezone for dates. Student verification and displayed trust metrics must come from real records.

Order lifecycle: Find → Match → Book → Complete → Review. Find/Match are discovery stages; the backend should separately track booking confirmation, completion, cancellation, and review eligibility. A review is allowed only after a completed order. Both buying and selling belong to the same user account.
