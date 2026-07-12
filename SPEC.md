# CMS Website Specification

## Overview

A headless CMS with a Laravel REST API backend, Vue 3 admin panel, and Vue 3 public site. All content is managed via the admin panel and served to the public through API calls.

---

## 1. System Architecture

```
Browser (User) ──▶ public-site:3000 ──▶ api:8000 ──▶ MySQL
Browser (Admin) ──▶ admin:3001   ──▶ api:8000 ──▶ MySQL
```

| Project | Role | Tech |
|---------|------|------|
| `api/` | REST API + backend | Laravel 12, JWT, MySQL |
| `admin/` | Content management SPA | Vue 3, Pinia, PrimeVue, Vite :3001 |
| `public-site/` | Public-facing website | Nuxt 3, PrimeVue, Vite :3000 |

**Auth model**: JWT Bearer tokens via `tymon/jwt-auth` (stored in localStorage, sent via `Authorization: Bearer` header).

---

## 2. Data Models

### User
| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| name | string(255) | |
| email | string(255) | unique |
| password | string(255) | hashed |
| role | enum | admin / editor / author |
| avatar_id | foreignId | nullable → media.id |
| timestamps | | |

### Page
| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| title | string(255) | |
| slug | string(255) | unique |
| content | longText | HTML / structured JSON |
| excerpt | text | nullable |
| status | enum | draft / published |
| featured_image_id | foreignId | nullable → media.id |
| author_id | foreignId | → users.id |
| published_at | timestamp | nullable |
| meta_title | string(255) | nullable, SEO |
| meta_description | text | nullable, SEO |
| timestamps + softDeletes | | |

### Post
| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| title | string(255) | |
| slug | string(255) | unique |
| content | longText | HTML / structured JSON |
| excerpt | text | nullable |
| status | enum | draft / published |
| featured_image_id | foreignId | nullable → media.id |
| author_id | foreignId | → users.id |
| category_id | foreignId | nullable → categories.id |
| published_at | timestamp | nullable |
| meta_title | string(255) | nullable, SEO |
| meta_description | text | nullable, SEO |
| timestamps + softDeletes | | |

### Category
| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| name | string(255) | |
| slug | string(255) | unique |
| description | text | nullable |
| timestamps | | |

### Tag
| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| name | string(255) | |
| slug | string(255) | unique |
| timestamps | | |

### Media
| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| name | string(255) | original filename |
| file_name | string(255) | stored filename |
| mime_type | string(127) | |
| size | integer | bytes |
| width | integer | nullable (images) |
| height | integer | nullable (images) |
| alt_text | string(255) | nullable |
| timestamps | | |

### Menu
| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| name | string(255) | display name |
| slug | string(255) | unique, used in API |
| location | string(255) | unique, e.g. "header", "footer" |
| timestamps | | |

### MenuItem
| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| menu_id | foreignId | → menus.id (cascade) |
| parent_id | foreignId | nullable, self-referencing |
| title | string(255) | |
| url | string(255) | external URL or path |
| type | enum | page / post / custom / category |
| reference_id | integer | nullable, polymorphic |
| target | enum | _self / _blank |
| order | integer | sorting |
| timestamps | | |

### SiteSetting
| Field | Type | Notes |
|-------|------|-------|
| id | bigIncrements | PK |
| key | string(255) | unique |
| value | text | |
| timestamps | | |

### Pivot: post_tag
- post_id → posts.id
- tag_id → tags.id

---

## 3. API Endpoints

### Authentication (no prefix guard)
| Method | Path | Description |
|--------|------|-------------|
| POST | /api/auth/register | Register user |
| POST | /api/auth/login | Login → returns token |
| POST | /api/auth/logout | Logout (revoke token) |
| GET | /api/auth/user | Get authenticated user |
| PUT | /api/auth/user | Update profile |

### Public (guest)
| Method | Path | Description |
|--------|------|-------------|
| GET | /api/pages | List published pages |
| GET | /api/pages/{slug} | Get single page |
| GET | /api/posts | List published posts (paginated, filterable) |
| GET | /api/posts/{slug} | Get single post with tags |
| GET | /api/categories | List categories |
| GET | /api/categories/{slug}/posts | Posts in category |
| GET | /api/tags | List tags |
| GET | /api/tags/{slug}/posts | Posts with tag |
| GET | /api/menus/{location} | Get menu tree by location |
| GET | /api/settings | Get public site settings |

### Admin (auth:api + role check)
| Method | Path | Description |
|--------|------|-------------|
| GET | /api/admin/dashboard | Dashboard stats |
| Resource | /api/admin/pages | Full CRUD |
| Resource | /api/admin/posts | Full CRUD |
| Resource | /api/admin/categories | Full CRUD |
| Resource | /api/admin/tags | Full CRUD |
| Resource | /api/admin/media | Upload, list, delete |
| Resource | /api/admin/menus | Full CRUD |
| Resource | /api/admin/menu-items | Full CRUD |
| GET/PUT | /api/admin/settings | Get/update settings |
| Resource | /api/admin/users | User management (admin only) |

---

## 4. Query Parameters & Pagination

All list endpoints (`GET /api/posts`, `GET /api/pages`, `GET /api/admin/*`, etc.) support the following query parameters.

### Pagination

| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `page_size` | int | 15 | Items per page (max 100) |

**Response format**:
```json
{
  "page": 1,
  "page_size": 15,
  "next": "http://localhost:8000/api/posts?page=2",
  "previous": null,
  "count": 42,
  "total_page": 3,
  "data": [ ... ]
}
```

### Search

| Param | Type | Description |
|-------|------|-------------|
| `search` | string | Full-text `LIKE` search across model-specific fields |

Example: `GET /api/posts?search=laravel` searches `title`, `content`, `excerpt`.

### Sort

| Param | Type | Description |
|-------|------|-------------|
| `sort` | string | Comma-separated field names. Prefix with `-` for descending. Dot notation for related fields. |

Examples:
```
GET /api/posts?sort=published_at
GET /api/posts?sort=-published_at
GET /api/posts?sort=category.name,-published_at
GET /api/posts?sort=author.name
```

### Filter

| Param | Type | Description |
|-------|------|-------------|
| `filter` | string | Format: `field;operator;value`. Repeat for multiple filters. Dot notation for related fields. |

**Operators**:

| Operator | SQL | Example |
|----------|-----|---------|
| `eq` | `=` | `filter=status;eq;published` |
| `neq` | `!=` | `filter=status;neq;draft` |
| `contains` | `LIKE %...%` | `filter=title;contains;hello` |
| `gt` | `>` | `filter=created_at;gt;2025-01-01` |
| `gte` | `>=` | `filter=page_size;gte;10` |
| `lt` | `<` | `filter=created_at;lt;2025-06-01` |
| `lte` | `<=` | `filter=page_size;lte;50` |
| `in` | `IN (...)` | `filter=status;in;draft,published` |
| `between` | `BETWEEN` | `filter=created_at;between;2025-01-01,2025-06-01` |

Related field examples:
```
GET /api/posts?filter=category.name;eq;news
GET /api/posts?filter=author.name;contains;Jane
GET /api/posts?filter=category.name;in;news,tutorials
```

Multiple filters combine with `AND`:
```
GET /api/posts?filter=status;eq;published&filter=category.name;contains;tech
```

---

## 5. Role-Based Access Control

| Action | Admin | Editor | Author |
|--------|-------|--------|--------|
| Manage users | ✅ | ❌ | ❌ |
| Manage settings | ✅ | ❌ | ❌ |
| Manage pages | ✅ | ✅ | ❌ |
| Manage posts (all) | ✅ | ✅ | ❌ |
| Manage own posts | ✅ | ✅ | ✅ |
| Manage categories/tags | ✅ | ✅ | ❌ |
| Manage media | ✅ | ✅ | ✅ |
| Manage menus | ✅ | ✅ | ❌ |
| Publish content | ✅ | ✅ | ❌ |
| Delete content | ✅ | ✅ | ❌ |

---

## 6. Admin Panel (Vue 3 SPA) — Pages

| Route | Component | Description |
|-------|-----------|-------------|
| /login | LoginView | Auth form |
| / | DashboardView | Stats, recent activity |
| /pages | PageListView | Table of pages (sort, filter) |
| /pages/create | PageFormView | Create page |
| /pages/:id/edit | PageFormView | Edit page |
| /posts | PostListView | Table of posts |
| /posts/create | PostFormView | Create post |
| /posts/:id/edit | PostFormView | Edit post |
| /categories | CategoryListView | CRUD categories |
| /tags | TagListView | CRUD tags |
| /media | MediaLibraryView | Grid uploader, manager |
| /menus | MenuListView | List + manage menus |
| /menus/:id/edit | MenuBuilderView | Drag-drop menu builder |
| /settings | SettingsView | Site settings form |
| /users | UserListView | User management |
| /users/:id/edit | UserFormView | Edit user |

---

## 7. Public Site (Nuxt 3) — Pages

| Route | Component | Description |
|-------|-----------|-------------|
| / | HomePage | Latest posts, hero |
| /pages/{slug} | PageDetail | Renders a page |
| /blog | BlogList | Paginated posts grid |
| /blog/{slug} | PostDetail | Full post with sidebar |
| /category/{slug} | CategoryPosts | Posts filtered by category |
| /tag/{slug} | TagPosts | Posts filtered by tag |
| /search | SearchResults | Full-text search |
| /404 | NotFound | Custom 404 |

---

## 8. Tech Stack Summary

| Component | Technology |
|-----------|-----------|
| Backend framework | Laravel 12 |
| API auth | JWT (tymon/jwt-auth, Bearer tokens) |
| Database | MySQL |
| Admin frontend | Vue 3 + TypeScript + Composition API + `<script setup>` |
| Admin state | Pinia |
| Admin UI | PrimeVue + Tailwind CSS |
| Admin build | Vite |
| Public frontend | Nuxt 3 |
| Public UI | PrimeVue + Tailwind CSS |
| Public build | Vite / Nitro |
| HTTP client | Axios |
| Validation (frontend) | VeeValidate + Zod |
| Validation (backend) | Laravel Form Requests |
| Image handling | Spatie Media Library (or manual upload to storage) |
| Testing (PHP) | Pest |
| Testing (JS) | Vitest |
| Code style (PHP) | Laravel Pint |
| Code style (JS) | ESLint + Prettier |
