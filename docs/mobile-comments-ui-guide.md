# Comments UI — React Native (Mobile) Implementation Guide

This document specifies every visual detail from the Laravel Blade implementation so the mobile team can build pixel-perfect React Native screens.

---

## 1. Comment Button (Inside Post Card)

Located below the post description, to the right of the like button.

### Layout
```
[Like btn] [Comment btn]
```
Both buttons sit in a `flex-row` container with `justify-between` and `px-2 mt-3`.

### Comment Button Specs
| Property | Value |
|---|---|
| Border radius | `rounded-xl` → 12px |
| Padding | `pr-3 py-2` → right: 12px, vertical: 8px |
| Background | Transparent by default, `bg-zinc-100` / `dark:bg-zinc-800` on press |
| Active opacity | `active:scale-95` (95% scale on press, 100ms) |

### Icon (Material "chat bubble" outline)
- Size: `w-6 h-6` → 24×24px
- Color: `text-zinc-900` / `dark:text-zinc-300`
- Fill: currentColor
- Path: `M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z`

### Count Badge
- Font: `text-sm font-black` → 14px, weight 900
- Color: `text-zinc-900` / `dark:text-zinc-300`
- Gap from icon: `gap-2` → 8px

---

## 2. Comments Drawer (Expand/Collapse)

Slides open below the post card's action bar, inside the same card.

### Container
```
Id: comments-section-{postId}
```

| Property | Value |
|---|---|
| Overflow | hidden (content clipped during animation) |
| Transition | `max-height` only, 300ms, `ease-in-out` |
| Initial state | `max-height: 0` (hidden) |
| Open state | `max-height: {scrollHeight}px` (set dynamically after render) |
| Close others | Only one section open at a time; closing others also uses 300ms ease-in-out |

### Animation Logic (JavaScript equivalent)
```js
// Open
section.style.maxHeight = section.scrollHeight + 'px';
// Close
section.style.maxHeight = '0px';
// After content loads (AJAX), recalculate:
requestAnimationFrame(() => {
  section.style.maxHeight = section.scrollHeight + 'px';
});
```

### Divider (top border of the drawer)
- `border-t border-zinc-200` / `dark:border-zinc-800`
- `mt-3 pt-3 px-2` → margin top 12px, padding top 12px, horizontal padding 8px

---

## 3. Comment Item

Each comment is a horizontal row.

### Container
```
flex-row gap-3
```
Gap: 12px between avatar and text block.

### Avatar (32×32px circle)
| Property | Value |
|---|---|
| Size | `w-8 h-8` → 32×32px |
| Border radius | `rounded-full` → 50% |
| Overflow | hidden |
| Background | `bg-zinc-200` / `dark:bg-zinc-700` |
| Flex | `flex-shrink-0` (never shrinks) |
| Alignment | `items-center justify-center` (centers svg fallback) |

**Photo behavior**:
- `<Image>` fills the container (`w-full h-full object-cover`)
- `resizeMode: 'cover'` in React Native
- If the image URL is null, empty, or fails to load → **show SVG fallback** (person icon)
- Never show a broken image placeholder

**SVG Fallback (Person Icon)**:
- Size: `w-full h-full` (fills the 32×32 container)
- Color: `text-zinc-400`
- ViewBox: 0 0 24 24
- Fill: none
- Path: `M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z`

### Text Block
| Property | Value |
|---|---|
| Flex | `flex-1 min-w-0` (fills remaining space, truncatable) |

**Username**:
- Font: `text-sm font-bold` → 14px, weight 700
- Color: `text-zinc-900` / `dark:text-white`
- Underline on press (`hover:underline`)

**Body text**:
- Font: `text-sm` → 14px, weight 400
- Color: `text-zinc-600` / `dark:text-zinc-400`
- Same line as username, separated by a space
- Should use `escapeHtml()` before rendering (XSS prevention)

**Time**:
- Font: `text-xs` → 12px
- Color: `text-zinc-400` / `dark:text-zinc-400`
- Margin top: `mt-1` → 4px below text
- Format: relative time via `diffForHumans()` (PHP Carbon) → always computed server-side
- Examples: "há 2 minutos", "há 3 horas", "há 2 dias"
- If the server returns an empty/null value, render nothing (not "now" or fallback)

### Delete Button (only for own comments)
Hidden for comments where `userId !== currentUserId`.

| Property | Value |
|---|---|
| Size | `w-4 h-4` → 16×16px |
| Color | `text-zinc-400` (default) → `hover:text-red-500` on press |
| Flex | `flex-shrink-0` |

**Trash icon** (SVG):
- ViewBox: 0 0 24 24
- Fill: none
- Stroke: currentColor, strokeWidth: 2
- Path: `M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16`

**Delete behavior**:
- Disable button immediately after press (prevent double-tap)
- Send DELETE request with CSRF token
- On success: remove the comment item from the list
- If list becomes empty after deletion → show empty state message
- Update comment count: decrement by 1

---

## 4. Comment Form

Located at the bottom of the drawer, below the comments list.

### Container
```
flex-row gap-2 mt-3 pb-1
```
Gap: 8px between input and button.

### Text Input
| Property | Value |
|---|---|
| Placeholder | "Escreva um comentário..." |
| Max length | 1000 characters |
| Flex | `flex-1` (fills remaining space) |
| Padding | `px-3 py-2` → horizontal 12px, vertical 8px |
| Border radius | `rounded-lg` → 8px |
| Background | `bg-zinc-100` / `dark:bg-zinc-800` |
| Text color | `text-zinc-900` / `dark:text-white` |
| Placeholder color | `text-zinc-400` / `dark:text-zinc-400` |
| Border | none (0) |
| Focus ring | 2px `ring-zinc-300` / `dark:ring-zinc-600` |
| Font | `text-sm` → 14px |

### Submit Button
| Property | Value |
|---|---|
| Padding | `px-4 py-2` → horizontal 16px, vertical 8px |
| Border radius | `rounded-lg` → 8px |
| Background | `bg-zinc-900` / `dark:bg-white` |
| Text color | `text-white` / `dark:text-zinc-900` |
| Font | `text-sm font-bold` → 14px, weight 700 |
| Disabled opacity | `disabled:opacity-50` |
| Initial state | `disabled` (enabled only when input has text) |
| Loading state | Disable button during request |

### Submit Behavior
1. Get `body` from input, trim whitespace
2. Validate: reject if empty (button should be disabled anyway)
3. Disable button
4. Send POST with CSRF token, Content-Type: application/json
5. On success: clear input, reload comments list, update comment count
6. On error: re-enable button
7. Response format: `{ "success": true, "comment": {...}, "comments_count": N }`

---

## 5. States

### Loading State
```
"Carregando..."
```
- Font: `text-sm`, centered
- Color: `text-zinc-400`
- Padding: `py-4` → 16px vertical

### Empty State
```
"Nenhum comentário ainda. Seja o primeiro!"
```
- Font: `text-sm`, centered
- Color: `text-zinc-500` / `dark:text-zinc-400`
- Padding: `py-8` → 32px vertical

### Error State
```
"Erro ao carregar comentários."
```
- Font: `text-sm`, centered
- Color: `text-zinc-400`
- Padding: `py-4` → 16px vertical
- Displayed on: AJAX failure or malformed response

---

## 6. API Contract

### Fetch Comments
```
GET /posts/{postId}/comments
Accept: application/json
X-Requested-With: XMLHttpRequest
```

**Response**:
```json
{
  "success": true,
  "comments": [
    {
      "id": 1,
      "body": "Comment text",
      "user_id": "uuid",
      "post_id": "1",
      "created_at": "2026-07-13T10:00:00Z",
      "users": {
        "id": "uuid",
        "username": "alice",
        "profile_photo_url": "https://..."
      },
      "time_ago": "há 2 horas"
    }
  ]
}
```

### Create Comment
```
POST /posts/{postId}/comments
Content-Type: application/json
X-CSRF-TOKEN: {token}
Body: { "body": "Comment text" }
```

**Response**:
```json
{
  "success": true,
  "comment": { ... },
  "comments_count": 5
}
```

### Delete Comment
```
DELETE /posts/comments/{commentId}
Content-Type: application/json
X-CSRF-TOKEN: {token}
```

**Response**:
```json
{
  "success": true
}
```

---

## 7. Color Tokens

| Token | Light | Dark |
|---|---|---|
| `--bg-card` (post card) | white | zinc-950 |
| `--divider` | zinc-200 | zinc-800 |
| `--text-primary` | zinc-900 | white |
| `--text-secondary` | zinc-600 | zinc-400 |
| `--text-muted` | zinc-400 | zinc-500 |
| `--text-placeholder` | zinc-400 | zinc-400 |
| `--bg-input` | zinc-100 | zinc-800 |
| `--bg-input-focus` | zinc-100 | zinc-800 |
| `--ring-focus` | zinc-300 | zinc-600 |
| `--bg-btn-primary` | zinc-900 | white |
| `--text-btn-primary` | white | zinc-900 |
| `--btn-disabled` | opacity-50 | opacity-50 |
| `--avatar-bg` | zinc-200 | zinc-700 |
| `--avatar-icon` | zinc-400 | zinc-400 |
| `--delete-icon` | zinc-400 | zinc-400 |
| `--delete-hover` | red-500 | red-500 |
| `--text-error` | zinc-400 | zinc-400 |
| `--text-empty` | zinc-500 | zinc-400 |

---

## 8. Animations Summary

| Element | Animation | Duration | Easing |
|---|---|---|---|
| Comment section expand | `max-height: 0 → scrollHeight` | 300ms | ease-in-out |
| Comment section collapse | `max-height: scrollHeight → 0` | 300ms | ease-in-out |
| Comment button press | scale(0.95) | 100ms | - |
| Like button press | `active:bg-red-50` / `dark:bg-zinc-800` | 100ms | - |
| Delete button hover | color zinc-400 → red-500 | 150ms | ease |

### Expand Animation Detail
```
1. Click comment button → measure scrollHeight of comments-section
2. Remove max-height constraint briefly (allow browser to measure)
3. Set max-height = scrollHeight + 'px' (triggers CSS transition from 0 → scrollHeight)
4. Fetch comments via AJAX (parallel)
5. On AJAX completion:
   a. Replace inner content (comments list HTML)
   b. requestAnimationFrame → re-measure scrollHeight → update max-height
```

---

## 9. Error Handling & Edge Cases

- **User has no profile photo**: `profile_photo_url` is null → show SVG fallback
- **Image URL returns 404/400**: The `<img>` native `onError` handler hides the image and shows the SVG fallback. In React Native use `Image.onError` callback → set state to show fallback icon
- **AJAX fails**: Show "Erro ao carregar comentários." centered message
- **Empty comments list**: Show "Nenhum comentário ainda. Seja o primeiro!" centered message
- **Comment body is empty after trim**: Prevent submission (button disabled)
- **Long comment body (>1000 chars)**: Rejected by server; handle validation error
- **Double-tap on submit**: Disable button immediately on press, re-enable on response
- **Double-tap on delete**: Disable button immediately on press, re-enable on response
- **Missing `time_ago` in response**: Render nothing (not a fallback string)
- **Missing `users` in response**: Show "Usuário" as username, SVG fallback for avatar
- **Multiple sections open**: Only one section at a time; closing all others when opening a new one

---

## 10. React Native Specific Implementation Notes

### Avatar Component Pattern
```tsx
const [imgFailed, setImgFailed] = useState(false);
const hasPhoto = user?.profile_photo_url && !imgFailed;

<View style={styles.avatar}>
  {hasPhoto ? (
    <Image
      source={{ uri: user.profile_photo_url }}
      style={StyleSheet.absoluteFill}
      resizeMode="cover"
      onError={() => setImgFailed(true)}
    />
  ) : (
    <PersonIcon width="100%" height="100%" fill="none" />
  )}
</View>
```

### Expand/Collapse using LayoutAnimation or Animated
```tsx
// Option 1: LayoutAnimation (simplest)
LayoutAnimation.configureNext({
  duration: 300,
  update: { type: 'easeInEaseOut', property: 'maxHeight' },
});

// Option 2: Animated.Value for max-height
const maxHeight = useRef(new Animated.Value(0)).current;
Animated.timing(maxHeight, {
  toValue: isOpen ? scrollHeight : 0,
  duration: 300,
  easing: Easing.inOut(Easing.ease),
  useNativeDriver: false, // required for max-height
}).start();
```

### ScrollView for comments list
```tsx
<ScrollView
  style={{ maxHeight: 320 }}
  showsVerticalScrollIndicator
  nestedScrollEnabled
>
  {comments.map(renderComment)}
</ScrollView>
```

### Transition note
React Native does not support CSS `transition: max-height`. Use `Animated.Value` + `Animated.View` with `maxHeight` interpolation, or `LayoutAnimation` for a simpler spring-based expand.

### Keyboard handling
When the input is focused, ensure the ScrollView scrolls to the bottom so the input stays visible. Use `KeyboardAvoidingView` or `KeyboardAwareScrollView`.
