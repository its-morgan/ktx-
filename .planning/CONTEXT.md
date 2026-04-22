# Design Contract - Dormitory Management System

This document locks in the visual identity for the project. All UI/UX intelligence (via UI/UX Pro Max) must be anchored to these parameters.

## Brand Identity
- **Name:** Hệ thống quản lý ký túc xá (hethongquanlyktx)
- **Vibe:** Modern, Friendly, Professional, Soft UI with Glassmorphism.
- **Language:** Vietnamese (Accented, professional tone).

## Design Tokens

### Typography
- **Primary/Display Font:** `Quicksand` (Rounded, friendly but legible).
- **Fallback:** `sans-serif`.
- **Scaling:** Uses standard Tailwind spacing/font scales.

### Color Palette
- **Brand Primary:** `Indigo` (Hex values mapped to Tailwind `indigo` 50-900).
- **Neutrals:** `Slate` (shades 50-900) for primary surfaces, `Zinc` for muted accents.
- **Surface Background:** `Slate-50` (base), `White` (panels), `Zinc-100` (muted areas).
- **Ink (Text):** `Slate-900` (primary), `Slate-600` (secondary).

### Aesthetic Accents
- **Glassmorphism:** `backdrop-blur: 12px`, `box-shadow: 0 10px 30px rgb(15 23 42 / 0.08), inset 0 1px 0 rgb(255 255 255 / 0.55)`.
- **Shadows:** `soft` (standard elevation), `float` (high elevation), `glass` (glassmorphism layer).
- **Transitions:** `cubic-bezier(0.22, 1, 0.36, 1)` (Smooth ease-out).

## Component Rules
- **Buttons:** Primary buttons use `brand-600` with `white` text and `soft` shadow.
- **Inputs:** Rounded (`Quicksand` style), focus rings in `brand-500`.
- **Cards:** White or Glass background with `soft` or `glass` shadows.
