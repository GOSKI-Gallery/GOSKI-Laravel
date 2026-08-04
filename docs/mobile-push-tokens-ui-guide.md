# Push Token — Mobile (React Native) Implementation Guide

This document specifies how the GOSKI mobile app registers push-notification tokens, following the same patterns as `docs/mobile-comments-ui-guide.md` and `docs/mobile-location-ui-guide.md`.

---

## 1. Overview

To deliver push notifications, the mobile app must register an **Expo Push Token** for the logged-in user. The token is an identifier the Expo Push Service uses to route a message to a specific device through FCM (Android) or APNs (iOS).

---

## 2. Capturing the token

```ts
// lib/notifications.ts:20 — registerForPushNotificationsAsync(userId)
import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import { Platform } from 'react-native';

const { status: existingStatus } = await Notifications.getPermissionsAsync();
let finalStatus = existingStatus;

if (existingStatus !== 'granted') {
  const { status } = await Notifications.requestPermissionsAsync();
  finalStatus = status;
}

if (finalStatus !== 'granted') {
  alert('Falamos! Ative as notificações para não perder suas interações.');
  return null; // usuário recusou — NÃO salva token
}

const tokenData = await Notifications.getExpoPushTokenAsync();
const expoPushToken = tokenData.data; // ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]

// Android: canal com importância MAX (lib/notifications.ts:40)
if (Platform.OS === 'android') {
  await Notifications.setNotificationChannelAsync('default', {
    name: 'default',
    importance: Notifications.AndroidImportance.MAX,
    sound: true,
    vibrationPattern: [0, 250, 250, 250],
  });
}
```

> ⚠️ Push remoto **não funciona no Expo Go a partir do SDK 53**. Precisa de **development build** (expo-dev-client) ou build de produção. No Expo Go, `getExpoPushTokenAsync` falha — o `try/catch` em `loadNotifications()` captura e retorna `null`, e o app segue funcionando normalmente.

---

## 3. Saving the token

- App entry: `app/_layout.tsx:88` — `loadNotifications()` runs when `user?.id` está disponível.
- Upsert via PostgREST: `lib/notifications.ts:56`:

```ts
await supabase
  .from('push_tokens')
  .upsert(
    { user_id: userId, token: expoPushToken, platform: Platform.OS },
    { onConflict: 'user_id' }
  );
```

### Request

```
POST {SUPABASE_URL}/rest/v1/push_tokens?on_conflict=user_id
Headers:
  apikey: {SUPABASE_ANON_KEY}
  Authorization: Bearer {user_jwt}
  Content-Profile: laravel
  Accept-Profile: laravel
  Prefer: resolution=merge-duplicates, return=representation
```

Body:
```json
{
  "user_id": "{auth.uid()}",
  "token": "ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]",
  "platform": "ios"
}
```

### Field constraints

| Field | Type | Rules |
|---|---|---|
| `user_id` | uuid | FK `laravel.users.id`, `on delete cascade`; = `auth.uid()` |
| `token` | text | not null; Expo Push Token format |
| `platform` | text | nullable; `ios` / `android` |
| `onConflict` | — | `user_id` — alinhado com a constraint única do Laravel |

> ❗ **Obrigatório:** os headers `Content-Profile: laravel` e `Accept-Profile: laravel` são obrigatórios para acessar tabelas no schema `laravel`. O client do app já está configurado com `db: { schema: 'laravel' }` (`lib/supabase.ts:32`), então `supabase.from('push_tokens')` resolve automaticamente para `laravel.push_tokens`.

---

## 4. Lifecycle

- **Created**: no login / quando o app inicia com usuário autenticado (`app/_layout.tsx:88`).
- **Updated**: a cada `upsert` o `updated_at` é atualizado.
- **Rotation**: quando o app é reinstalado, o SO rotaciona o token ou o Expo Push Service regenera — o `upsert` (on_conflict `user_id`) sobrescreve o anterior.
- **Revocation / cleanup**: quando o usuário desinstala o app ou o token invalida, o backend recebe `DeviceNotRegistered` da Expo ao tentar enviar e deve deletar o token (responsabilidade do backend — `SupabasePushTokenService::deleteToken`).

### Uniqueness note

- `unique (user_id, token)`: permite **vários dispositivos** por usuário.
- O `onConflict: user_id` do app cobre apenas conflitos por `user_id`. Para multi-dispositivo, o `token` diferente cria nova linha — o backend deve iterar sobre todos os tokens do usuário ao enviar.

---

## 5. RLS

O app usa a **anon key** e respeita RLS:

```sql
alter table laravel.push_tokens enable row level security;

create policy "Usuario insere seu token"
  on laravel.push_tokens for insert with check (auth.uid() = user_id);

create policy "Usuario atualiza seu token"
  on laravel.push_tokens for update using (auth.uid() = user_id) with check (auth.uid() = user_id);

-- opcional, se houver leitura pelo client:
create policy "Usuario le seus tokens"
  on laravel.push_tokens for select using (auth.uid() = user_id);
```

O **backend Laravel** usa `service_role` (ignora RLS) — por isso consegue ler a tabela para disparar as notificações.

---

## 6. Verification

After creating the table, the `[Push] Erro ao salvar token` log disappears. Confirm the table is visible to PostgREST:

```sql
select schemaname, tablename from pg_tables
where schemaname = 'laravel' and tablename = 'push_tokens';
```

And, after a login on a real device:

```sql
select user_id, token, platform, updated_at
from laravel.push_tokens
order by updated_at desc;
```
