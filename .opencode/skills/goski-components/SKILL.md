---
name: goski-components
description: Blade + Tailwind CSS v4 component conventions for GOSKI Laravel
---
# GOSKI Components — Blade & Styling Conventions

## Stack
- **Blade** templates with Tailwind CSS v4
- Alpine.js for interactivity (when needed)
- Components live in `resources/views/components/`

## Color palette
| Context | Light | Dark |
|---------|-------|------|
| Body background | `bg-zinc-50` or CSS var `--bg-body` | `bg-zinc-900` or `var(--bg-body)` |
| Card/Modal background | `bg-white` | `dark:bg-zinc-950` |
| Primary text | `text-zinc-900` | `dark:text-white` |
| Secondary text | `text-zinc-400` / `text-gray-400` | `dark:text-zinc-500` |
| Borders | `border-zinc-200` | `dark:border-zinc-800` |
| Input background | `bg-white` / `var(--bg-input)` | `dark:bg-zinc-900` |
| Unread/highlight | `bg-zinc-50` | `dark:bg-zinc-900` |
| Red (destructive) | `text-red-500` / `bg-red-50` | `dark:bg-red-900/30` |
| Green (success) | `text-green-600` / `bg-green-50` | `dark:bg-green-900/30` |

## Typography
| Element | Classes |
|---------|---------|
| Title | `font-black text-sm uppercase tracking-tight` |
| Body | `text-[10px]` or `text-xs` |
| Timestamp | `text-[9px] font-bold uppercase tracking-tight` |
| Button label | `text-[10px] font-black uppercase tracking-tight` or `text-sm font-bold` |
| Username link | `font-black text-[10px] uppercase tracking-tight hover:underline` |
| Error message | `text-red-500 text-[10px] font-bold uppercase tracking-tight` |
| Placeholder | `placeholder:text-zinc-400` |

## Border radiuses
| Element | Radius |
|---------|--------|
| Cards | `rounded-xl` |
| Buttons | `rounded-xl` |
| Inputs | `rounded-xl` |
| Modals | `rounded-xl` |
| Avatars | `rounded-full` |

## Dark mode
- Always use `dark:` prefix variants on ALL color classes
- Use CSS custom properties via `var(--bg-*)` for theme-agnostic backgrounds
- Never hardcode colors without a dark mode alternative

## Component patterns

### Anonymous components (`resources/views/components/my-component.blade.php`)
```html
@props(['propName' => 'default'])
<div class="...">
    {{ $slot ?? '' }}
</div>
```

### Named/x-components
Used with `<x-my-component />` syntax in Blade.

### Modals
```html
<div id="my-modal" class="fixed inset-0 z-100 hidden items-center justify-center bg-[var(--bg-overlay)] backdrop-blur-sm transition-opacity">
    <div class="relative w-full max-w-md bg-[var(--bg-card)] dark:bg-zinc-950 rounded-xl shadow-2xl overflow-hidden mx-4" @click.stop>
        <!-- header -->
        <!-- content -->
    </div>
</div>
```

### Cards
```html
<div class="bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
    <!-- card content -->
</div>
```

## Icons
- Inline SVGs with `currentColor` for fill/stroke
- Use `w-4 h-4` (16px) or `w-5 h-5` (20px) sizing
- Always set `fill="none"` with `stroke="currentColor"` for outlined icons

## Utility classes used in the project
- `font-black` — heaviest font weight
- `tracking-tight` / `tracking-tighter` — letter spacing
- `rounded-xl` — 12px border radius (Tailwind v4)
- `backdrop-blur-sm` — frosted glass effect
- `transition-all` / `transition-colors` — animations
- `active:scale-[0.98]` — button press effect
- `group` / `group-hover:` — parent-child hover states
