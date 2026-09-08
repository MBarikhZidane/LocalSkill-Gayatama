# GitHub Copilot System Instructions: LOCALSKILL

## Project Context
LOCALSKILL is a campus-based, peer-to-peer skill-sharing marketplace designed for university students. It allows students to offer, discover, book, and exchange skills locally on campus.

## Tech Stack & Architecture Guidelines
- **Markup & Styling:** Standard HTML5 with Tailwind CSS (CDN-based) and DaisyUI (v4 CDN).
- **Icons:** Lucide Icons (`<i data-lucide="icon-name"></i>`).
- **No Heavy Build Tools:** Do NOT generate React, Vue, SASS, or Node.js/NPM dependencies unless explicitly requested. Keep everything CDN-compatible and runnable directly in the browser.
- **NO Bootstrap:** Never use Bootstrap classes (`container`, `row`, `col-lg-6`, `btn-primary`). Use Tailwind utilities (`flex`, `grid`, `bg-emerald-600`, `p-4`) and DaisyUI component classes (`btn`, `card`, `badge`, `modal`, `drawer`).

## Core Business & UX Rules
1. **Unified Dual-Role Model:** Every registered user has a single account that acts as BOTH a skill seeker (buyer) and a skill provider (seller). NEVER generate separate signup forms or hard role toggles (e.g., "Register as Buyer" vs "Register as Seller").
2. **5-Step Core Flow:** Always align service orders with the platform's core progress timeline:
   `Find` → `Match` → `Book` → `Complete` → `Review`
3. **Card & Item Displays:** Service listings must emphasize campus proximity and trust metrics:
   - Provider Avatar & Name
   - Proximity Badge (e.g., `📍 1.2 km away` or campus zone)
   - Rating Badge (e.g., `⭐ 4.9 (24 jobs)`)
   - Price Display in Indonesian Rupiah (e.g., `Rp150.000`)
4. **UI Style:** Modern, mobile-first, high-density on-demand app style (inspired by Gojek / Uber UI).

5. **Allowed Skills Only:** Limit all displayed listings, categories, examples, and offering forms to Design, Peer tutoring, Presentation slides design, Graduation and event photography, Graphic design, and Photocopy services. Use category keys `design`, `peer-tutoring`, `presentation-slides-design`, `graduation-event-photography`, `graphic-design`, and `photocopy-services`.
6. **Google Account Access:** Both login and registration support the same Google Identity Services flow and the same dual-role account. Never simulate authentication success in a static preview.

## Code Generation Requirements
- **Complete Snippets:** Always output full, fully formed HTML elements. NEVER use lazy placeholder comments like `<!-- rest of code here -->` or `<!-- TODO -->`.
- **Accessibility & Cleanliness:** Ensure proper standard HTML structure, clean indentation, and responsive breakpoint prefixes (`sm:`, `md:`, `lg:`).
- **Color Scheme:** Use `emerald-600` / `emerald-500` as the primary brand color for high-visibility CTAs and active states, balanced with `slate-50` backgrounds and `slate-800` body text.