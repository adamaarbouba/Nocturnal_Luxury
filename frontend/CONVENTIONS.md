# React conversion conventions (agents: read fully before working)

Goal: convert the Laravel Blade app into Laravel API (Sanctum SPA auth) + this standalone React app (Vite, React Router, axios, Tailwind v4). Fidelity to the existing Blade UI is the bar — same palette, layout, copy.

## Theme
Dark "Nocturnal Luxury". Tailwind v4 theme tokens defined in `src/index.css`:
`night #4E3B46` (page bg), `charcoal #383537` (cards/nav), `rose #A0717F` (accent/buttons), `rose-light #b58290`, `cream #EAD3CD` (headings), `mist #CFCBCA` (body text), `ink #1A1515`.
Use them as Tailwind classes (`bg-night`, `text-cream`, `bg-rose`…). Headings use `font-serif`.

## Frontend structure
- `src/lib/api.js` — default export with `.get/.post/.put/.patch/.delete('/path')`; already prefixes `/api` and handles CSRF + credentials. Never instantiate axios yourself.
- `src/context/AuthContext.jsx` — `useAuth()` → `{ user, loading, login, register, logout }`; `dashboardPathFor(user)`.
- `src/components/` — shared UI (Button, Card, Alert, Badge, StatCard, Table, FormInput, Icon, Breadcrumbs, Navbar, Footer, Layout, ProtectedRoute). Reuse these; do not duplicate.
- `src/pages/<area>/…` — your pages. Wrap page content in `<Layout>`.
- `src/routes/<area>.jsx` — the ONLY routes file you edit. Export default array of `{ path, element }`. Guard with `<ProtectedRoute roles={['admin']}>…</ProtectedRoute>`.

## Backend structure
- Add API controllers under `app/Http/Controllers/Api/<Area>/` — thin JSON versions of the existing web controllers. Reuse the existing controller logic/queries verbatim where possible; return `response()->json(...)` instead of views/redirects. Keep existing FormRequests.
- Register routes ONLY in `routes/api/<area>.php` (create it; it's auto-required by routes/api.php). Prefix with the area, guard with `Route::middleware(['auth:sanctum', 'role:<slug>'])`.
- Do NOT edit routes/web.php, routes/api.php, bootstrap/app.php, or another area's files.

## Conversion rules
- One React page per Blade view; match the Blade markup/classes closely (inline styles from Blade may become Tailwind arbitrary values or stay inline).
- Blade `@if(session('success'))` flashes → local component state (show Alert after actions).
- Forms: controlled inputs, submit via `api.post/...`, show validation errors from the 422 response (`err.response.data.errors`).
- Links between pages: `<Link>` from react-router-dom, paths mirror the web routes (e.g. `/admin/users/:id`).
- DB may not be running locally — verify correctness by reading the existing web controller code, not by executing requests. `cd frontend && npx vite build` must pass; `php -l` your PHP files.
