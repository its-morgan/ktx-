---
name: auth
description: "Skill for the Auth area of hethongquanlyktx. 19 symbols across 14 files."
---

# Auth

19 symbols | 14 files | Cohesion: 100%

## When to Use

- Working with code in `tests/`
- Understanding how TestCase, ProfileTest, HopdongTest work
- Modifying auth-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `tests/Feature/Auth/EmailVerificationTest.php` | EmailVerificationTest, test_email_verification_screen_can_be_rendered, test_email_can_be_verified, test_email_is_not_verified_with_invalid_hash |
| `app/Http/Requests/Auth/LoginRequest.php` | authenticate, ensureIsNotRateLimited, throttleKey |
| `tests/TestCase.php` | TestCase |
| `tests/Feature/ProfileTest.php` | ProfileTest |
| `tests/Feature/HopdongTest.php` | HopdongTest |
| `tests/Feature/ExampleTest.php` | ExampleTest |
| `tests/Unit/ExampleTest.php` | ExampleTest |
| `tests/Feature/Auth/RegistrationTest.php` | RegistrationTest |
| `tests/Feature/Auth/PasswordUpdateTest.php` | PasswordUpdateTest |
| `tests/Feature/Auth/PasswordResetTest.php` | PasswordResetTest |

## Entry Points

Start here when exploring this area:

- **`TestCase`** (Class) — `tests/TestCase.php:6`
- **`ProfileTest`** (Class) — `tests/Feature/ProfileTest.php:8`
- **`HopdongTest`** (Class) — `tests/Feature/HopdongTest.php:12`
- **`ExampleTest`** (Class) — `tests/Feature/ExampleTest.php:7`
- **`ExampleTest`** (Class) — `tests/Unit/ExampleTest.php:6`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `TestCase` | Class | `tests/TestCase.php` | 6 |
| `ProfileTest` | Class | `tests/Feature/ProfileTest.php` | 8 |
| `HopdongTest` | Class | `tests/Feature/HopdongTest.php` | 12 |
| `ExampleTest` | Class | `tests/Feature/ExampleTest.php` | 7 |
| `ExampleTest` | Class | `tests/Unit/ExampleTest.php` | 6 |
| `RegistrationTest` | Class | `tests/Feature/Auth/RegistrationTest.php` | 7 |
| `PasswordUpdateTest` | Class | `tests/Feature/Auth/PasswordUpdateTest.php` | 9 |
| `PasswordResetTest` | Class | `tests/Feature/Auth/PasswordResetTest.php` | 10 |
| `PasswordConfirmationTest` | Class | `tests/Feature/Auth/PasswordConfirmationTest.php` | 8 |
| `EmailVerificationTest` | Class | `tests/Feature/Auth/EmailVerificationTest.php` | 11 |
| `AuthenticationTest` | Class | `tests/Feature/Auth/AuthenticationTest.php` | 8 |
| `unverified` | Method | `database/factories/UserFactory.php` | 37 |
| `test_email_verification_screen_can_be_rendered` | Method | `tests/Feature/Auth/EmailVerificationTest.php` | 15 |
| `test_email_can_be_verified` | Method | `tests/Feature/Auth/EmailVerificationTest.php` | 24 |
| `test_email_is_not_verified_with_invalid_hash` | Method | `tests/Feature/Auth/EmailVerificationTest.php` | 43 |
| `authenticate` | Method | `app/Http/Requests/Auth/LoginRequest.php` | 40 |
| `ensureIsNotRateLimited` | Method | `app/Http/Requests/Auth/LoginRequest.php` | 60 |
| `throttleKey` | Method | `app/Http/Requests/Auth/LoginRequest.php` | 81 |
| `store` | Method | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | 24 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `Store → ThrottleKey` | intra_community | 4 |

## How to Explore

1. `gitnexus_context({name: "TestCase"})` — see callers and callees
2. `gitnexus_query({query: "auth"})` — find related execution flows
3. Read key files listed above for implementation details
