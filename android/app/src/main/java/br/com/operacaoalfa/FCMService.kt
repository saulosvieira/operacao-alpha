package br.com.operacaoalfa

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.os.Build
import android.util.Log
import android.webkit.CookieManager
import androidx.core.app.NotificationCompat
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage
import org.json.JSONObject
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL
import java.util.UUID
import java.util.concurrent.Executors

/**
 * Firebase Cloud Messaging service that handles push notifications.
 *
 * Responsibilities (Task 8.1):
 * - [onNewToken]: Save token in SharedPreferences, mark as not synced, attempt backend sync
 * - [onMessageReceived]: Extract title, body, url from data message and show notification
 * - [showNotification]: Build and display a system notification with a PendingIntent
 *   that opens MainActivity with the notification URL
 * - [sendTokenToBackend]: Send FCM token to backend via HTTP POST (Task 8.3)
 *
 * Validates: Requirements 3.2, 3.4, 3.5, 3.6
 */
class FCMService : FirebaseMessagingService() {

    companion object {
        private const val TAG = "FCMService"
        private const val PREFS_NAME = "br.com.operacaoalfa.prefs"
        private const val KEY_FCM_TOKEN = "fcm_token"
        private const val KEY_FCM_TOKEN_SYNCED = "fcm_token_synced"
        private const val KEY_DEVICE_ID = "device_id"
        private const val CHANNEL_ID = "operacao_alfa_notifications"
        private const val CHANNEL_NAME = "Notificações"
        private const val CHANNEL_DESCRIPTION = "Notificações do Operação Alfa"

        /**
         * Retries sending the FCM token to the backend if it was not synced previously.
         *
         * Checks `fcm_token_synced` in SharedPreferences. If `false` and a stored
         * token exists, performs the POST request on a background thread.
         *
         * Intended to be called from [MainActivity.onCreate] on each app launch
         * to fulfil Requirement 3.7 (retry on next startup).
         *
         * Validates: Requirement 3.7
         *
         * @param context Application or Activity context.
         */
        fun retrySyncIfNeeded(context: Context) {
            val prefs = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
            val synced = prefs.getBoolean(KEY_FCM_TOKEN_SYNCED, true)
            val token = prefs.getString(KEY_FCM_TOKEN, null)

            if (!synced && !token.isNullOrBlank()) {
                Log.d(TAG, "Retrying FCM token sync...")
                val deviceId = prefs.getString(KEY_DEVICE_ID, null) ?: run {
                    val newId = UUID.randomUUID().toString()
                    prefs.edit().putString(KEY_DEVICE_ID, newId).apply()
                    newId
                }

                Executors.newSingleThreadExecutor().execute {
                    try {
                        val url = URL(BuildConfig.BASE_URL + "/api/notifications/fcm/subscribe")
                        val connection = url.openConnection() as HttpURLConnection

                        connection.requestMethod = "POST"
                        connection.setRequestProperty("Content-Type", "application/json")
                        connection.setRequestProperty("Accept", "application/json")

                        val cookies = CookieManager.getInstance().getCookie(BuildConfig.BASE_URL)
                        if (!cookies.isNullOrBlank()) {
                            connection.setRequestProperty("Cookie", cookies)
                        }

                        connection.doOutput = true
                        connection.connectTimeout = 15_000
                        connection.readTimeout = 15_000

                        val jsonBody = JSONObject().apply {
                            put("token", token)
                            put("device_id", deviceId)
                        }

                        OutputStreamWriter(connection.outputStream, Charsets.UTF_8).use { writer ->
                            writer.write(jsonBody.toString())
                            writer.flush()
                        }

                        val responseCode = connection.responseCode
                        connection.disconnect()

                        if (responseCode in 200..299) {
                            Log.d(TAG, "FCM token retry sync succeeded (HTTP $responseCode)")
                            prefs.edit().putBoolean(KEY_FCM_TOKEN_SYNCED, true).apply()
                        } else {
                            Log.w(TAG, "FCM token retry sync failed (HTTP $responseCode)")
                            prefs.edit().putBoolean(KEY_FCM_TOKEN_SYNCED, false).apply()
                        }
                    } catch (e: Exception) {
                        Log.e(TAG, "FCM token retry sync error", e)
                        prefs.edit().putBoolean(KEY_FCM_TOKEN_SYNCED, false).apply()
                    }
                }
            }
        }
    }

    /** Single-thread executor for background network operations. */
    private val executor = Executors.newSingleThreadExecutor()

    /**
     * Called when a new FCM registration token is generated or refreshed.
     *
     * Saves the token to SharedPreferences, marks it as not yet synced with the
     * backend, and attempts to send it to the server.
     *
     * Validates: Requirements 3.2, 3.6
     *
     * @param token The new FCM registration token.
     */
    override fun onNewToken(token: String) {
        super.onNewToken(token)
        Log.d(TAG, "New FCM token received")

        val prefs = getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
        prefs.edit()
            .putString(KEY_FCM_TOKEN, token)
            .putBoolean(KEY_FCM_TOKEN_SYNCED, false)
            .apply()

        // Ensure a device_id exists (generated once on first install)
        getOrCreateDeviceId(prefs)

        // Attempt to send the token to the backend (Task 8.3)
        sendTokenToBackend(token)
    }

    /**
     * Called when a data message is received from FCM.
     *
     * Extracts `title`, `body`, and `url` from the message data payload
     * and delegates to [showNotification] to display a system notification.
     *
     * Expected payload format (data message):
     * ```json
     * {
     *   "data": {
     *     "title": "Novo Simulado Disponível",
     *     "body": "Um novo simulado de PM-SP foi adicionado!",
     *     "url": "/exams/123"
     *   }
     * }
     * ```
     *
     * Validates: Requirement 3.4
     *
     * @param message The remote message received from FCM.
     */
    override fun onMessageReceived(message: RemoteMessage) {
        super.onMessageReceived(message)
        Log.d(TAG, "Message received from: ${message.from}")

        val data = message.data
        val title = data["title"] ?: getString(R.string.app_name)
        val body = data["body"] ?: ""
        val url = data["url"]

        if (title.isNotBlank() || body.isNotBlank()) {
            showNotification(title, body, url)
        }
    }

    /**
     * Builds and displays a system notification.
     *
     * Creates a notification using [NotificationCompat.Builder] with the
     * [CHANNEL_ID] notification channel. The notification includes a [PendingIntent]
     * that opens [MainActivity] with an extra `notification_url` containing the
     * target URL from the push data.
     *
     * Validates: Requirements 3.4, 3.5
     *
     * @param title The notification title.
     * @param body  The notification body text.
     * @param url   Optional URL to navigate to when the notification is tapped.
     */
    private fun showNotification(title: String, body: String, url: String?) {
        createNotificationChannel()

        val intent = Intent(this, MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TOP
            if (!url.isNullOrBlank()) {
                putExtra("notification_url", url)
            }
        }

        val pendingIntent = PendingIntent.getActivity(
            this,
            System.currentTimeMillis().toInt(),
            intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        val notification = NotificationCompat.Builder(this, CHANNEL_ID)
            .setSmallIcon(R.mipmap.ic_launcher)
            .setContentTitle(title)
            .setContentText(body)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setAutoCancel(true)
            .setContentIntent(pendingIntent)
            .build()

        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.notify(System.currentTimeMillis().toInt(), notification)
    }

    /**
     * Creates the notification channel required for Android O (API 26) and above.
     *
     * Uses [CHANNEL_ID] as the channel identifier. This is safe to call multiple
     * times — the system ignores the call if the channel already exists.
     */
    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                CHANNEL_ID,
                CHANNEL_NAME,
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = CHANNEL_DESCRIPTION
            }
            val notificationManager =
                getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            notificationManager.createNotificationChannel(channel)
        }
    }

    /**
     * Sends the FCM token to the backend via POST /api/notifications/fcm/subscribe.
     *
     * Runs on a background thread using [executor]. The request includes:
     * - JSON body with `token` and `device_id`
     * - Authentication cookies from [CookieManager] (same session as the WebView)
     * - Content-Type and Accept headers set to application/json
     *
     * On success (HTTP 2xx), marks `fcm_token_synced = true` in SharedPreferences.
     * On failure, marks `fcm_token_synced = false` so the token can be retried on
     * the next app launch via [retrySyncIfNeeded].
     *
     * Validates: Requirements 3.3, 3.6, 3.7
     *
     * @param token The FCM token to send to the backend.
     */
    private fun sendTokenToBackend(token: String) {
        executor.execute {
            val prefs = getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
            val deviceId = getOrCreateDeviceId(prefs)

            try {
                val url = URL(BuildConfig.BASE_URL + "/api/notifications/fcm/subscribe")
                val connection = url.openConnection() as HttpURLConnection

                connection.requestMethod = "POST"
                connection.setRequestProperty("Content-Type", "application/json")
                connection.setRequestProperty("Accept", "application/json")

                // Attach session cookies from the WebView for authentication (Req 3.3)
                val cookies = CookieManager.getInstance().getCookie(BuildConfig.BASE_URL)
                if (!cookies.isNullOrBlank()) {
                    connection.setRequestProperty("Cookie", cookies)
                }

                connection.doOutput = true
                connection.connectTimeout = 15_000
                connection.readTimeout = 15_000

                // Build JSON body
                val jsonBody = JSONObject().apply {
                    put("token", token)
                    put("device_id", deviceId)
                }

                OutputStreamWriter(connection.outputStream, Charsets.UTF_8).use { writer ->
                    writer.write(jsonBody.toString())
                    writer.flush()
                }

                val responseCode = connection.responseCode
                connection.disconnect()

                if (responseCode in 200..299) {
                    Log.d(TAG, "FCM token synced successfully (HTTP $responseCode)")
                    prefs.edit().putBoolean(KEY_FCM_TOKEN_SYNCED, true).apply()
                } else {
                    Log.w(TAG, "FCM token sync failed (HTTP $responseCode)")
                    prefs.edit().putBoolean(KEY_FCM_TOKEN_SYNCED, false).apply()
                }
            } catch (e: Exception) {
                Log.e(TAG, "FCM token sync error", e)
                prefs.edit().putBoolean(KEY_FCM_TOKEN_SYNCED, false).apply()
            }
        }
    }

    /**
     * Returns the unique device ID from SharedPreferences, generating a new
     * UUID on first install if none exists.
     *
     * @param prefs The SharedPreferences instance to read/write the device ID.
     * @return The persisted device ID string.
     */
    private fun getOrCreateDeviceId(prefs: android.content.SharedPreferences): String {
        val existing = prefs.getString(KEY_DEVICE_ID, null)
        if (existing != null) return existing

        val newId = UUID.randomUUID().toString()
        prefs.edit().putString(KEY_DEVICE_ID, newId).apply()
        Log.d(TAG, "Generated new device_id")
        return newId
    }
}
