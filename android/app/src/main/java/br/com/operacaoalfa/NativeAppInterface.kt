package br.com.operacaoalfa

import android.content.Context
import android.webkit.JavascriptInterface

/**
 * JavaScript bridge exposed to the WebView as "NativeApp".
 *
 * Allows the React frontend to detect the native environment,
 * retrieve the FCM token, and query the app version.
 *
 * Usage in frontend:
 * ```js
 * if (window.NativeApp?.isNativeApp()) {
 *     const token = window.NativeApp.getFcmToken();
 *     const version = window.NativeApp.getAppVersion();
 * }
 * ```
 *
 * @param context Application or Activity context used to access SharedPreferences.
 *
 * Validates: Requirements 8.1, 8.2, 8.3
 */
class NativeAppInterface(private val context: Context) {

    companion object {
        private const val PREFS_NAME = "br.com.operacaoalfa.prefs"
        private const val KEY_FCM_TOKEN = "fcm_token"
    }

    /**
     * Returns `true` so the frontend can detect it is running inside the native WebView.
     *
     * Validates: Requirement 8.3
     */
    @JavascriptInterface
    fun isNativeApp(): Boolean = true

    /**
     * Returns the current FCM device token stored in SharedPreferences,
     * or an empty string if no token has been saved yet.
     *
     * The token is persisted by [FCMService.onNewToken] using the same
     * SharedPreferences file and key.
     *
     * Validates: Requirements 8.1, 8.2
     */
    @JavascriptInterface
    fun getFcmToken(): String {
        val prefs = context.getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)
        return prefs.getString(KEY_FCM_TOKEN, "") ?: ""
    }

    /**
     * Returns the application version name from BuildConfig (e.g. "1.0").
     *
     * Validates: Requirement 8.3
     */
    @JavascriptInterface
    fun getAppVersion(): String = BuildConfig.VERSION_NAME
}
