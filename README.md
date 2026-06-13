# SkilledTalk — Professional Skills Marketplace & Social Network

> **Laravel 8 + Livewire** platform combining a LinkedIn-style professional network with a skills-based consultation marketplace. Professionals list their skills, accept paid video consultations via Twilio, and monetize through subscriptions and a built-in wallet system.

![Laravel](https://img.shields.io/badge/Laravel-8-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-2.8-FB70A9?style=flat-square)
![Pusher](https://img.shields.io/badge/Pusher-Real--time-300D4F?style=flat-square&logo=pusher&logoColor=white)
![Stripe](https://img.shields.io/badge/Stripe-7.x-635BFF?style=flat-square&logo=stripe&logoColor=white)
![Twilio](https://img.shields.io/badge/Twilio-Video-F22F46?style=flat-square&logo=twilio&logoColor=white)

---

## Table of Contents
- [What is SkilledTalk?](#what-is-skilledtalk)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Route Structure](#route-structure)
- [Database Schema](#database-schema)
- [Real-time Features](#real-time-features)
- [Consultation & Video Calling](#consultation--video-calling)
- [Wallet & Payments](#wallet--payments)
- [Mobile API](#mobile-api)
- [Getting Started](#getting-started)

---

## What is SkilledTalk?

SkilledTalk is a professional marketplace where:
- **Professionals** create profiles with tagged skills, post content, and offer paid consultation slots
- **Users** discover professionals by skill, follow them, engage with posts, and book video consultations
- **Groups & Pages** enable community building around topics and professional brands
- **Wallet system** manages earnings, withdrawals, and subscription payments

---

## Tech Stack

| Package | Version | Purpose |
|---|---|---|
| `laravel/framework` | ^8.0 | Core framework |
| `livewire/livewire` | ^2.8 | Reactive UI (feed, comments, real-time updates) |
| `pusher/pusher-php-server` | ^4.1.5 | WebSocket broadcasting (chat, notifications) |
| `stripe/stripe-php` | ^7.0 | Payment processing + subscription billing |
| `twilio/sdk` | ^6.0 | SMS OTP verification + video consultation rooms |
| `laravel/sanctum` | ^2.9 | API token authentication (mobile API) |
| `laravel/breeze` | ^1.1 | Auth scaffolding |
| Bootstrap | 5.x | Frontend CSS framework |
| MySQL | — | Primary database |

---

## Architecture

```
Web Application (Laravel + Livewire)
    |
    +-- /home, /discover     — Social feed, professional discovery
    +-- /inbox               — Real-time chat (Pusher + Laravel Echo)
    +-- /post/*              — Post CRUD, reactions, comments
    +-- /group/*             — Group communities
    +-- /job/*               — Job board
    +-- /wallet/*            — Stripe payments, withdrawal requests
    +-- /subscription        — Subscription plan management
    |
Mobile API (Laravel Sanctum)
    |
    +-- /api/user/*          — Auth, profile, OTP (Twilio SMS)
    +-- /api/post/*          — Feed, reactions, comments
    +-- /api/jobs/           — Job listings
    +-- /api/fellow/*        — Connections
    +-- /api/engagements/    — Engagement tracking

Real-time: Pusher + Laravel Echo
    -> Chat messages
    -> Video call invites
    -> Notifications
```

---

## Route Structure

**Social Feed:**
```
GET  /home                    — Main feed (Livewire)
GET  /discover                — Discover professionals by skill
```

**Profile:**
```
GET  /profile                 — Own profile
POST /profile/update          — Update profile info
GET|POST /experience/*        — Work experience management
GET  /user/profile/{id}       — View another user's profile
POST /user/addSkill           — Tag skills to profile
GET  /search, /search/skills  — Full-text search
```

**Posts:**
```
POST /post/create             — Create post (text + media)
GET|POST /post/{id}/edit      — Edit post
DELETE /post/delete           — Delete post
POST /post/repost             — Repost
POST /post/rate               — React (like) to a post
POST /post/reflect            — Comment on a post
POST /post/save               — Save post to bookmarks
```

**Messaging & Video Calls:**
```
GET  /inbox/{id?}             — Real-time chat inbox (Pusher)
POST /inbox/sendMessage       — Send message
POST /inbox/setMeeting        — Schedule video consultation
POST /inbox/AcceptRejectMeeting — Accept/reject meeting request
GET  /user/calling/{meeting_id} — Join Twilio video room
```

**Groups & Pages:**
```
GET|POST /group/list          — Browse / create groups
POST /group/join, /group/leave
GET|POST /page/*              — Business/professional pages
POST /page/follow, /page/unfollow
```

**Jobs:**
```
GET  /job/*                   — Browse job listings
POST /job/apply               — Apply to a job post
```

**Wallet & Subscriptions:**
```
GET  /wallet                  — Balance overview
POST /wallet/withdraw         — Request withdrawal
POST /wallet/ebank            — Link e-bank account
GET  /subscription            — Subscription plans
POST /unsubscribe             — Cancel subscription
```

---

## Database Schema (48 Migrations)

**Users & Profiles:**
| Table | Key Columns |
|---|---|
| `users` | id, name, email, password, otp, otp_verified |
| `profiles` | user_id, bio, headline, location, avatar, cover |
| `skills` | id, name |
| `custom_fields` | user_id, field_key, field_value |
| `employee_types` | id, name |

**Social:**
| Table | Key Columns |
|---|---|
| `posts` | id, user_id, content, media, post_type (post/job/repost) |
| `post_media` | id, post_id, file_path, media_type |
| `reflections` | id, post_id, user_id, content (comments) |
| `rates` | id, post_id, user_id (reactions/likes) |
| `user_save_posts` | user_id, post_id |
| `connections` | id, requester_id, receiver_id, status |
| `friends` | user_id, friend_id (accepted connections) |
| `messages` | id, from_id, to_id, message, read_at |
| `notifications` | id, user_id, type, data, read_at |

**Consultations:**
| Table | Key Columns |
|---|---|
| `consultations` | id, consult_from (UUID), consult_with (UUID), consult_type, date, time, meeting_id, is_accepted |

**Groups & Pages:**
| Table | Key Columns |
|---|---|
| `groups` | id, name, description, creator_id, privacy |
| `group_users` | group_id, user_id, role |
| `pages` | id, name, category, owner_id |
| `page_followers` | page_id, user_id |

**Jobs:**
| Table | Key Columns |
|---|---|
| `post_jobs` | id, post_id, title, location, salary, type (linked to posts) |
| `job_applicants` | job_id, user_id, cover_letter |

**Financials:**
| Table | Key Columns |
|---|---|
| `subscriptions` | id, title, month, price |
| `user_subscriptions` | user_id, subscription_id, expires_at, stripe_subscription_id |
| `withdrawals` | id, user_id, price, is_approved |
| `user_ebank_mail` | user_id, ebank_id, account_info |
| `ebank` | id, name (banking partners) |
| `ads` | id, owner_id, target, budget |

---

## Real-time Features

**Broadcasting driver:** Pusher + Laravel Echo on the frontend.

| Feature | Channel Type | Event |
|---|---|---|
| New chat message | Private `chat.{user_id}` | `MessageSent` |
| Video call invite | Private `call.{user_id}` | `CallInitiated` |
| Meeting accepted/rejected | Private | `MeetingUpdated` |
| Notifications | Private `notifications.{user_id}` | `NewNotification` |

Livewire handles reactive UI updates (feed pagination, live comment counts, post creation) without page reload.

---

## Consultation & Video Calling

```
1. Professional lists available times in their profile

2. User initiates meeting:
   POST /inbox/setMeeting
   -> Create consultations record (status: pending, meeting_id: UUID)
   -> Broadcast CallInitiated event via Pusher

3. Professional responds:
   POST /inbox/AcceptRejectMeeting
   -> is_accepted = true/false
   -> Broadcast MeetingUpdated event

4. If accepted — both parties join:
   GET /user/calling/{meeting_id}
   -> Twilio SDK generates room token
   -> Join Twilio Video room (meeting_id = room name)
   -> WebRTC video/audio session

5. Session ends -> earnings credited to professional wallet
```

**OTP verification:** `twilio/sdk` sends SMS OTP on registration. `users.otp` + `users.otp_verified` columns track state.

---

## Wallet & Payments

**Earning:** Professionals earn from consultations booked through the platform.

**Subscription billing:** Stripe handles subscription plans. `user_subscriptions` table tracks active plans with Stripe subscription IDs.

**Withdrawals:**
1. Professional requests withdrawal (`POST /wallet/withdraw`)
2. Links e-bank account (`POST /wallet/ebank`)
3. Admin reviews and approves via `skilledtalk-admin`
4. `withdrawals.is_approved` set to true

---

## Mobile API

Full REST API for mobile clients, authenticated via Laravel Sanctum tokens.

| Endpoint | Description |
|---|---|
| `POST /api/user/register` | Register + send OTP |
| `POST /api/user/verify-otp` | Verify OTP → activate account |
| `POST /api/user/login` | Token issuance |
| `POST /api/user/logout` | Revoke token |
| `GET|POST /api/user/profile` | View/update profile |
| `GET /api/post/feed` | Paginated post feed |
| `POST /api/post/create` | Create post |
| `POST /api/post/rate` | React to post |
| `POST /api/post/reflect` | Comment on post |
| `GET /api/jobs/` | Job listings |
| `GET /api/fellow/myFellows` | Connections list |
| `GET /api/engagements/` | Engagement analytics |

---

## Getting Started

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

**Required environment variables:**
```env
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=

STRIPE_KEY=
STRIPE_SECRET=

TWILIO_SID=
TWILIO_TOKEN=
TWILIO_FROM=

BROADCAST_DRIVER=pusher
```

## Related Repositories

| Repo | Purpose |
|---|---|
| `skilledtalk-admin` | Admin panel for platform management |
