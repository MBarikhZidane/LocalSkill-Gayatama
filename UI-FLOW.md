# LOCALSKILL: screen and click flow

This guide describes the HTML that exists now. Start with `index.html`. A link to another filename opens another page; a `#section` link scrolls within a page. Query parameters choose a filter or service. All paths below are relative to the project root.

The UI uses one student account for both booking and offering skills. The only categories are **Design**, **Peer tutoring**, **Presentation slides design**, **Graduation and event photography**, **Graphic design**, and **Photocopy services**.

## 1. First visit: `index.html`

The landing page introduces LOCALSKILL, shows sample skills and the six allowed categories, explains the order timeline, and invites students to join. It is the main public entry point, not the student dashboard.

| Control / location | Click result | What appears |
| --- | --- | --- |
| LOCALSKILL logo | Top of `index.html` | Landing hero |
| Search field + **Find talent** | `explore.html?q=<encoded search>`; blank search opens `explore.html` | Matching sample listings |
| Header **Explore skills** | `index.html#services` | Six category panels |
| **Explore all skills**, **Explore services**, or footer **Explore skills** | `explore.html` | All six sample listings |
| A category panel | `explore.html?category=<category key>` | Listings in that category; keys are in section 3 |
| **How it works** or **Discover our approach** | `index.html#how` | Find, Match, Book, Complete, Review explanation |
| **Our impact** | `index.html#impact` | Introductory copy and sample platform metrics |
| Top strip **Become a provider** | `index.html#talent` | Provider invitation and sample profile |
| **Log in** | `login.html` | Google sign-in option and email login preview |
| **Join the network**, **Join LOCALSKILL**, provider-section/footer **Become a provider** | `register.html` | Google account option and email registration preview |
| Footer **Campus hub** | `layout-public.html` | Compact alternative public screen |
| Footer **Student workspace** | `layout-dashboard.html` | Combined booking/offering demo |
| Footer **My orders** / **My services** | `layout-dashboard.html#orders` / `layout-dashboard.html#services` | Corresponding workspace section |
| Footer **Admin demo** | `layout-admin.html` | Sample moderation queue |
| Mobile menu icon | Stays on `index.html`; opens/closes navigation | Explore skills, How it works, Our impact links |

The hero skill cards, scrolling category ticker, statistics, and provider profile are display elements; they do not open detail pages. Category panels and explore links are the routes into discovery. The footer is shorter on narrow screens, so some desktop footer links are hidden.

### Photocopy explanation: `index.html#photocopy`

After the category panels, the **No printer at home? We can help.** section explains printing digital files and photocopying originals, including essays, journals, thesis pages, and study materials. It tells customers what to include in their brief and to agree on price, document handover, and campus pickup.

- Footer **About photocopy services** scrolls to this section.
- **Find photocopy services** opens `explore.html?category=photocopy-services`, showing the photocopy sample card.
- That card's title or **View skill** opens `service.html?id=photocopy-01`. The sample package is 20 A4 black-and-white printed sides for Rp10.000.
- The customer enters a preferred date and brief, then clicks **Preview booking request**. Existing validation and inline demo feedback apply; no booking, document upload, file transfer, or pickup reservation occurs. Other quantities and colour printing need a provider agreement.

## 2. Shared public navigation and `layout-public.html`

All HTML pages except `index.html` share this navigation:

| Control | Destination |
| --- | --- |
| **Skip to content** (keyboard focus) | Current page's `#content` main area |
| LOCALSKILL logo | `index.html` |
| **Explore** | `explore.html` |
| **Workspace** | `layout-dashboard.html` |
| **Log in** | `login.html` |
| Footer five-step timeline | `index.html#how` |

`layout-public.html` is a runnable compact public layout, not an automatically included wrapper. Its **Explore skills** button opens `explore.html`; **Join LOCALSKILL** opens `register.html`. Its five numbered process cards are informational. A backend developer can extract the shared markup into template partials.

## 3. Find a skill: `explore.html`

The initial screen has a search input, category select, **Find talent**, a result count, and six cards. Every card shows provider initials/name, campus proximity, rating/job count, scope, and Rupiah price.

| Selected category | URL after category search | Card title | Detail destination |
| --- | --- | --- | --- |
| Design | `explore.html?category=design` | Design consultation | `service.html?id=design-01` |
| Peer tutoring | `explore.html?category=peer-tutoring` | Peer tutoring | `service.html?id=tutoring-01` |
| Presentation slides design | `explore.html?category=presentation-slides-design` | Presentation slides design | `service.html?id=slides-01` |
| Graduation and event photography | `explore.html?category=graduation-event-photography` | Graduation and event photography | `service.html?id=photography-01` |
| Graphic design | `explore.html?category=graphic-design` | Graphic design | `service.html?id=graphic-01` |
| Photocopy services | `explore.html?category=photocopy-services` | Photocopy services | `service.html?id=photocopy-01` |

1. Enter a phrase and/or choose a category, then click **Find talent** (or press Enter in search). The GET form reloads `explore.html` with `q` and `category`; selecting a category alone does not submit. Empty fields may remain in the URL.
2. `assets/js/app.js` filters the sample cards and updates the count. Text search is case-insensitive and matches displayed card text. Text and category conditions must both match.
3. Click either a card title or **View skill** to open that card's detail URL above. The rest of the card is not a link.
4. **Clear filters** returns to `explore.html` without query parameters.
5. If nothing matches, the screen shows **No skills found** and **Show all skills**. That button returns to `explore.html`. Unknown category keys also produce no results.

Without JavaScript, the static six cards remain visible and a notice explains that filtering requires JavaScript. No search is sent to a backend yet.

## 4. Match and request a booking: `service.html`

The URL's `id` selects the title, package description, provider, location, rating, price, and hidden `service_id` booking field. With no ID, the page shows Design consultation. An unknown ID shows **This skill is unavailable** and a **Browse other skills** link to `explore.html`; the booking area is hidden.

| Action | Current UI result |
| --- | --- |
| **Back to skills** | Opens unfiltered `explore.html`; previous filters are not retained |
| Select preferred date | Fills `scheduled_date`; dates before the local current date are disallowed |
| Write your brief | Fills `brief`; 20–2000 characters are required |
| **Preview booking request** with invalid fields | Browser validation identifies the invalid field; stays on this page |
| **Preview booking request** with valid fields | Inline feedback says the preview was validated and nothing was sent or saved; stays on this page |

The timeline highlights **Match**. It does not advance on preview submission. There is no payment page, real booking, or login redirect in this static preview. Without JavaScript, the default Design consultation sample is shown with a notice, and preview submission is disabled.

**Backend behavior to implement:** require a session before accepting POST `/bookings`, resolve the selected service and price on the server, save the booking, then show it in `layout-dashboard.html#orders`. Preserve a safe return destination if authentication interrupts booking. This behavior is planned, not currently simulated.

## 5. Sign in / sign up: `login.html` and `register.html`

Both screens begin with **Continue with Google**, followed by **or use email**. Both paths refer to the same dual-role account; signing up as a separate buyer or seller is not an option.

### Google path

In the unconfigured local preview, the Google button is disabled and the message says Google sign-in is not connected. It does not pretend to authenticate or navigate to the workspace. `assets/js/google-auth.js` contains the single public client-ID setting; `BACKEND.md` explains setup.

Once the Google client ID, registered origin/redirect URI, and backend callback are ready:

1. Open either `login.html` or `register.html` over the configured web origin. The official Google button replaces the disabled preview button.
2. Click **Continue with Google**. Google's account-selection/sign-in interface appears; it is hosted by Google and has no local HTML filename. The exact screens depend on the user's Google session.
3. Choose a Google account and finish Google's prompts. Redirect UX sends the returned credential to POST `/auth/google/callback`. This is a backend route, not an HTML page.
4. The backend verifies the credential, finds or creates the account, and creates a session. The intended success destination is `layout-dashboard.html` via an HTTP 303 redirect.
5. Cancellation does not create a LOCALSKILL session. The user can return to either account page and retry. A Google script load error shows an inline retry message; callback verification failures should be rendered by the backend on `login.html`.

Google authentication and campus verification are separate. A future onboarding UI must collect missing university details before verified-student privileges are granted; that UI has not been built. Callback handling, account linking, sessions, and redirects are also backend work, not functionality supplied by these static files.

### Email path and secondary links

| Screen / control | Current result |
| --- | --- |
| `login.html`: student email, password, **Preview login** | Required/email validation; valid input shows inline demo feedback and clears the password; no login occurs |
| `register.html`: full name, university, student email, password, **Preview registration** | Required/email validation and minimum 8-character password; valid input shows demo feedback and clears the password; no account is saved |
| `login.html`: **Create an account** | Opens `register.html` |
| `register.html`: **Log in** | Opens `login.html` |
| Either screen: **View demo workspace** | Opens `layout-dashboard.html` without authentication; explicitly a demo shortcut |

The Google button is outside the email forms: users do not need to fill email/password fields to use it. The email POST routes are `/auth/login` and `/auth/register`; their current `data-demo` interceptors prevent network submission.

## 6. Student workspace: `layout-dashboard.html`

This screen combines sample booking statistics, **My orders**, **My services**, and **Offer a skill**. Jamie is sample data, not a signed-in identity. Header navigation remains public in the prototype.

| Control | Result |
| --- | --- |
| **Find a skill** | Opens `explore.html` |
| **My orders** | Scrolls to `#orders`, showing a Design consultation booking awaiting provider confirmation |
| Order **View skill** | Opens `service.html?id=design-01`; this is a service page, not a separate order-details screen |
| **My services** | Scrolls to `#services`, showing Jamie's Presentation slides design listing |
| **Offer a skill** | Scrolls to `#offer`, revealing the listing form |
| **Preview listing** | Validates title, allowed category, campus zone, integer price (minimum Rp1.000, increments of Rp1.000), and description; shows inline demo feedback on success |

Listing preview stays on this screen and does not add a card, change counters, or publish anything. The future server target is POST `/services`. The published sample card has no edit/delete action.

**Complete and Review:** both stages are shown in the explanatory timeline, but there are no completion, delivery, or review forms yet. Backend and UI work is still needed to confirm delivery and permit one eligible review after completion. Do not interpret the diagram as implemented order transitions.

## 7. Admin moderation: `layout-admin.html`

Open this file directly or use the desktop homepage footer's **Admin demo** link. The page shows sample counts and one pending Graduation and event photography listing. It is publicly accessible sample HTML; a live backend must require admin authorization.

1. Select **Approve listing**, **Request changes**, or **Reject listing** from Decision.
2. Enter the required Review note.
3. Click **Preview decision**. Missing values trigger browser validation. Valid input shows inline demo feedback without changing the listing or count.
4. The future POST route is `/admin/services/pending-01/moderation`; replace the sample ID with the actual record ID in a server loop.

There is no separate moderation-result screen, provider notification, or automatic publication in the preview. Shared navigation returns to the homepage, explorer, workspace, or login page.

## 8. Files responsible for behavior

| File | Responsibility |
| --- | --- |
| `index.html` inline script | Homepage search redirect, mobile menu, reveal observer |
| `assets/js/app.js` | Six sample service records, search/category filtering, detail selection, date minimum, local form previews, Lucide icons |
| `assets/js/google-auth.js` | Google client configuration, official button loading, redirect callback URL, availability messages |
| `BACKEND.md` | Routes, field names, category allowlist, Google setup and server responsibilities |
| `.github/copilot-instructions.md` | Project stack, permitted categories, account model, and UI rules for future changes |

All pages are standalone HTML, with Tailwind/DaisyUI CDNs on the connected pages and the preserved custom styling on the homepage. No build step is required. Internet is needed for CDN styles/icons; live Google access additionally requires the configured web origin and backend.
