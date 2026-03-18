# EventPass — Complete CRUD Operations Documentation

> **Stack:** Laravel 13 · PHP 8.4 · MySQL · Blade + Tailwind CSS

---

## Table of Contents
1. [System Architecture](#1-system-architecture)
2. [Database Schema & Relationships](#2-database-schema--relationships)
3. [Request Lifecycle](#3-request-lifecycle)
4. [Routes Map](#4-routes-map)
5. [Middleware Chain](#5-middleware-chain)
6. [Model Relationships (Code)](#6-model-relationships)
7. [CREATE an Event](#7-create-an-event)
8. [READ Events](#8-read-events)
9. [UPDATE an Event](#9-update-an-event)
10. [DELETE an Event (Soft Delete)](#10-delete-an-event)
11. [BOOK a Ticket](#11-book-a-ticket)
12. [CANCEL a Ticket](#12-cancel-a-ticket)
13. [Admin CRUD Panel](#13-admin-crud-panel)
14. [File & Folder Structure](#14-file--folder-structure)

---

## 1. System Architecture

```mermaid
graph TD
    Browser["🌐 Browser"] -->|HTTP Request| Entry["public/index.php"]
    Entry --> Boot["bootstrap/app.php\n(Middleware aliases, route files)"]
    Boot --> Router["Laravel Router\nroutes/web.php\nroutes/admin.php"]
    Router --> MW["Middleware Stack\nweb · auth · event.owner · admin"]
    MW --> Ctrl["Controller\n(Business Logic)"]
    Ctrl --> FR["Form Request\n(Validation)"]
    FR --> Model["Eloquent Model\n(ORM)"]
    Model --> DB[("MySQL Database")]
    Ctrl --> Resource["API Resource\n(JSON transform)"]
    Ctrl --> View["Blade View\n(HTML Response)"]
    View --> Browser
```

---

## 2. Database Schema & Relationships

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email
        string password
        text bio
        string profile_picture
        boolean is_admin
        timestamp created_at
    }

    categories {
        bigint id PK
        string name
        string slug
    }

    events {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        string title
        text description
        date date
        time time
        string location
        integer available_tickets
        string poster_image
        timestamp deleted_at
        timestamp created_at
    }

    tags {
        bigint id PK
        string name
        string slug
    }

    event_tag {
        bigint event_id FK
        bigint tag_id FK
    }

    bookings {
        bigint id PK
        bigint user_id FK
        bigint event_id FK
        timestamp created_at
    }

    users ||--o{ events : "creates (user_id)"
    users ||--o{ bookings : "books (user_id)"
    categories ||--o{ events : "categorizes (category_id)"
    events ||--o{ bookings : "has bookings (event_id)"
    events }o--o{ tags : "event_tag pivot"
```

> **Key:** `deleted_at` on [events](file:///e:/PROJECTS/php_project-1/crud/app/Models/Tag.php#12-16) enables **Soft Deletes** — records are never physically removed until an admin force-deletes them.

---

## 3. Request Lifecycle

```mermaid
sequenceDiagram
    participant B as 🌐 Browser
    participant R as Router
    participant M as Middleware
    participant FR as Form Request
    participant C as Controller
    participant E as Eloquent Model
    participant D as MySQL DB
    participant V as Blade View

    B->>R: HTTP Request (GET/POST/PUT/DELETE)
    R->>M: Match route, run middleware stack
    M-->>R: ✅ Pass  OR  ❌ abort(403/401)
    R->>FR: (POST/PUT only) Validate data
    FR-->>R: ✅ Validated data  OR  ❌ redirect + errors
    R->>C: Call controller method
    C->>E: Query / create / update / delete
    E->>D: SQL (SELECT / INSERT / UPDATE / DELETE)
    D-->>E: Results
    E-->>C: Eloquent Collection / Model
    C->>V: Return view with data
    V-->>B: HTML Response
```

---

## 4. Routes Map

```mermaid
graph LR
    subgraph Public["🟢 Public (no auth needed)"]
        R1["GET /\n→ redirect to /events"]
        R2["GET /events\n→ events.index"]
        R3["GET /events/{event}\n→ events.show"]
        R4["GET /api/events\n→ EventResource JSON"]
    end

    subgraph Auth["🔵 Auth Required"]
        R5["GET  /events/create\n→ events.create"]
        R6["POST /events\n→ events.store"]
        R7["GET  /dashboard\n→ dashboard"]
        R8["GET  /profile\n→ profile.edit"]
        R9["PATCH /profile\n→ profile.update"]
        R10["POST /events/{event}/book\n→ bookings.store"]
        R11["DELETE /events/{event}/book\n→ bookings.destroy"]
    end

    subgraph Owner["🟡 Auth + Event Owner"]
        R12["GET    /events/{event}/edit\n→ events.edit"]
        R13["PUT    /events/{event}\n→ events.update"]
        R14["DELETE /events/{event}\n→ events.destroy"]
    end

    subgraph Admin["🔴 Auth + Admin"]
        R15["GET /admin\n→ admin.dashboard"]
        R16["GET /admin/users\n→ admin.users.index"]
        R17["GET /admin/events\n→ admin.events.index"]
        R18["GET /admin/bookings\n→ admin.bookings.index"]
        R19["GET /admin/categories\n→ admin.categories.index"]
        R20["GET /admin/tags\n→ admin.tags.index"]
    end
```

**Route ordering rule:** `/events/create` is declared **before** `/events/{event}` in [routes/web.php](file:///e:/PROJECTS/php_project-1/crud/routes/web.php). Without this, Laravel would match [create](file:///e:/PROJECTS/php_project-1/crud/app/Http/Controllers/EventController.php#23-29) as the `{event}` wildcard (ID = "create") → 404.

---

## 5. Middleware Chain

```mermaid
flowchart LR
    Req["Request"] --> web["web middleware\n(sessions, cookies,\nCSRF, errors)"]
    web --> auth{"auth\nmiddleware"}
    auth -->|Not logged in| Login["→ /login"]
    auth -->|Logged in| next1["Next"]

    next1 --> owner{"event.owner\n(EnsureUserOwnsEvent)"}
    owner -->|user_id ≠ auth id| A403["abort(403)"]
    owner -->|user_id = auth id| next2["Next"]

    next2 --> admin{"admin\n(EnsureUserIsAdmin)"}
    admin -->|is_admin = false| B403["abort(403)"]
    admin -->|is_admin = true| Controller["Controller runs ✅"]
```

### [EnsureUserOwnsEvent.php](file:///e:/PROJECTS/php_project-1/crud/app/Http/Middleware/EnsureUserOwnsEvent.php) — Actual Code
```php
public function handle(Request $request, Closure $next): Response
{
    $event = $request->route('event');

    if (!$event instanceof Event) {
        $event = Event::findOrFail($event);
    }

    if ($event->user_id !== auth()->id()) {
        abort(403, 'Unauthorized. Only the event owner can do this.');
    }

    return $next($request);
}
```

### [EnsureUserIsAdmin.php](file:///e:/PROJECTS/php_project-1/crud/app/Http/Middleware/EnsureUserIsAdmin.php) — Actual Code
```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check() || !auth()->user()->is_admin) {
        abort(403, 'Access denied. Admins only.');
    }
    return $next($request);
}
```

---

## 6. Model Relationships

```mermaid
classDiagram
    class User {
        +id, name, email
        +bio, profile_picture
        +is_admin: boolean
        +hasMany(Event)
        +hasMany(Booking)
    }

    class Event {
        +id, title, description
        +date, time, location
        +available_tickets
        +poster_image
        +deleted_at  ← SoftDeletes
        +belongsTo(User)
        +belongsTo(Category)
        +belongsToMany(Tag)
        +hasMany(Booking)
    }

    class Category {
        +id, name, slug
        +hasMany(Event)
    }

    class Tag {
        +id, name, slug
        +belongsToMany(Event)
    }

    class Booking {
        +id, user_id, event_id
        +belongsTo(User)
        +belongsTo(Event)
    }

    User "1" --> "*" Event : creates
    User "1" --> "*" Booking : places
    Category "1" --> "*" Event : categorizes
    Event "*" --> "*" Tag : via event_tag pivot
    Event "1" --> "*" Booking : has
```

---

## 7. CREATE an Event

### Flow Diagram

```mermaid
flowchart TD
    A["User visits\nGET /events/create"] --> B{"auth\nmiddleware"}
    B -->|Not logged in| C["→ /login"]
    B -->|Logged in| D["EventController@create\nloads Categories + Tags"]
    D --> E["Blade: events/create.blade.php\n(form rendered)"]
    E --> F["User fills form & submits\nPOST /events"]
    F --> G["StoreEventRequest\nvalidates all fields"]
    G -->|Fails| H["Redirect back\nwith $errors"]
    G -->|Passes| I["EventController@store"]
    I --> J{"poster_image\nuploaded?"}
    J -->|Yes| K["Storage::disk('public')\n->store('posters')\nSaves to storage/app/public/posters/"]
    J -->|No| L["Skip"]
    K --> M["Event::create(data + user_id)"]
    L --> M
    M --> N["INSERT into events table"]
    N --> O["$event->tags()->sync(tagIds)\nINSERT into event_tag pivot"]
    O --> P["redirect → events.show\n+ 'success' flash"]
```

### Validation Rules ([StoreEventRequest](file:///e:/PROJECTS/php_project-1/crud/app/Http/Requests/StoreEventRequest.php#7-30))

| Field | Rule |
|---|---|
| `title` | `required\|string\|max:255` |
| `description` | `required\|string` |
| [date](file:///e:/PROJECTS/php_project-1/crud/app/Http/Controllers/EventController.php#64-81) | `required\|date\|after_or_equal:today` |
| `time` | `required` |
| `location` | `required\|string\|max:255` |
| `available_tickets` | `required\|integer\|min:1` |
| `category_id` | `required\|exists:categories,id` |
| `tags.*` | `exists:tags,id` |
| `poster_image` | `nullable\|image\|mimes:jpg,jpeg,png,webp\|max:2048` |

### Actual Controller Code
```php
// EventController@store
public function store(StoreEventRequest $request)
{
    $data = $request->validated();

    if ($request->hasFile('poster_image')) {
        $data['poster_image'] = $request->file('poster_image')
                                        ->store('posters', 'public');
    }

    $data['user_id'] = auth()->id();
    $event = Event::create($data);
    $event->tags()->sync($request->input('tags', []));

    return redirect()->route('events.show', $event)
                     ->with('success', 'Event created successfully!');
}
```

---

## 8. READ Events

### Index Page (Listing)

```mermaid
flowchart LR
    A["GET /events"] --> B["EventController@index"]
    B --> C["Event::with(['category','user','tags'])\n→ Eager loads 3 relationships\n→ paginate(9)"]
    C --> D["SELECT *\nJOIN categories\nJOIN users\nJOIN tags\n(4 queries, no N+1)"]
    D --> E["events/index.blade.php\nCard grid with pagination"]
```

### Show Page (Single Event)

```mermaid
flowchart LR
    A["GET /events/{event}"] --> B["EventController@show"]
    B --> C["Route Model Binding\nLaravel auto-fetches Event by ID"]
    C --> D["$event->load(['category','user','tags','bookings'])"]
    D --> E{"auth()->check()"}
    E -->|Logged in| F["Check: bookings where user_id = auth()->id()"]
    E -->|Guest| G["$hasBooked = false"]
    F --> H["$hasBooked = true/false"]
    G --> I["events/show.blade.php"]
    H --> I
    I --> J{"$hasBooked?"}
    J -->|true| K["Show ✅ You have a ticket\n+ ❌ Cancel button"]
    J -->|false + tickets > 0| L["Show 🎟️ Book button"]
    J -->|false + tickets = 0| M["Show 🔴 Sold Out"]
```

### Actual Controller Code
```php
// EventController@index
public function index()
{
    $events = Event::with(['category', 'user', 'tags'])
        ->latest()
        ->paginate(9);
    return view('events.index', compact('events'));
}

// EventController@show
public function show(Event $event)
{
    $event->load(['category', 'user', 'tags', 'bookings']);
    $hasBooked = auth()->check()
        ? $event->bookings()->where('user_id', auth()->id())->exists()
        : false;
    return view('events.show', compact('event', 'hasBooked'));
}
```

---

## 9. UPDATE an Event

```mermaid
flowchart TD
    A["User visits\nGET /events/{event}/edit"] --> B["auth middleware"]
    B --> C{"EnsureUserOwnsEvent\nevent->user_id == auth->id() ?"}
    C -->|No| D["abort(403)"]
    C -->|Yes| E["EventController@edit\nloads categories, tags, selectedTags"]
    E --> F["events/edit.blade.php\n(pre-filled form)"]
    F --> G["User submits\nPUT /events/{event}"]
    G --> H["UpdateEventRequest validates"]
    H -->|Fails| I["Redirect back + errors"]
    H -->|Passes| J["EventController@update"]
    J --> K{"New poster\nuploaded?"}
    K -->|Yes| L["Storage::delete(old poster)\nStorage::store(new poster)"]
    K -->|No| M["Keep existing poster"]
    L --> N["$event->update(validated data)"]
    M --> N
    N --> O["UPDATE events table"]
    O --> P["$event->tags()->sync(tagIds)\nDELETE old from event_tag\nINSERT new into event_tag"]
    P --> Q["redirect → events.show\n+ 'success' flash"]
```

### Actual Controller Code
```php
// EventController@update
public function update(UpdateEventRequest $request, Event $event)
{
    $data = $request->validated();

    if ($request->hasFile('poster_image')) {
        if ($event->poster_image) {
            Storage::disk('public')->delete($event->poster_image);
        }
        $data['poster_image'] = $request->file('poster_image')
                                        ->store('posters', 'public');
    }

    $event->update($data);
    $event->tags()->sync($request->input('tags', []));

    return redirect()->route('events.show', $event)
                     ->with('success', 'Event updated successfully!');
}
```

**How `->sync()` works:**

```
Before: event has tags [1, 3, 5]
User selects:            [2, 3]

sync() does:
  DELETE FROM event_tag WHERE event_id=X AND tag_id IN (1, 5)
  INSERT INTO event_tag (event_id, tag_id) VALUES (X, 2)
  (tag 3 stays unchanged)
```

---

## 10. DELETE an Event

### Soft Delete vs Hard Delete

```mermaid
flowchart TD
    A["User clicks Delete\nDELETE /events/{event}"] --> B["auth + event.owner\nmiddleware"]
    B --> C["EventController@destroy"]
    C --> D["$event->delete()"]
    D --> E["SoftDeletes Trait\nSETs deleted_at = NOW()"]
    E --> F["Event STAYS in DB!"]
    F --> G["Hidden from all normal queries\n(Event::all(), Event::paginate())"]
    F --> H["Visible ONLY to Admin\n(Event::withTrashed())"]

    H --> I{"Admin Action"}
    I -->|"♻️ Restore"| J["$event->restore()\ndeleted_at = NULL\nEvent is back!"]
    I -->|"💥 Force Purge"| K["$event->forceDelete()\nPermanently removed\nfrom database"]
```

### How SoftDeletes Works Internally

```php
// Event Model
class Event extends Model
{
    use SoftDeletes;         // ← Just adding this trait is enough!
    // Laravel automatically:
    // - Adds WHERE deleted_at IS NULL to all queries
    // - Sets deleted_at timestamp on ->delete()
    // - Ignores the record from all relationships
}

// Controller
$event->delete();            // ← Sets deleted_at, NOT a real DELETE
$event->restore();           // ← Clears deleted_at
$event->forceDelete();       // ← Actual DELETE FROM events WHERE id=X
```

### SQL Generated

| Action | SQL |
|---|---|
| `->delete()` | `UPDATE events SET deleted_at = '2026-03-18...' WHERE id = 1` |
| Normal `Event::all()` | `SELECT * FROM events WHERE deleted_at IS NULL` |
| `Event::withTrashed()` | `SELECT * FROM events` (includes deleted) |
| `Event::onlyTrashed()` | `SELECT * FROM events WHERE deleted_at IS NOT NULL` |
| `->restore()` | `UPDATE events SET deleted_at = NULL WHERE id = 1` |
| `->forceDelete()` | `DELETE FROM events WHERE id = 1` |

---

## 11. BOOK a Ticket

```mermaid
flowchart TD
    A["User clicks Book\nPOST /events/{event}/book"] --> B["auth middleware"]
    B --> C["BookingController@store"]
    C --> D{"Already booked?\nbookings WHERE user_id = auth->id()"}
    D -->|Yes| E["redirect back\n'Already booked' error"]
    D -->|No| F["DB::transaction()"]
    F --> G["Event::lockForUpdate()->find(id)\n← Row-level DB lock\nBlocks other transactions"]
    G --> H{"available_tickets <= 0?"}
    H -->|Yes| I["Transaction rolls back\n'Sold out' error"]
    H -->|No| J["$event->decrement('available_tickets')\nUPDATE events SET available_tickets = available_tickets - 1"]
    J --> K["$event->bookings()->create(['user_id' => auth()->id()])\nINSERT INTO bookings"]
    K --> L["Transaction COMMITS\n🎉 'Ticket booked!'"]
```

> **Why `lockForUpdate()`?** Without it, if 2 users book the last ticket simultaneously:
> - Both read `available_tickets = 1` ✅
> - Both decrement → `available_tickets = -1` ❌ (oversold!)
>
> With `lockForUpdate()`, the second user's transaction waits until the first commits, then reads `available_tickets = 0` → returns "Sold out".

### Actual Controller Code
```php
public function store(Request $request, Event $event)
{
    $alreadyBooked = $event->bookings()
                           ->where('user_id', auth()->id())
                           ->exists();
    if ($alreadyBooked) {
        return back()->with('error', 'Already booked!');
    }

    $updated = DB::transaction(function () use ($event) {
        $event = Event::lockForUpdate()->find($event->id);  // ← DB lock

        if ($event->available_tickets <= 0) {
            return false;
        }

        $event->decrement('available_tickets');
        $event->bookings()->create(['user_id' => auth()->id()]);

        return true;
    });

    if (!$updated) {
        return back()->with('error', 'Sorry, no tickets available.');
    }

    return back()->with('success', 'Ticket booked! 🎉');
}
```

---

## 12. CANCEL a Ticket

```mermaid
flowchart TD
    A["User clicks ❌ Cancel\nDELETE /events/{event}/book"] --> B["auth middleware"]
    B --> C["BookingController@destroy"]
    C --> D["Find booking WHERE user_id = auth()->id()"]
    D --> E{"Booking found?"}
    E -->|No| F["redirect back\n'No booking found' error"]
    E -->|Yes| G["DB::transaction()"]
    G --> H["$booking->delete()\nDELETE FROM bookings WHERE id=X"]
    H --> I["$event->increment('available_tickets')\nUPDATE events SET available_tickets = available_tickets + 1"]
    I --> J["Transaction COMMITS\n🔓 'Ticket cancelled, seat returned'"]
```

### Actual Controller Code
```php
public function destroy(Request $request, Event $event)
{
    $booking = $event->bookings()
                     ->where('user_id', auth()->id())
                     ->first();

    if (!$booking) {
        return back()->with('error', 'No booking found.');
    }

    DB::transaction(function () use ($booking, $event) {
        $booking->delete();
        $event->increment('available_tickets');  // ← Seat returned
    });

    return back()->with('success', 'Ticket cancelled. 🔓');
}
```

---

## 13. Admin CRUD Panel

```mermaid
graph TD
    Admin["🛡️ Admin User\n(is_admin = true)"] --> AP["/admin"]

    AP --> D["📊 Dashboard\nStats + recent activity"]
    AP --> U["👥 Users\nList all, toggle admin, delete"]
    AP --> E["🗓️ Events\nAll events incl. deleted\nSoft-delete, Restore, Force-purge"]
    AP --> B["🎫 Bookings\nAll bookings, cancel any"]
    AP --> C["🏷️ Categories\nAdd/delete"]
    AP --> T["🔖 Tags\nAdd/delete"]

    U --> U1["POST /admin/users/{user}/toggle-admin\nis_admin = !is_admin"]
    U --> U2["DELETE /admin/users/{user}\nUser::delete()"]

    E --> E1["DELETE /admin/events/{event}\nSoft delete"]
    E --> E2["POST /admin/events/{id}/restore\n$event->restore()"]
    E --> E3["DELETE /admin/events/{id}/force\n$event->forceDelete()"]

    B --> B1["DELETE /admin/bookings/{booking}\nbooking->delete() + increment tickets"]

    C --> C1["POST /admin/categories\nCategory::create()"]
    C --> C2["DELETE /admin/categories/{category}\nCategory::delete()"]
```

### How Admin Access Works

```php
// 1. Database: users.is_admin column (boolean, default: false)

// 2. Middleware checks
if (!auth()->user()->is_admin) {
    abort(403);
}

// 3. Grant admin (Tinker or Admin → Users page)
User::find(1)->update(['is_admin' => true]);

// 4. Revoke admin (Admin → Users page, click "Revoke Admin")
User::find(2)->update(['is_admin' => false]);
```

---

## 14. File & Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── EventController.php          ← Create, Read, Update, Delete (soft)
│   │   ├── BookingController.php         ← Book ticket, Cancel ticket
│   │   ├── ProfileController.php         ← Profile update + avatar upload
│   │   ├── DashboardController.php       ← User dashboard (my events + bookings)
│   │   └── Admin/
│   │       ├── AdminDashboardController.php
│   │       ├── AdminUserController.php   ← List, toggle-admin, delete
│   │       ├── AdminEventController.php  ← List, soft-delete, restore, purge
│   │       ├── AdminBookingController.php← List, cancel
│   │       ├── AdminCategoryController.php← List, create, delete
│   │       └── AdminTagController.php    ← List, create, delete
│   ├── Middleware/
│   │   ├── EnsureUserOwnsEvent.php       ← Checks event->user_id = auth id
│   │   └── EnsureUserIsAdmin.php         ← Checks user->is_admin = true
│   ├── Requests/
│   │   ├── StoreEventRequest.php         ← Validation for CREATE
│   │   ├── UpdateEventRequest.php        ← Validation for UPDATE
│   │   └── ProfileUpdateRequest.php      ← Validation for profile
│   └── Resources/
│       └── EventResource.php             ← API JSON transformer
├── Models/
│   ├── User.php                          ← hasMany Events, Bookings
│   ├── Event.php                         ← SoftDeletes, belongsTo, hasMany, belongsToMany
│   ├── Category.php                      ← hasMany Events
│   ├── Tag.php                           ← belongsToMany Events
│   └── Booking.php                       ← belongsTo User + Event
├── Providers/
│   └── AppServiceProvider.php            ← Auto-discovered by Laravel
bootstrap/
│   └── app.php                           ← Registers middleware aliases
│                                            Loads routes/admin.php
routes/
│   ├── web.php                           ← All public + auth routes
│   ├── admin.php                         ← All /admin/* routes
│   └── auth.php                          ← Breeze auth routes (login, register, etc.)
database/
│   ├── migrations/
│   │   ├── create_categories_table.php
│   │   ├── create_events_table.php       ← includes deleted_at for SoftDeletes
│   │   ├── create_tags_table.php
│   │   ├── create_event_tag_table.php    ← Many-to-Many pivot
│   │   ├── create_bookings_table.php
│   │   ├── add_bio_and_profile_picture_to_users_table.php
│   │   └── add_is_admin_to_users_table.php
│   └── seeders/
│       └── DatabaseSeeder.php            ← Seeds 6 categories + 7 tags
resources/views/
│   ├── layouts/
│   │   ├── app.blade.php                 ← Breeze main layout
│   │   └── navigation.blade.php          ← Navbar (guest/auth/admin-aware)
│   ├── components/admin/
│   │   └── layout.blade.php              ← Dark sidebar admin layout (<x-admin.layout>)
│   ├── events/
│   │   ├── index.blade.php               ← Card grid + pagination
│   │   ├── show.blade.php                ← Detail, Book/Cancel, Edit/Delete
│   │   ├── create.blade.php              ← Create form (with tags, categories, file upload)
│   │   └── edit.blade.php                ← Edit form (pre-filled)
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── users/index.blade.php
│   │   ├── events/index.blade.php
│   │   ├── bookings/index.blade.php
│   │   ├── categories/index.blade.php
│   │   └── tags/index.blade.php
│   ├── dashboard.blade.php               ← My events + attendees + my tickets
│   └── profile/
│       └── partials/
│           └── update-profile-information-form.blade.php
storage/app/public/
│   ├── posters/                          ← Event poster images
│   └── profile_pictures/                 ← User avatar images
public/storage → ← Symlink to storage/app/public (php artisan storage:link)
```

---

## Summary Table

| Operation | HTTP | URL | Auth | Validation | Special |
|---|---|---|---|---|---|
| List events | GET | `/events` | ❌ Public | — | Eager loading, pagination |
| View event | GET | `/events/{id}` | ❌ Public | — | Route Model Binding |
| Create form | GET | `/events/create` | ✅ auth | — | Loads categories + tags |
| Create event | POST | `/events` | ✅ auth | [StoreEventRequest](file:///e:/PROJECTS/php_project-1/crud/app/Http/Requests/StoreEventRequest.php#7-30) | File upload, tag sync |
| Edit form | GET | `/events/{id}/edit` | ✅ auth + owner | — | Pre-fills form |
| Update event | PUT | `/events/{id}` | ✅ auth + owner | [UpdateEventRequest](file:///e:/PROJECTS/php_project-1/crud/app/Http/Requests/UpdateEventRequest.php#7-30) | Replace image, re-sync tags |
| Delete event | DELETE | `/events/{id}` | ✅ auth + owner | — | **Soft delete** (sets `deleted_at`) |
| Book ticket | POST | `/events/{id}/book` | ✅ auth | — | DB transaction, row lock |
| Cancel ticket | DELETE | `/events/{id}/book` | ✅ auth | — | Restores `available_tickets` |
| Restore event | POST | `/admin/events/{id}/restore` | ✅ admin | — | Clears `deleted_at` |
| Purge event | DELETE | `/admin/events/{id}/force` | ✅ admin | — | **Permanent** `forceDelete()` |
| Toggle admin | POST | `/admin/users/{id}/toggle-admin` | ✅ admin | — | Flips `is_admin` bool |
| API JSON | GET | `/api/events` | ❌ Public | — | [EventResource](file:///e:/PROJECTS/php_project-1/crud/app/Http/Resources/EventResource.php#8-39) transformer |
