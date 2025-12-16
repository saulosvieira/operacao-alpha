# Notification Feature

Esta feature implementa o sistema de notificações push usando Web Push API e VAPID.

## Componentes

### DTOs
- **SubscriptionData**: Representa uma inscrição de notificação de um usuário
- **NotificationData**: Representa os dados de uma notificação a ser enviada

### Actions
- **SubscribeToNotificationsAction**: Inscreve um usuário para receber notificações
- **UnsubscribeFromNotificationsAction**: Remove a inscrição de notificações
- **SendNotificationAction**: Envia notificações push para usuários

### Repository
- **NotificationRepository**: Gerencia o acesso aos dados de inscrições

## Configuração

### 1. Chaves VAPID

As chaves VAPID já foram geradas e estão no arquivo `.env`:

```env
VAPID_PUBLIC_KEY=BBKIAKHKVmWFEhXsPERxxHB4TiolpmuIOQOBsQpl9JumuAoE1Btkih_S8ccHCGRp5WIoAIo2RNLmiHoRSjYDYek
VAPID_PRIVATE_KEY=EQ9Pho1yNtb3hHOYH4-UDrI3VuY0bhj_nfk8xVDAREE
```

Para gerar novas chaves, execute:
```bash
php artisan vapid:generate
```

### 2. Configuração do Service Worker

No frontend, você precisará registrar um service worker para receber notificações. Exemplo:

```javascript
// public/sw.js
self.addEventListener('push', function(event) {
  const data = event.data.json();
  
  const options = {
    body: data.body,
    icon: data.icon || '/icons/icon-192x192.png',
    badge: data.badge || '/icons/badge-72x72.png',
    data: data.data || {}
  };
  
  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  
  const url = event.notification.data.url || '/';
  
  event.waitUntil(
    clients.openWindow(url)
  );
});
```

## Uso

### Inscrever usuário para notificações

```php
use App\Domain\Notification\Actions\SubscribeToNotificationsAction;

$action = app(SubscribeToNotificationsAction::class);

$subscription = $action->execute($userId, [
    'endpoint' => 'https://fcm.googleapis.com/fcm/send/...',
    'keys' => [
        'p256dh' => 'BNcRd...',
        'auth' => 'tBHI...'
    ]
]);
```

### Enviar notificação para um usuário

```php
use App\Domain\Notification\Actions\SendNotificationAction;
use App\Domain\Notification\DTOs\NotificationData;

$action = app(SendNotificationAction::class);

$notification = new NotificationData(
    title: 'Novo Simulado Disponível',
    body: 'Um novo simulado de PM-SP foi adicionado!',
    icon: '/icons/icon-192x192.png',
    url: '/exams/123'
);

$result = $action->execute($userId, $notification);
```

### Enviar notificação para todos os usuários

```php
$result = $action->sendToAll($notification);
```

## API Endpoints

### POST /api/notifications/subscribe
Inscreve o usuário autenticado para receber notificações.

**Request:**
```json
{
  "endpoint": "https://fcm.googleapis.com/fcm/send/...",
  "keys": {
    "p256dh": "BNcRd...",
    "auth": "tBHI..."
  }
}
```

### POST /api/notifications/unsubscribe
Remove a inscrição de notificações.

**Request:**
```json
{
  "endpoint": "https://fcm.googleapis.com/fcm/send/..."
}
```

### GET /api/notifications/vapid-public-key
Retorna a chave pública VAPID para uso no frontend.

**Response:**
```json
{
  "publicKey": "BBKIAKHKVmWFEhXsPERxxHB4TiolpmuIOQOBsQpl9JumuAoE1Btkih_S8ccHCGRp5WIoAIo2RNLmiHoRSjYDYek"
}
```

## Frontend Integration

No React, você pode usar a API de notificações assim:

```typescript
// Solicitar permissão
const permission = await Notification.requestPermission();

if (permission === 'granted') {
  // Registrar service worker
  const registration = await navigator.serviceWorker.register('/sw.js');
  
  // Obter chave pública VAPID
  const response = await fetch('/api/notifications/vapid-public-key');
  const { publicKey } = await response.json();
  
  // Inscrever para notificações
  const subscription = await registration.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey: publicKey
  });
  
  // Enviar inscrição para o backend
  await fetch('/api/notifications/subscribe', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(subscription.toJSON())
  });
}
```

## Casos de Uso

1. **Novo simulado disponível**: Notificar usuários quando um novo simulado é publicado
2. **Resultado disponível**: Notificar quando o resultado de um simulado está pronto
3. **Lembrete de simulado**: Lembrar usuários de simulados não finalizados
4. **Ranking atualizado**: Notificar quando o ranking é atualizado
5. **Novo edital**: Alertar sobre novos editais publicados

## Observações

- As inscrições expiradas são automaticamente removidas quando uma notificação falha
- O sistema suporta múltiplas inscrições por usuário (diferentes dispositivos)
- As notificações são enviadas de forma assíncrona
- Erros de envio são logados para análise
