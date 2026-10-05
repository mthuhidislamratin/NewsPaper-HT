# KhoborPatra Newsroom UX and Design System

This is the implementation handoff for the KhoborPatra public website and newsroom CMS. The editorial voice and familiar red masthead remain recognizable; the interface uses a quieter, high-contrast canvas, a strong typographic hierarchy, and publication controls that make the next editorial action obvious.

## 1. Product and UX strategy

### Reader and newsroom needs

- Readers scan the top of a page before deciding to commit. Put urgent, important, and personalized signals above the fold, keep headlines specific, and never make a reader decipher which content is sponsored.
- Mobile is the default reading context: one-column feeds, large tap targets, stable image ratios, visible publication times, short navigation, and no horizontal scrolling.
- Breaking news is a newsroom state, not a permanent decoration. Use it only while the story is actively developing; show a timestamp and link to the source article.
- Editorial work is a sequence with accountability: draft, review, fact check, approved, scheduled, published, archived. Every state change records actor, timestamp, and reason; destructive actions require confirmation and offer recovery where possible.
- Search starts with a forgiving query, then supports category, author, date, and sort refinement without discarding the query. Empty states offer a way to broaden or clear filters.
- Ads use reserved aspect-ratio slots with a visible “Advertisement” label. They never interrupt a headline, cover image, or paragraph without a clear boundary.
- SEO defaults are generated from editorial fields. A preview shows title, description, canonical URL, and social-card crop before publish.
- Reader retention comes from useful continuation: context links, topic tags, related coverage, author pages, a restrained newsletter prompt, and a clear next story—not forced overlays.

### UX principles

1. **Clarity before density:** one lead story, a small number of supporting priorities, then a scannable feed.
2. **Editorial hierarchy:** breaking > lead > section lead > latest > reference/navigation.
3. **Trust by disclosure:** visible author, date/time, correction and sponsored indicators.
4. **Progressive control:** simple defaults first; advanced filters, SEO, targeting, and analytics only when needed.
5. **Safe publishing:** explicit status, validation at the point of entry, preview, confirmation for irreversible operations, and audit trail.
6. **Accessible by default:** semantic landmarks, keyboard-operable controls, non-color status cues, reduced motion, captions/alt text, and WCAG AA contrast.

## 2. Brand system

### Naming and identity

- Primary brand: **KhoborPatra** (খবরপত্র).
- Alternative product/editorial descriptors: KhoborPatra Newsroom; KP Briefing; The Daily Dispatch.
- Logo direction: a sturdy editorial wordmark with a compact “KP” monogram for small screens. Use a custom vector mark; do not substitute an emoji or a generic newspaper icon.
- Personality: informed, independent, composed, human, locally grounded, globally curious.
- Voice: direct and specific; distinguish confirmed fact from analysis; avoid sensational claims, unexplained acronyms, and false urgency.
- Audiences: daily readers, mobile-first commuters, diaspora readers, subject-followers, contributors, editors, ad operations, and administrators.

### Color tokens

| Token | Light | Usage |
|---|---|---|
| brand-700 | `#9B1C27` | Masthead accent, primary actions |
| brand-800 | `#781720` | Hover/pressed primary |
| brand-100 | `#FBEAEC` | Tinted editorial surface |
| ink-950 | `#17191D` | Headlines |
| ink-700 | `#444A53` | Body |
| ink-500 | `#6C737D` | Metadata |
| line-200 | `#E5E7EB` | Dividers and borders |
| paper-50 | `#F7F7F5` | Page background |
| surface | `#FFFFFF` | Cards and forms |
| success-700 | `#187347` | Published/success |
| warning-700 | `#98620B` | Scheduled/review |
| danger-700 | `#B4232D` | Breaking/destructive |
| info-700 | `#205DA8` | Fact-check/information |

### Design tokens JSON

```json
{
  "color": {
    "brand": { "50": "#FBEAEC", "700": "#9B1C27", "800": "#781720" },
    "ink": { "950": "#17191D", "700": "#444A53", "500": "#6C737D" },
    "line": { "200": "#E5E7EB" },
    "paper": { "50": "#F7F7F5", "surface": "#FFFFFF" },
    "status": { "success": "#187347", "warning": "#98620B", "danger": "#B4232D", "info": "#205DA8" }
  },
  "font": {
    "display": "Georgia, 'Times New Roman', serif",
    "ui": "Inter, 'Segoe UI', Arial, sans-serif",
    "mono": "ui-monospace, SFMono-Regular, Consolas, monospace"
  },
  "typeScale": { "xs": "0.75rem", "sm": "0.875rem", "base": "1rem", "lg": "1.125rem", "xl": "1.25rem", "2xl": "1.5rem", "3xl": "2rem", "4xl": "2.75rem", "5xl": "3.5rem" },
  "space": { "1": "0.25rem", "2": "0.5rem", "3": "0.75rem", "4": "1rem", "6": "1.5rem", "8": "2rem", "12": "3rem", "16": "4rem" },
  "radius": { "sm": "0.375rem", "md": "0.625rem", "lg": "1rem", "pill": "999px" },
  "shadow": { "card": "0 1px 3px rgb(23 25 29 / 7%)", "menu": "0 12px 32px rgb(23 25 29 / 16%)" },
  "breakpoint": { "sm": "640px", "md": "768px", "lg": "1024px", "xl": "1280px", "2xl": "1440px" }
}
```

### Typography and layout

- Display/headlines: editorial serif (Georgia fallback); article body: Georgia or a licensed readable serif, 18–20px desktop and 18px mobile, 1.75–1.9 line height.
- UI, nav, metadata, controls: neutral sans; metadata 12–14px; body text 16px; buttons at least 14px and 44px high on touch.
- Responsive headline scale: lead 36–56px; section lead 26–36px; card title 18–24px; compact title 16–20px.
- Maximum reading measure: 680px; general layout max: 1240px; 12-column desktop grid, 8-column tablet, 4-column mobile.
- Spacing follows a 4px base. Use 16–24px card padding, 32–48px section separation, and consistent 1px neutral dividers.
- Breakpoints: 640 / 768 / 1024 / 1280 / 1440px. Below 768px, collapse to one column and replace the full nav with menu, search, and bottom actions.

## 3. Components

### Controls

- **Primary button:** brand fill, white text, 44px minimum height, visible focus ring; one per decision area.
- **Secondary:** neutral surface with border; **ghost:** transparent, hover tint; **outline:** transparent with brand border; **icon:** 40px square with accessible name.
- **Search:** leading search icon, query hint, clear affordance, submit action; preserve query and filters on result pages.
- **Select/textarea:** persistent label, help/error text, keyboard-native behavior, 44px control minimum; never rely on placeholder as label.
- **Checkbox/radio:** 20px control and 44px interactive target; group related values with `fieldset` and `legend`.

### Cards and badges

- **Hero:** 16:9 image, category, one concise headline, excerpt, author/time. Lead content is not obscured by overlays.
- **Breaking:** solid danger label plus text and timestamp; animation only if reduced-motion is not requested and the ticker is user-controllable.
- **Trending:** numbered list with restrained dividers and optional movement indicator backed by real data.
- **Article card:** image, category, headline, excerpt, author/time; image alt uses editorial alt text, not title by default.
- **Author:** portrait or initials, name, role, bio, social links, published count.
- **Advertisement:** fixed ratio, reserved space, “Advertisement” label, safe external-link behavior.
- **Badges:** Breaking (danger), Exclusive (brand outline), Live (danger plus text), Trending (neutral/icon plus text); never color-only.

### Navigation and shell

- Desktop: slim utility row (date, editions, language, social); prominent masthead/search; primary category nav; optional topic/mega-menu.
- Sticky header becomes compact after scrolling; it does not cover anchors or reading controls.
- Mobile: compact masthead with menu/search actions, horizontally scrollable topic chips, optional four-item bottom nav (Home, Sections, Search, Saved) with text labels.
- Footer: about, editorial standards, contact, advertise, privacy, terms, social and newsletter; preserve clear hierarchy and legal links.

## 4. Information architecture and journeys

```text
Home
├── Sections (Politics, World, Business, Sports, Technology, Entertainment, Health, Opinion)
│   └── Category landing → article
├── Topics / Tags → topic feed
├── Search → results → filters → article
├── Authors → author profile → articles
├── Article → related coverage / topic / author
├── Newsletter
├── About / Editorial standards / Contact / Advertise
├── Privacy / Terms / Accessibility
└── Admin (authenticated and permission-gated)
    ├── Dashboard / Analytics / Audit log
    ├── Articles / Categories / Tags / Media
    ├── Users / Roles / Permissions / Comments
    ├── Advertisements / Homepage builder / SEO
    └── Settings
```

Reader journeys: scan breaking + lead → open story → read/share/follow topic → related coverage; search query → refine filters → result → author/topic continuation; category browse → lead → latest list. Newsroom journey: assignment/draft → save → review → fact-check → approve → preview → schedule/publish → monitor → correct/archive, with visible ownership and logged transitions.

## 5. Public page wireframes and behavior

### Home

```text
Utility: date | edition | weather (only if configured) | language | social
Masthead: logo | newsletter/search actions
Primary nav: sections | topics | menu
Breaking ticker (only while active; timestamp; pause/close control)
Lead grid: 2/3 lead story | 1/3 two supporting stories
Top stories: ranked short list
Latest: image-led list                    Sidebar: trending / labeled ad
Section highlights: section title + 3 stories per section
Editor's picks | video | most read | in-feed reserved ad
Newsletter panel
Footer navigation, trust links, contact, edition/language
```

Purpose: orient, prioritize, and provide breadth. The lead owns visual focus; latest remains chronological; editorial picks are labeled as selected rather than algorithmic. Never show empty decorative sections—hide them or show an editor-friendly empty state only in preview.

### Article detail

Desktop: article column (max 680px) plus 300px context rail; mobile: single column with no sticky ad. Order: breadcrumb/category → headline → dek → author/date/read time/correction → hero/caption/credit → share/save → body with subheads and pull quote → clearly separated in-article ad slots → sources/updates/tags → author card → related and trending → newsletter/comments. Reading progress is a thin `aria-hidden` indicator; share actions are keyboard operable. Include canonical, OpenGraph/Twitter image, Article JSON-LD, and `datePublished` / `dateModified`.

### Category, tag, author, and search

- Category: topic title/description; optional pinned lead; Latest/Most read switch; paginated list; category SEO.
- Tag: topic label, related articles, chronological default.
- Author: profile/role/bio/social links, recent and popular articles, follower count only if backed by a real follow system; do not show invented metrics.
- Search: always-visible query field; instant suggestions are a Livewire enhancement with a normal GET fallback; filters are category, author, date range, sort; chips expose and remove active filters; sort by relevance/newest/most read. Empty state repeats the query, suggests spelling/broader terms, and provides clear filters. Mobile filters open in a labelled bottom sheet and retain applied state.

### Supporting public pages

Newsletter uses explicit consent and confirmation; About and Editorial Standards describe ownership, corrections, sourcing, and contact; Advertise has placement specs and a contact CTA; Privacy/Terms/Accessibility are readable text pages, linked from footer.

## 6. Advertising rules

Desktop: top banner (max 970×90), sidebar (300×250), inline (responsive 1.91:1), footer (responsive). Tablet: full-width reserved banner or omit sidebar. Mobile: one in-feed unit after several stories and optional bottom sticky unit with close control; never cover article text/actions. Every slot reserves dimensions to reduce layout shift, labels sponsorship, has frequency limits, and respects consent/region. Report impressions/clicks only with disclosure and privacy controls; never execute arbitrary administrator-entered script in the page.

## 7. CMS shell and shared admin patterns

Desktop shell: 248px left sidebar with grouped navigation, top bar with breadcrumbs/search/notifications/profile, page title/action row, 12-column content canvas. Tablet collapses to icon rail; mobile uses drawer and sticky page action footer. Use persistent filters, saved views, pagination, row selection, bulk-action confirmation, undo toast for reversible actions, and explicit error summaries. Tables become stacked record cards on narrow screens; they must not force page-level horizontal scroll.

### Admin page archetypes

Every list: page title + primary create action; KPI summary when useful; search/filter/sort; selectable rows; pagination; empty/loading/error states; permission-aware actions. Every create/edit: labelled sections, server validation, draft save, preview where applicable, cancel protection for unsaved changes, success toast and return destination. Every view: metadata, relationships, activity/audit history, permitted actions. Audit log includes actor, verb, target, time, IP subject to retention policy, and before/after diff; secrets are redacted.

#### Articles

- List columns: title/status/category/author/views/published/updated. Filters: draft/review/fact-check/approved/scheduled/published/archived, category/tag/author/date. Bulk: publish, unpublish, archive, restore, delete; permission checked per row and operation.
- Editor flow: 1) title/dek/category/tags/author; 2) rich-text/block content and autosave; 3) SEO/social preview; 4) featured/gallery/video media and captions; 5) preview; 6) workflow action, schedule, or publish. Preserve server-side validation if JS is unavailable.
- Edit page includes revision timeline, compare/restore, reviewer assignment and notes, status timeline, and explicit correction/update metadata.
- Validation: title required/max 255; unique slug; category/tag existence; content required and sanitized; media MIME/size; SEO title max 70 and description max 160 recommended; published/scheduled states require permission and valid timestamp.
- Data/API: `articles`, `article_tag`, media; standard Laravel resource routes plus explicit workflow transition endpoints; Livewire `ArticleTable`, `ArticleEditor`, `ArticlePreview`, `RevisionTimeline`; Blade `admin.articles.index/create/edit/show`.

#### Categories and tags

- Category list/create/edit/show includes hierarchy, active state, slug, description, SEO, homepage pin, article count and analytics. Prevent self-parenting/cycles; deleting an in-use category requires reassign or explicit nullable detach.
- Tag list has usage count and search. Merge is transactional: move pivots to target, avoid duplicates, retain redirect/alias for old slug, then retire source. Deletion warns with affected article count.
- Data/API: `categories`, `tags`, `article_tag`; Livewire `CategoryTree`, `TagMergeDialog`; audit create/update/merge/delete.

#### Users, roles, permissions

- Users list: name/email/role/active/last activity; create/edit/view/deactivate/suspend/delete with confirmation and actor safeguards. Profile shows activity and login history if instrumented.
- Role assignment uses a searchable role selector and a read-only effective permission summary. Never allow removing the final active Super Admin.
- Role matrix groups permissions by articles, taxonomy, media, ads, homepage, SEO, users, moderation, analytics, audit, settings. Inheritance is explicit and visible; no hidden implicit grants. Super Admin bypass is logged.
- Data/API: `users`, Spatie role/permission pivots; `UserTable`, `UserForm`, `PermissionMatrix`; each controller action authorizes server-side.

#### Media library

- Grid/list, folders/collections, drag/drop multi-upload with progress, search by filename/alt/caption, filters by MIME/owner/date, storage total, reuse picker.
- Detail/edit supports alt, caption, credit, focal point/crop, replace while preserving references, usage list and safe delete (block or confirm detach if referenced).
- Validate server MIME, size and image decoding; never trust extension; storage errors are visible. Store originals in Laravel Storage; generate responsive variants through the media package.
- Livewire: `MediaBrowser`, `MediaUploader`, `MediaDetails`, `MediaPicker`; storage is private by default when media access is restricted.

#### Advertisements and campaigns

- Campaign dashboard, list/create/edit/view/archive; name, advertiser, placement, creative, destination, start/end, active state, campaign label, UTM metadata.
- Filters by placement/status/date/advertiser; schedule conflicts surfaced; preview creative at desktop/tablet/mobile slot ratios. Reports display impressions, clicks, CTR only when tracked; export CSV.
- Never store executable HTML/script as ad content. Use vetted image + URL creatives or an explicitly reviewed provider integration.
- Data/API: `advertisements` plus future campaign/event tables; Livewire `CampaignTable`, `CampaignEditor`, `AdPreview`; validate URL, dates, MIME, active window.

#### Homepage builder

- Preview canvas plus ordered section panel. Add/remove/reorder/pin/feature/schedule, section visibility, article selector, draft preview, publish layout, restore history.
- Reorder by accessible up/down controls as well as drag/drop; save order transactionally; preview uses an unpublished revision; publishing captures the previous version.
- Data/API: `homepage_sections` and version records; Livewire `HomepageBuilder`, `SectionSettings`, `ArticlePicker`; current section table can store configuration but version history requires a dedicated version table before promised.

#### SEO manager and settings

- SEO overview flags missing metadata, duplicates and index/canonical issues; edit per page/entity; live SERP and social-card preview; sitemap status, redirects, indexing and 404 monitoring only when backed by stored data/integration.
- Settings are grouped General, Brand, Theme, Email, Notifications, Social, SEO defaults, API credentials, Security, Backup, Storage. Sensitive secrets are masked and never redisplayed; changes require permissions and are audited.
- Data/API: article SEO fields and `site_settings`; redirect/404/Search Console dashboards require dedicated tables/integration and must show “not connected” rather than invented metrics.

#### Comments, analytics, audit, editorial workflow

- Comments: queue tabs pending/approved/rejected/spam; approve/reject/spam/mute/ban, keyword rules, reason and audit record. Require a comments table and moderation events before exposing operational controls.
- Analytics: range, comparison, category/author/article, views/visitors/engagement/revenue/CTR. Only show metrics sourced from configured events/analytics; CSV export includes applied filters and generation time.
- Audit: searchable actor/action/target/date/IP, redacted before/after diff; rollback only when the target has a safe inverse and the user has permission.
- Editorial timeline: draft → review → fact check → approved → scheduled → published → archived; assignment, reviewer notes, timestamps and notifications. Transitions are validated by role and current state; direct status field edits do not bypass workflow.

## 8. Dark mode

Use `data-theme="dark"` and semantic variables rather than inverted colors. Dark surfaces: page `#111316`, raised `#1A1D22`, line `#30353D`, text `#F4F5F6`, muted `#B2B8C1`; brand accent `#EF7C84`. Preserve contrast and status distinctions. Article body remains high-contrast and long-form comfortable; images/ad placeholders keep boundaries; admin forms/tables/focus states are themed. Persist explicit user preference; respect `prefers-color-scheme` before an explicit choice.

## 9. Accessibility and micro-interactions

- Target WCAG 2.2 AA: normal text contrast ≥4.5:1; large text and meaningful UI graphics ≥3:1; visible 2px focus ring with offset; 44×44px touch target; do not encode status by color alone.
- Use `header/nav/main/aside/footer`, a skip link, one H1, ordered headings, visible form labels, `aria-describedby` errors, live regions for upload/status messages, and `aria-current` navigation.
- Keyboard: logical tab order, skip-to-content, escape closes menus/dialogs, focus is trapped/restored in modal, no keyboard trap; sortable headers announce direction.
- Respect `prefers-reduced-motion`; captions/transcripts for video; informative alt for images and empty alt for decorative images.
- Autosave indicates “Saving / Saved at time / Failed—retry”; draft recovery is opt-in and never silently replaces server content. Preview opens a separate, authenticated preview URL. Toasts are announced and actionable. Destructive dialogs identify the target and consequence.

## 10. Handoff architecture

### Blade component inventory

`components/site/{masthead,utility-bar,primary-nav,mobile-nav,breaking-ticker,ad-slot,section-heading,article-card,hero-card,author-byline,topic-chip,newsletter-form,footer,seo}`; `components/admin/{shell,sidebar,topbar,breadcrumbs,page-heading,stat-card,data-table,status-badge,filter-bar,form-field,empty-state,confirm-dialog,toast}`.

### Livewire inventory

Public: `SearchBox`, `SearchFilters`, `NewsletterSignup`, `BreakingTicker` (only if live updates are needed). Admin: `ArticleTable`, `ArticleEditor`, `ArticlePreview`, `RevisionTimeline`, `CategoryTree`, `TagManager`, `MediaBrowser`, `MediaUploader`, `MediaPicker`, `CampaignTable`, `HomepageBuilder`, `PermissionMatrix`, `AuditViewer`, `CommentQueue`, `AnalyticsDashboard`.

### Folder structure

```text
app/
  Actions/Editorial/TransitionArticle.php
  Policies/{Article,Category,Tag,Media,Advertisement,User}Policy.php
  Models/
  Livewire/{Public,Admin}/
resources/
  views/components/{site,admin}/
  views/frontend/
  views/admin/
  css/app.css
  js/app.js
routes/web.php
tests/Feature/{Public,Admin,Editorial}/
```

### Tailwind mapping

Use semantic CSS variables mapped to Tailwind theme colors, container widths `max-w-screen-xl` / `max-w-prose`, responsive grids `grid-cols-1 md:grid-cols-8 xl:grid-cols-12`, focus `focus-visible:ring-2`, and state variants from real model state. Keep repeated component patterns in Blade components; avoid long duplicated utility strings. Respect Laravel Vite asset entry points already present.

### Implementation sequence and acceptance

1. Establish shared tokens, public/admin shells, responsive primitives, semantics and contrast checks.
2. Deliver home/article/category/search/author pages with DB-driven content and SEO, including empty/loading/error states.
3. Complete article and taxonomy CRUD, editorial transitions, preview, media reuse, and authorization with feature tests.
4. Complete user/role/permission matrix, ads, homepage revisions, SEO manager and safe settings.
5. Add moderation, event-backed analytics, audit, exports and external integrations only with real storage/data sources.
6. Run Vite build, migrations from clean PostgreSQL, focused feature tests, role/permission negative tests, mobile viewport checks, and a browser acceptance flow.

Release gate: no fake metrics, no placeholder controls, no unsafe script fields, no public draft leakage, and every visible action has a working route and authorization test.
