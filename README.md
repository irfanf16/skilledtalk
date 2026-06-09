# SkilledTalk

A **LinkedIn-inspired professional skills marketplace and social network** built with Laravel 8 + Livewire. Users showcase skills, find jobs, post content, connect with peers, book paid consultations, and transact via an e-wallet.

![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat&logo=php)
![Livewire](https://img.shields.io/badge/Livewire-2.x-FB70A9?style=flat)
![Sanctum](https://img.shields.io/badge/Sanctum-2.9-FF2D20?style=flat&logo=laravel)
![Pusher](https://img.shields.io/badge/Pusher-4.x-300D4F?style=flat&logo=pusher)
![Stripe](https://img.shields.io/badge/Stripe-7.x-008CDD?style=flat&logo=stripe)
![Twilio](https://img.shields.io/badge/Twilio-6.x-F22F46?style=flat&logo=twilio)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql)

## Features

- **Auth** — Email/password with Twilio SMS OTP verification, email verification, password reset
- **Social Feed** — Create/edit/delete/repost posts; reactions (rate/reflect); saved posts; hashtag support; post privacy; media attachments
- **Connections** — Send/accept/reject friend requests, unfriend, view friend list
- **Messaging** — Real-time inbox, send messages, video meeting scheduling (Pusher)
- **Job Board** — Browse and apply to job posts; post jobs with salary range, required skills, and experience levels
- **Paid Consultations** — Book sessions at set `session_price`; attach files; accept/reject with Zoom-style meeting IDs
- **User Profiles** — Headline, current position, education, industry, skills, work history, profile/banner images
- **Pages** — Create business/company pages with followers
- **Groups** — Create, join, leave, and post within groups
- **E-Wallet** — View balance, add bank accounts, submit withdrawals
- **Subscriptions** — Tiered plans with monthly pricing
- **Mobile API** — Sanctum-protected REST API for auth, profile, posts, experience, jobs, fellows

## Database Schema

| Table | Key Columns | Purpose |
|---|---|---|
| `users` | `id` (UUID), `firstname`, `lastname`, `email`, `phone`, `headline`, `current_position`, `session_price`, `rating`, `otp` | User profiles |
| `posts` | `id` (UUID), `user_id`, `post_type_id`, `post_privacy_id`, `heading`, `description`, `hashtags`, `view_count` | Feed posts |
| `post_jobs` | `post_id`, `job_title`, `company`, `skills`, `salary_from`, `salary_to`, `employee_type_id` | Job listings |
| `friends` | `request_from` (UUID), `request_to` (UUID), `is_accepted` | Connection requests |
| `messages` | `connection_id`, `send_from`, `send_to`, `type`, `text`, `meeting_id` | Chat messages |
| `consultations` | `consult_from`, `consult_with`, `consult_type`, `date`, `time`, `price`, `meeting_id` | Paid consultations |
| `pages` | `user_id`, `name`, `public_url`, `industry`, `company_size` | Business pages |
| `groups` / `group_users` | `name`, `about`, group membership | Groups |
| `skills` | `user_id` (UUID), `name` | User skills |
| `subscriptions` | `title`, `month`, `price` | Subscription tiers |
| `user_subscriptions` | `user_id`, `subscription_id` | Active subscriptions |

## Getting Started

```bash
composer install && npm install && npm run dev
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan storage:link
php artisan serve
```

## Environment Variables

```env
PUSHER_APP_ID=    PUSHER_APP_KEY=    PUSHER_APP_SECRET=
STRIPE_KEY=       STRIPE_SECRET=
TWILIO_SID=       TWILIO_TOKEN=      TWILIO_FROM=
```

## License
MIT
