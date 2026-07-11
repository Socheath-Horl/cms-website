# CMS Website — Implementation Plan

> **Status Legend**: ⬜ Pending · 🔄 In Progress · ✅ Completed · ❌ Cancelled

---

## Phase 1: Foundation — Week 1

**Goal**: Three projects running, connected, talking to each other.

---

### Step 1.1 — Create Laravel 11 API

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 1.1.1 | Scaffold Laravel project | `composer create-project laravel/laravel api --prefer-dist` | ✅ |
| 1.1.2 | Configure MySQL database | Edit `api/.env`: DB_DATABASE=cms_website, DB_USERNAME, DB_PASSWORD | ✅ |
| 1.1.3 | Create database | `mysql -u root -e "CREATE DATABASE cms_website"` (or use GUI) | ✅ |
| 1.1.4 | Run initial migration | `cd api && php artisan migrate` | ✅ |
| 1.1.5 | Install Sanctum | `cd api && composer require laravel/sanctum` | ✅ |
| 1.1.6 | Publish Sanctum config | `cd api && php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"` | ✅ |
| 1.1.7 | Run Sanctum migration | `cd api && php artisan migrate` | ✅ |
| 1.1.8 | Set Sanctum stateful domains | In `api/.env`: SANCTUM_STATEFUL_DOMAINS=localhost:3001,localhost:3000 | ✅ |
| 1.1.9 | Set SESSION_DRIVER | In `api/.env`: SESSION_DRIVER=cookie, SESSION_DOMAIN=localhost | ✅ |
| 1.1.10 | Configure CORS | Edit `api/config/cors.php`: allowed_origins env, supports_credentials = true | ✅ |
| 1.1.11 | Verify API works | `cd api && php artisan serve` → `http://localhost:8000` → 200 OK | ✅ |

---

### Step 1.2 — Create Nuxt 3 Public Site

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 1.2.1 | Scaffold Nuxt 3 project | `npx nuxi@latest init public-site` | ✅ |
| 1.2.2 | Install PrimeVue | `cd public-site && npm install primevue @primevue/nuxt-module` | ✅ |
| 1.2.3 | Install Tailwind CSS | `cd public-site && npm install -D tailwindcss @tailwindcss/vite` | ✅ |
| 1.2.4 | Create Tailwind CSS file | Create `app/assets/css/tailwind.css` with `@import "tailwindcss"` | ✅ |
| 1.2.5 | Configure nuxt.config.ts | Add PrimeVue module, Tailwind CSS import | ✅ |
| 1.2.6 | Create basic layout | Create `app/app.vue` with Header, Footer, `<NuxtPage />` + `app/pages/index.vue` | ✅ |
| 1.2.7 | Verify dev server | `cd public-site && npm run dev` → `http://localhost:3000` (Nuxt 4.4.8 running) | ✅ |

---

### Step 1.3 — Create Vue 3 Admin Panel

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 1.3.1 | Scaffold Vite + Vue 3 + TS | `npm create vite@latest admin -- --template vue-ts` | ✅ |
| 1.3.2 | Install dependencies | `cd admin && npm install && npm install vue-router pinia axios` | ✅ |
| 1.3.3 | Install PrimeVue | `cd admin && npm install primevue @primeuix/themes` (swapped deprecated package) | ✅ |
| 1.3.4 | Install Tailwind CSS | `cd admin && npm install -D tailwindcss @tailwindcss/vite` | ✅ |
| 1.3.5 | Set Vite port to 3001 | Edit `admin/vite.config.ts`: server.port = 3001 | ✅ |
| 1.3.6 | Add Tailwind CSS plugin | Add `tailwindcss()` to Vite plugins array | ✅ |
| 1.3.7 | Create Tailwind entry file | Edit `admin/src/style.css`: add `@import "tailwindcss"` | ✅ |
| 1.3.8 | Create sidebar layout | Edit `admin/src/App.vue`: sidebar Menubar + `<router-view />` | ✅ |
| 1.3.9 | Create Router placeholder | Create `admin/src/router/index.ts` with Dashboard route | ✅ |
| 1.3.10 | Create Pinia store placeholder | Create `admin/src/stores/auth.ts` | ✅ |
| 1.3.11 | Verify dev server | `cd admin && npm run dev` → `http://localhost:3001` (Vite v8.1.4) | ✅ |

---

### Step 1.4 — Wire Up End-to-End Connectivity

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 1.4.1 | Create Axios API service | Create `admin/src/services/api.ts` with baseURL, credentials, token interceptor | ✅ |
| 1.4.2 | Add CSRF cookie exchange | Call `GET /sanctum/csrf-cookie` on app mount | ✅ |
| 1.4.3 | Test API call from admin | Call `GET /api/settings` from admin, log result | ✅ |
| 1.4.4 | Start both servers | Terminal 1: `cd api && php artisan serve` / Terminal 2: `cd admin && npm run dev` | ✅ |
| 1.4.5 | Verify no CORS errors | Open `http://localhost:3001` → console shows Connected! with site settings JSON | ✅ |

---

### Step 1.T — Testing

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 1.T.1 | Write health-check Pest test | Test `GET /api/settings` returns 200 + asserts site_name and version | ✅ |
| 1.T.2 | Run Pest test | `cd api && php artisan test` → 3 passed (4 assertions) | ✅ |

---

**✅ Phase 1 Done**: Three servers running. Admin can make API calls to Laravel.

---

## Phase 2: Authentication — Week 2

**Goal**: Full login/register/logout flow in admin panel.

---

### Step 2.1 — Build Laravel Auth Endpoints

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 2.1.1 | Create AuthController | `api/app/Http/Controllers/Api/AuthController.php` | ⬜ |
| 2.1.2 | Create register endpoint | Validates name, email, password; returns user + token | ⬜ |
| 2.1.3 | Create login endpoint | Validates email, password; checks hash; returns user + token | ⬜ |
| 2.1.4 | Create logout endpoint | Revokes current token via `currentAccessToken()->delete()` | ⬜ |
| 2.1.5 | Create user endpoint | Returns `$request->user()` | ⬜ |
| 2.1.6 | Create updateUser endpoint | Validates + updates name/email | ⬜ |
| 2.1.7 | Add auth routes to api.php | POST register/login, GET user, POST logout, PUT user (all with sanctum middleware where needed) | ⬜ |
| 2.1.8 | Test with curl | `curl -X POST ... /api/auth/register` → see token returned | ⬜ |

---

### Step 2.2 — Build Admin Login UI + Pinia Auth Store

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 2.2.1 | Define User TypeScript interface | `admin/src/types/index.ts`: id, name, email, role | ⬜ |
| 2.2.2 | Create Pinia auth store | `admin/src/stores/auth.ts`: login(), logout(), fetchUser() actions | ⬜ |
| 2.2.3 | Add Axios 401 interceptor | In `admin/src/services/api.ts`: clear token + redirect on 401 | ⬜ |
| 2.2.4 | Create LoginView page | PrimeVue form: InputText(email) + Password + Button | ⬜ |
| 2.2.5 | Add /login route to router | Import LoginView, add to routes array | ⬜ |

---

### Step 2.3 — Implement Persistent Auth + Route Guards

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 2.3.1 | Store token in localStorage | In auth store login action: `localStorage.setItem('token', data.token)` | ⬜ |
| 2.3.2 | Restore session on mount | In main.ts: call `authStore.fetchUser()` before `app.mount()` | ⬜ |
| 2.3.3 | Add route guard | Router `beforeEach`: redirect to /login if requiresAuth + no token | ⬜ |
| 2.3.4 | Add guest guard | Redirect to / if already logged in and visiting /login | ⬜ |
| 2.3.5 | Show/hide nav by auth state | In Sidebar: v-if on auth.isAuthenticated | ⬜ |
| 2.3.6 | Verify full flow | Login → redirect → refresh → stay logged in → logout → redirect to login | ⬜ |

---

### Step 2.T — Testing

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 2.T.1 | Pest: register test | POST register → assert 201, assert JSON structure | ⬜ |
| 2.T.2 | Pest: login test | POST login with valid creds → assert 200 | ⬜ |
| 2.T.3 | Pest: invalid credentials | POST login with wrong password → assert 422 | ⬜ |
| 2.T.4 | Pest: protected route with valid token | GET auth/user with token → assert 200 | ⬜ |
| 2.T.5 | Pest: protected route without token | GET auth/user → assert 401 | ⬜ |
| 2.T.6 | Pest: logout revokes token | Login → logout → try protected route → assert 401 | ⬜ |
| 2.T.7 | Vitest: auth store login | Mock Axios, test store token/user after login | ⬜ |
| 2.T.8 | Vitest: auth store logout | Test token cleared from localStorage | ⬜ |
| 2.T.9 | Vitest: 401 interceptor | Mock 401 response → verify token cleared + redirect | ⬜ |

---

**✅ Phase 2 Done**: Admin can log in, see dashboard, log out. Token persists on refresh.

---

## Phase 3: Core Content CRUD — Week 3-4

**Goal**: Admin can create, read, update, delete Pages and Posts.

---

### Step 3.1 — Create Models + Migrations

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 3.1.1 | Create Page model + migration | `php artisan make:model Page -m` | ⬜ |
| 3.1.2 | Create Post model + migration | `php artisan make:model Post -m` | ⬜ |
| 3.1.3 | Create Category model + migration | `php artisan make:model Category -m` | ⬜ |
| 3.1.4 | Create Tag model + migration | `php artisan make:model Tag -m` | ⬜ |
| 3.1.5 | Create Media model + migration | `php artisan make:model Media -m` | ⬜ |
| 3.1.6 | Create post_tag pivot migration | `php artisan make:migration create_post_tag_table` | ⬜ |
| 3.1.7 | Define Page migration columns | title, slug(unique), content, excerpt, status(draft/published), featured_image_id, author_id, published_at, meta_title, meta_description, softDeletes | ⬜ |
| 3.1.8 | Define Post migration columns | Same as Page + category_id + tags relationship | ⬜ |
| 3.1.9 | Define Media migration columns | name, file_name, mime_type, size, width, height, alt_text | ⬜ |
| 3.1.10 | Define Category migration columns | name, slug(unique), description | ⬜ |
| 3.1.11 | Define Tag migration columns | name, slug(unique) | ⬜ |
| 3.1.12 | Define post_tag pivot columns | post_id FK, tag_id FK, composite PK | ⬜ |
| 3.1.13 | Set Post model relationships | belongsTo(author), belongsTo(category), belongsToMany(tags), belongsTo(featuredImage) | ⬜ |
| 3.1.14 | Set Page model relationships | belongsTo(author), belongsTo(featuredImage) | ⬜ |
| 3.1.15 | Create PostFactory | `php artisan make:factory PostFactory --model=Post` | ⬜ |
| 3.1.16 | Create PageFactory | `php artisan make:factory PageFactory --model=Page` | ⬜ |
| 3.1.17 | Create CategoryFactory + TagFactory | `php artisan make:factory CategoryFactory --model=Category` | ⬜ |
| 3.1.18 | Seed database | Update DatabaseSeeder: create admin user, 5 categories, 10 tags, 10 pages, 20 posts with tags | ⬜ |
| 3.1.19 | Run migrate + seed | `php artisan migrate && php artisan db:seed` | ⬜ |

---

### Step 3.2 — Build Public + Admin API Routes

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 3.2.1 | Create PostResource | `api/app/Http/Resources/PostResource.php` with all fields + relationships | ⬜ |
| 3.2.2 | Create PageResource | `api/app/Http/Resources/PageResource.php` | ⬜ |
| 3.2.3 | Create CategoryResource + TagResource | `api/app/Http/Resources/CategoryResource.php`, `TagResource.php` | ⬜ |
| 3.2.4 | Create UserResource | `api/app/Http/Resources/UserResource.php` | ⬜ |
| 3.2.5 | Create MediaResource | `api/app/Http/Resources/MediaResource.php` | ⬜ |
| 3.2.6 | Create StorePostRequest | `php artisan make:request StorePostRequest` with validation rules | ⬜ |
| 3.2.7 | Create StorePageRequest | `php artisan make:request StorePageRequest` with validation rules | ⬜ |
| 3.2.8 | Create public PostController | `Api/PostController`: index(paginated, published only, with relations) + show(by slug) | ⬜ |
| 3.2.9 | Create public PageController | `Api/PageController`: index + show(by slug) | ⬜ |
| 3.2.10 | Create public CategoryController | `Api/CategoryController`: index + postsBySlug | ⬜ |
| 3.2.11 | Create public TagController | `Api/TagController`: index + postsBySlug | ⬜ |
| 3.2.12 | Create admin PostController | `Admin/PostController`: full CRUD with PostResource | ⬜ |
| 3.2.13 | Create admin PageController | `Admin/PageController`: full CRUD with PageResource | ⬜ |
| 3.2.14 | Create admin CategoryController | `Admin/CategoryController`: full CRUD | ⬜ |
| 3.2.15 | Create admin TagController | `Admin/TagController`: full CRUD | ⬜ |
| 3.2.16 | Add public routes to api.php | GET pages, posts, categories, tags (no auth) | ⬜ |
| 3.2.17 | Add admin routes to api.php | apiResource under auth:sanctum + admin prefix | ⬜ |

---

### Step 3.3 — Admin Page Management UI

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 3.3.1 | Create pageStore (Pinia) | `admin/src/stores/pageStore.ts`: fetchAll, create, update, remove | ⬜ |
| 3.3.2 | Create PageListView | DataTable with title, slug, status, author, published_at columns; sort + search | ⬜ |
| 3.3.3 | Add page routes to router | /pages, /pages/create, /pages/:id/edit (all requiresAuth) | ⬜ |
| 3.3.4 | Create PageFormView | Form: title, slug(auto from title), content(textarea), excerpt, status(SelectButton), published_at, meta fields | ⬜ |
| 3.3.5 | Add create/edit/delete buttons | Create button in list, Edit/Delete per row, confirm dialog for delete | ⬜ |
| 3.3.6 | Add toast notifications | PrimeVue Toast for success/error after CRUD operations | ⬜ |

---

### Step 3.4 — Admin Post Management UI

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 3.4.1 | Create postStore (Pinia) | `admin/src/stores/postStore.ts`: fetchAll, create, update, remove | ⬜ |
| 3.4.2 | Create PostListView | DataTable with title, category, author, status, published_at columns; filter by category + status | ⬜ |
| 3.4.3 | Add post routes to router | /posts, /posts/create, /posts/:id/edit | ⬜ |
| 3.4.4 | Create PostFormView | Form: title, slug, content, excerpt, category dropdown, tag MultiSelect, featured image placeholder, status, published_at, meta fields | ⬜ |
| 3.4.5 | Load categories + tags for form | On mount: fetch categories and tags from API for dropdowns | ⬜ |

---

### Step 3.T — Testing

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 3.T.1 | Pest: admin can create post | POST /api/admin/posts with token → assert 201 | ⬜ |
| 3.T.2 | Pest: admin can update post | PUT /api/admin/posts/{id} → assert 200 | ⬜ |
| 3.T.3 | Pest: admin can delete post | DELETE /api/admin/posts/{id} → assert 200 + assert soft deleted | ⬜ |
| 3.T.4 | Pest: guest cannot create post | POST /api/admin/posts without token → assert 401 | ⬜ |
| 3.T.5 | Pest: validation errors for post | POST with missing title → assert 422 + field errors | ⬜ |
| 3.T.6 | Pest: public can view posts | GET /api/posts → assert 200, assert pagination structure | ⬜ |
| 3.T.7 | Pest: public page by slug | GET /api/pages/{slug} → assert 200 | ⬜ |
| 3.T.8 | Pest: 404 for missing slug | GET /api/pages/non-existent → assert 404 | ⬜ |

---

**✅ Phase 3 Done**: Admin can create, edit, delete, list pages + posts. Public API returns them.

---

## Phase 4: Media Library — Week 4

**Goal**: Upload images, browse them, assign as featured images.

---

### Step 4.1 — Build Laravel Media Endpoints

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 4.1.1 | Define Media model with fillable | name, file_name, mime_type, size, width, height, alt_text | ⬜ |
| 4.1.2 | Create Admin/MediaController | index(paginated), store(upload + validate), destroy(delete file + DB record) | ⬜ |
| 4.1.3 | Add media routes to api.php | GET|POST /api/admin/media, DELETE /api/admin/media/{id} (auth:sanctum) | ⬜ |
| 4.1.4 | Create storage symlink | `php artisan storage:link` | ⬜ |
| 4.1.5 | Add file validation | Validate mimes=jpeg,png,webp,gif, max:5120 (5MB) | ⬜ |

---

### Step 4.2 — Admin Media Library UI

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 4.2.1 | Create mediaStore (Pinia) | fetchAll, upload, remove | ⬜ |
| 4.2.2 | Create MediaLibraryView | PrimeVue FileUpload for upload + image grid thumbnails + delete per item | ⬜ |
| 4.2.3 | Add media route to router | /media (requiresAuth) | ⬜ |
| 4.2.4 | Create MediaPicker modal | Dialog component: grid of thumbnails, click to select, emits media ID back | ⬜ |
| 4.2.5 | Integrate MediaPicker into forms | Replace featured image placeholder in PostFormView with MediaPicker button | ⬜ |

---

### Step 4.T — Testing

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 4.T.1 | Pest: upload image file | POST /api/admin/media with image file → assert 200 | ⬜ |
| 4.T.2 | Pest: reject invalid file type | POST /api/admin/media with .txt file → assert 422 | ⬜ |
| 4.T.3 | Pest: reject oversized file | POST /api/admin/media with >5MB file → assert 422 | ⬜ |
| 4.T.4 | Pest: list media | GET /api/admin/media → assert paginated structure | ⬜ |
| 4.T.5 | Pest: delete media | DELETE /api/admin/media/{id} → assert file removed + DB deleted | ⬜ |

---

**✅ Phase 4 Done**: Upload images, browse grid, assign as featured images.

---

## Phase 5: Navigation & Settings — Week 5

**Goal**: Manage menus, site settings, categories, and tags.

---

### Step 5.1 — Categories + Tags CRUD

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 5.1.1 | Create Admin/CategoryController | Add all CRUD methods (already started in 3.2.14) | ⬜ |
| 5.1.2 | Create Admin/TagController | Add all CRUD methods (already started in 3.2.15) | ⬜ |
| 5.1.3 | Create admin CategoryListView | DataTable with name, slug, actions | ⬜ |
| 5.1.4 | Create admin TagListView | DataTable with name, slug, actions | ⬜ |

---

### Step 5.2 — Menu Builder

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 5.2.1 | Create Menu model + migration | `php artisan make:model Menu -m`: name, slug(unique), location(unique) | ⬜ |
| 5.2.2 | Create MenuItem model + migration | `php artisan make:model MenuItem -m`: menu_id, parent_id(self-ref), title, url, type(enum), reference_id, target(enum), order | ⬜ |
| 5.2.3 | Set Menu model relationships | hasMany(menuItems), hasMany(children) for self-join | ⬜ |
| 5.2.4 | Create Admin/MenuController | CRUD for menus | ⬜ |
| 5.2.5 | Create Admin/MenuItemController | CRUD with order sorting, nested tree response | ⬜ |
| 5.2.6 | Create public MenuController | `GET /api/menus/{location}` returns tree with nested items | ⬜ |
| 5.2.7 | Add menu routes to api.php | Admin CRUD + public show by location | ⬜ |
| 5.2.8 | Create MenuListView | List of menus with create/edit/delete | ⬜ |
| 5.2.9 | Create MenuBuilderView | Drag-drop reorder (sortablejs/vuedraggable), add item dialog (type picker, reference search) | ⬜ |
| 5.2.10 | Add menu routes to admin router | /menus, /menus/:id/edit | ⬜ |

---

### Step 5.3 — Site Settings

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 5.3.1 | Create SiteSetting model + migration | `php artisan make:model SiteSetting -m`: key(string unique), value(text) | ⬜ |
| 5.3.2 | Create Admin/SettingsController | index returns all as key-value object, update saves each key | ⬜ |
| 5.3.3 | Create public SettingsController | `GET /api/settings` returns public settings only | ⬜ |
| 5.3.4 | Add settings routes | Admin GET/PUT, public GET | ⬜ |
| 5.3.5 | Create SettingsView | Form fields: site_name, site_description, logo, contact_email, social_links | ⬜ |
| 5.3.6 | Add settings route to admin router | /settings | ⬜ |
| 5.3.7 | Seed default settings | In DatabaseSeeder: site_name = "My CMS", etc. | ⬜ |

---

### Step 5.T — Testing

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 5.T.1 | Pest: menu tree returns nested structure | GET /api/menus/{location} → assert children array | ⬜ |
| 5.T.2 | Pest: settings save and retrieve | PUT settings → GET settings → assert same values | ⬜ |
| 5.T.3 | Pest: category CRUD | Create → read → update → delete category | ⬜ |

---

**✅ Phase 5 Done**: Drag-drop menus, editable settings, categories/tags managed.

---

## Phase 6: Admin Fine-Tuning — Week 5-6

**Goal**: Dashboard, role-based access, user management.

---

### Step 6.1 — Role-Based Access

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 6.1.1 | Create migration for role column | `php artisan make:migration add_role_to_users_table`: enum(admin, editor, author) default author | ⬜ |
| 6.1.2 | Create CheckRole middleware | `api/app/Http/Middleware/CheckRole.php`: checks user role against allowed list | ⬜ |
| 6.1.3 | Register middleware in Kernel | Add to `$routeMiddleware`: 'role' => CheckRole::class | ⬜ |
| 6.1.4 | Apply role middleware to routes | admin users route: role:admin; admin posts: role:admin,editor; etc. | ⬜ |
| 6.1.5 | Update admin UI for role gating | Hide/create buttons per role, disable routes | ⬜ |

---

### Step 6.2 — Dashboard

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 6.2.1 | Create DashboardController | `GET /api/admin/dashboard`: total_posts, pages, users, media + recent_posts + posts_per_month | ⬜ |
| 6.2.2 | Add dashboard route | GET /api/admin/dashboard (auth:sanctum) | ⬜ |
| 6.2.3 | Create DashboardView | 4 stat cards (PrimeVue Card) + recent posts table + chart | ⬜ |
| 6.2.4 | Install Chart.js or use PrimeVue Chart | `npm install chart.js` or PrimeVue Chart component | ⬜ |

---

### Step 6.3 — User Management

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 6.3.1 | Create Admin/UserController | CRUD for users, admin-only | ⬜ |
| 6.3.2 | Add user routes | apiResource under role:admin middleware | ⬜ |
| 6.3.3 | Create UserListView | DataTable with name, email, role, created_at; filter by role | ⬜ |
| 6.3.4 | Create UserFormView | Create/edit: name, email, password(only on create), role dropdown | ⬜ |
| 6.3.5 | Add user routes to admin router | /users, /users/:id/edit | ⬜ |

---

### Step 6.T — Testing

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 6.T.1 | Pest: editor cannot manage users | Editor role → POST user → assert 403 | ⬜ |
| 6.T.2 | Pest: author cannot publish posts | Author role → POST post with status=published → assert 403 | ⬜ |
| 6.T.3 | Pest: admin dashboard returns stats | GET /api/admin/dashboard → assert JSON structure | ⬜ |

---

**✅ Phase 6 Done**: Dashboard, role-gated features, user management.

---

## Phase 7: Public Site — Week 6-7

**Goal**: Nuxt 3 public site consumes API, renders pages and posts.

---

### Step 7.1 — Nuxt 3 Setup for API

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 7.1.1 | Add API URL to runtime config | `nuxt.config.ts`: runtimeConfig.public.apiUrl | ⬜ |
| 7.1.2 | Create useApi composable | Wrapper around $fetch with baseURL | ⬜ |
| 7.1.3 | Create usePosts composable | getLatest(page), getBySlug(slug) | ⬜ |
| 7.1.4 | Create usePages composable | getBySlug(slug) | ⬜ |
| 7.1.5 | Create useCategories composable | getAll(), getPostsBySlug(slug) | ⬜ |
| 7.1.6 | Create useMenu composable | getByLocation(location) | ⬜ |

---

### Step 7.2 — Public Pages

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 7.2.1 | Create HomePage (index.vue) | Hero section + latest 6 posts grid | ⬜ |
| 7.2.2 | Create PageDetail (pages/[slug].vue) | Render page title + content | ⬜ |
| 7.2.3 | Create BlogList (blog/index.vue) | Paginated post grid + category filter sidebar | ⬜ |
| 7.2.4 | Create PostDetail (blog/[slug].vue) | Full post: content, author, featured image, tags, related posts | ⬜ |
| 7.2.5 | Create CategoryPosts (category/[slug].vue) | Posts filtered by category | ⬜ |
| 7.2.6 | Create TagPosts (tag/[slug].vue) | Posts filtered by tag | ⬜ |
| 7.2.7 | Create 404 page | Custom not-found page | ⬜ |

---

### Step 7.3 — Navigation

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 7.3.1 | Fetch menus in layout | OnMounted: fetch header + footer menu | ⬜ |
| 7.3.2 | Create NavItem recursive component | Renders menu tree with children | ⬜ |
| 7.3.3 | Create Breadcrumbs component | Computed from current route path segments | ⬜ |

---

### Step 7.4 — SEO

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 7.4.1 | Add dynamic title/meta per page | useHead() in each page with computed values | ⬜ |
| 7.4.2 | Install Nuxt sitemap module | `npm install @nuxtjs/sitemap`, configure in nuxt.config | ⬜ |
| 7.4.3 | Add Open Graph meta tags | og:title, og:description, og:image per page/post | ⬜ |
| 7.4.4 | Add Twitter card meta tags | twitter:card, twitter:title, twitter:description | ⬜ |

---

### Step 7.5 — Search

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 7.5.1 | Add search query param to PostController | Filter by `?search=keyword` on title + content (LIKE) | ⬜ |
| 7.5.2 | Create SearchResults page (search.vue) | Input field + results grid with excerpts | ⬜ |
| 7.5.3 | Handle empty results state | Show "No results found" message | ⬜ |

---

### Step 7.T — Testing

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 7.T.1 | Pest: public posts endpoint shape | GET /api/posts → assert pagination, data array, correct fields | ⬜ |
| 7.T.2 | Pest: public pages no auth required | GET /api/pages without token → assert 200 | ⬜ |
| 7.T.3 | Pest: search filter works | GET /api/posts?search=keyword → assert filtered results | ⬜ |
| 7.T.4 | Pest: 404 for non-existent page | GET /api/pages/bad-slug → assert 404 | ⬜ |

---

**✅ Phase 7 Done**: Full public-facing blog + pages with SEO, search, navigation.

---

## Phase 8: Final Polish & Deploy — Week 7-8

**Goal**: Full integration suite, production deployment.

---

### Step 8.1 — Full Integration Test Suite

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 8.1.1 | Run all Pest tests | `cd api && php artisan test` → fix any failures | ⬜ |
| 8.1.2 | Run all Vitest tests | `cd admin && npm run test` → fix any failures | ⬜ |
| 8.1.3 | Write e2e flow test | Create post via admin → verify in public listing | ⬜ |
| 8.1.4 | Test all role/permission combos | Admin/Editor/Author for each protected endpoint | ⬜ |
| 8.1.5 | Test media upload → featured image flow | Upload image → set as featured → verify in public post | ⬜ |

---

### Step 8.2 — Production Deployment

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 8.2.1 | Cache Laravel config + routes | `php artisan config:cache`, `route:cache`, `view:cache`, `optimize` | ⬜ |
| 8.2.2 | Build admin for production | `cd admin && npm run build` → serve `dist/` with Nginx | ⬜ |
| 8.2.3 | Build public site for production | `cd public-site && npm run build` → serve `.output/public/` with Nginx | ⬜ |
| 8.2.4 | Set production .env | APP_ENV=production, APP_DEBUG=false, DB credentials | ⬜ |
| 8.2.5 | Run production migrations | `php artisan migrate --force` | ⬜ |

---

### Step 8.3 — Documentation

| # | Task | Commands / Notes | Status |
|---|------|-------------------|--------|
| 8.3.1 | Write README.md | Project overview, setup steps, env variables, commands | ⬜ |
| 8.3.2 | Auto-generate API docs | Install Scramble or Scribe: `composer require dedoc/scramble` | ⬜ |

---

## Progress Summary

| Phase | Duration | Tasks | Status |
|-------|----------|-------|--------|
| 1 — Foundation | Week 1 | 11 + 7 + 11 + 5 + 2 = 36 tasks | 🔄 |
| 2 — Authentication | Week 2 | 8 + 5 + 6 + 9 = 28 tasks | ⬜ |
| 3 — Content CRUD | Week 3-4 | 19 + 17 + 6 + 5 + 8 = 55 tasks | ⬜ |
| 4 — Media Library | Week 4 | 5 + 5 + 5 = 15 tasks | ⬜ |
| 5 — Navigation & Settings | Week 5 | 4 + 10 + 7 + 3 = 24 tasks | ⬜ |
| 6 — Admin Fine-Tuning | Week 5-6 | 5 + 4 + 5 + 3 = 17 tasks | ⬜ |
| 7 — Public Site | Week 6-7 | 6 + 7 + 3 + 4 + 3 + 4 = 27 tasks | ⬜ |
| 8 — Final Polish & Deploy | Week 7-8 | 5 + 5 + 2 = 12 tasks | ⬜ |
| **Total** | **~8 weeks** | **~214 tasks** | |
