package br.com.operacaoalfa

import android.Manifest
import android.app.Activity
import android.app.NotificationChannel
import android.app.NotificationManager
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.util.Log
import android.view.View
import android.webkit.CookieManager
import android.webkit.ValueCallback
import android.webkit.WebView
import androidx.activity.OnBackPressedCallback
import androidx.activity.result.ActivityResultLauncher
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import br.com.operacaoalfa.databinding.ActivityMainBinding

/**
 * Main activity that hosts the WebView loading the Operação Alfa frontend.
 *
 * Responsibilities (Task 3.2):
 * - Configure WebView with JavaScript, DOM Storage, and cookies enabled
 * - Configure CookieManager for persistent sessions
 * - Show splash screen on startup, hide when page finishes loading
 * - Load the base URL from BuildConfig
 *
 * Responsibilities (Task 3.3):
 * - Handle back button: navigate WebView history or show exit confirmation dialog
 *
 * Responsibilities (Task 3.4):
 * - Flush cookies in onPause() and onDestroy() for session persistence
 * - Destroy WebView in onDestroy() to release resources
 *
 * Responsibilities (Task 4.1):
 * - Use AppWebViewClient for URL routing, splash/offline callbacks, and SSL handling
 *
 * Responsibilities (Task 4.2 / 4.3):
 * - Set AppWebChromeClient for file upload support
 * - Register ActivityResultLauncher for file chooser
 * - Handle file selection result and cancellation
 *
 * Responsibilities (Task 5.2):
 * - Register NetworkMonitor in onCreate, unregister in onDestroy
 * - When connectivity is restored and offline screen is visible, auto-reload the WebView
 * - Retry button hides offline screen and reloads the WebView
 *
 * Placeholder areas for future tasks:
 * - NativeAppInterface / JavaScriptInterface (Task 6)
 * - FCM / Notification channel (Task 8)
 */
class MainActivity : AppCompatActivity() {

    companion object {
        private const val TAG = "MainActivity"
        private const val CHANNEL_ID = "operacao_alfa_notifications"
        private const val CHANNEL_NAME = "Notificações"
        private const val CHANNEL_DESCRIPTION = "Notificações do Operação Alfa"
    }

    private lateinit var binding: ActivityMainBinding

    /**
     * Monitors network connectivity changes (Req 6.1, 6.4).
     * Registered in onCreate, unregistered in onDestroy.
     */
    private lateinit var networkMonitor: NetworkMonitor

    /**
     * Callback from the WebView's WebChromeClient to deliver the selected file URI(s)
     * back to the web content after a file chooser request (Req 5.1, 5.2, 5.3).
     * Set when onShowFileChooser is triggered; cleared after the result is delivered.
     */
    private var fileUploadCallback: ValueCallback<Array<Uri>>? = null

    /**
     * ActivityResultLauncher for the file chooser intent (Req 5.1, 5.2, 5.3).
     *
     * Registered in the class body (before onCreate) so it is available during the
     * CREATED lifecycle state as required by the Activity Result API.
     *
     * On result:
     * - If the user selected a file (RESULT_OK), delivers the URI to [fileUploadCallback]
     * - If the user cancelled, sends null to [fileUploadCallback] so the WebView is unblocked
     */
    private val fileChooserLauncher: ActivityResultLauncher<Intent> =
        registerForActivityResult(ActivityResultContracts.StartActivityForResult()) { result ->
            val callback = fileUploadCallback
            fileUploadCallback = null

            if (result.resultCode == Activity.RESULT_OK && result.data?.data != null) {
                // User selected a file — deliver the URI to the WebView (Req 5.2)
                callback?.onReceiveValue(arrayOf(result.data!!.data!!))
            } else {
                // User cancelled or no data — notify the WebView (Req 5.3)
                callback?.onReceiveValue(null)
            }
        }

    /**
     * ActivityResultLauncher for requesting POST_NOTIFICATIONS permission (Req 3.1).
     *
     * Registered in the class body (before onCreate) so it is available during the
     * CREATED lifecycle state as required by the Activity Result API.
     *
     * On API 33+ (TIRAMISU), the system shows a permission dialog. The result
     * indicates whether the user granted or denied the permission.
     */
    private val notificationPermissionLauncher: ActivityResultLauncher<String> =
        registerForActivityResult(ActivityResultContracts.RequestPermission()) { isGranted ->
            if (isGranted) {
                Log.d(TAG, "POST_NOTIFICATIONS permission granted")
            } else {
                Log.d(TAG, "POST_NOTIFICATIONS permission denied")
            }
        }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupCookieManager()
        setupWebView()
        setupBackNavigation()

        // Task 5.2 — Register NetworkMonitor and retry button (Req 6.2, 6.3, 6.4)
        setupNetworkMonitor()
        setupRetryButton()

        // Task 8.2 — Create notification channel and request POST_NOTIFICATIONS permission (Req 3.1)
        createNotificationChannel()
        requestNotificationPermission()

        // Task 8.3 — Retry sending FCM token if previous sync failed (Req 3.7)
        FCMService.retrySyncIfNeeded(this)

        // Show splash and load the base URL
        showSplashScreen()
        binding.webView.loadUrl(BuildConfig.BASE_URL)

        // Task 8.4 — Navigate to notification URL if the activity was launched from a notification tap
        handleNotificationUrl(intent)
    }

    // region WebView Setup

    /**
     * Configures the WebView with required settings:
     * - JavaScript enabled (Req 1.2)
     * - DOM Storage enabled (Req 1.3)
     * - AppWebViewClient for URL routing, splash/offline callbacks, and SSL handling (Task 4.1)
     */
    private fun setupWebView() {
        binding.webView.apply {
            settings.javaScriptEnabled = true          // Req 1.2
            settings.domStorageEnabled = true           // Req 1.3
            settings.loadWithOverviewMode = true
            settings.useWideViewPort = true

            // Custom WebViewClient — handles URL routing, splash, offline, and SSL (Task 4.1)
            webViewClient = AppWebViewClient(
                context = this@MainActivity,
                baseDomain = extractDomain(BuildConfig.BASE_URL),
                onPageLoaded = { hideSplashScreen() },
                onError = { showOfflineScreen() }
            )

            // Custom WebChromeClient — handles file upload via onShowFileChooser (Task 4.2 / 4.3)
            webChromeClient = AppWebChromeClient { filePathCallback, fileChooserParams ->
                // Cancel any pending callback to avoid WebView getting stuck (Req 5.3)
                fileUploadCallback?.onReceiveValue(null)
                fileUploadCallback = filePathCallback

                // Build a file picker intent that supports images and generic files (Req 5.1, 5.4)
                val contentIntent = Intent(Intent.ACTION_GET_CONTENT).apply {
                    addCategory(Intent.CATEGORY_OPENABLE)
                    type = "*/*"
                    // Use accepted types from the web content if available
                    val acceptTypes = fileChooserParams.acceptTypes
                    if (!acceptTypes.isNullOrEmpty() && acceptTypes[0].isNotBlank()) {
                        type = acceptTypes[0]
                        if (acceptTypes.size > 1) {
                            putExtra(Intent.EXTRA_MIME_TYPES, acceptTypes)
                        }
                    }
                }

                val chooserIntent = Intent.createChooser(contentIntent, "Selecionar arquivo")
                fileChooserLauncher.launch(chooserIntent)
                true
            }
            // Task 6.2 — Expose NativeAppInterface to JavaScript as "NativeApp" (Req 8.1, 8.3, 8.4)
            addJavascriptInterface(NativeAppInterface(this@MainActivity), "NativeApp")
        }
    }

    /**
     * Extracts the host/domain from a URL string.
     * For example, "https://operacaoalfa.com.br" → "operacaoalfa.com.br"
     * Falls back to the full URL if parsing fails.
     */
    private fun extractDomain(url: String): String {
        return try {
            Uri.parse(url).host ?: url
        } catch (_: Exception) {
            url
        }
    }

    // endregion

    // region Cookie Manager

    /**
     * Configures CookieManager for persistent cookie storage (Req 2.1, 2.2, 2.5).
     * Enables cookie acceptance and third-party cookies so authentication
     * sessions survive app restarts.
     */
    private fun setupCookieManager() {
        val cookieManager = CookieManager.getInstance()
        cookieManager.setAcceptCookie(true)                              // Req 2.1
        cookieManager.setAcceptThirdPartyCookies(binding.webView, true)  // Req 2.2
    }

    // endregion

    // region Splash Screen

    /**
     * Shows the splash screen overlay (Req 1.6).
     * Called at startup before the WebView begins loading content.
     */
    private fun showSplashScreen() {
        binding.splashScreen.visibility = View.VISIBLE
    }

    /**
     * Hides the splash screen overlay (Req 1.6).
     * Called from onPageFinished when the WebView finishes loading the initial page.
     */
    private fun hideSplashScreen() {
        binding.splashScreen.visibility = View.GONE
    }

    // endregion

    // region Offline Screen & Network Monitoring (Task 5.2)

    /**
     * Shows the offline error screen (Req 6.1).
     * Called from AppWebViewClient when a network error occurs.
     */
    private fun showOfflineScreen() {
        binding.offlineScreen.visibility = View.VISIBLE
    }

    /**
     * Hides the offline error screen (Req 6.2, 6.4).
     * Called when connectivity is restored or the user taps "Tentar Novamente".
     */
    private fun hideOfflineScreen() {
        binding.offlineScreen.visibility = View.GONE
    }

    /**
     * Creates and registers the NetworkMonitor (Req 6.4).
     *
     * When connectivity changes:
     * - If the device comes back online and the offline screen is visible,
     *   automatically hides the offline screen and reloads the WebView (Req 6.4).
     */
    private fun setupNetworkMonitor() {
        networkMonitor = NetworkMonitor(this)
        networkMonitor.onConnectivityChanged = { online ->
            runOnUiThread {
                if (online && binding.offlineScreen.visibility == View.VISIBLE) {
                    hideOfflineScreen()
                    binding.webView.reload()  // Req 6.4
                }
            }
        }
        networkMonitor.register()
    }

    /**
     * Sets up the "Tentar Novamente" retry button click listener (Req 6.2, 6.3).
     * Hides the offline screen and reloads the WebView.
     */
    private fun setupRetryButton() {
        binding.retryButton.setOnClickListener {
            hideOfflineScreen()
            binding.webView.reload()  // Req 6.3
        }
    }

    // endregion

    // region Back Navigation

    /**
     * Configures back button handling using onBackPressedDispatcher (Req 4.1–4.4).
     *
     * - If the WebView has navigation history, go back in the WebView (Req 4.1)
     * - If no history, show an AlertDialog asking the user to confirm exit (Req 4.2)
     * - Confirming closes the activity (Req 4.3)
     * - Cancelling keeps the user on the current screen (Req 4.4)
     */
    private fun setupBackNavigation() {
        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (binding.webView.canGoBack()) {
                    binding.webView.goBack()                              // Req 4.1
                } else {
                    showExitConfirmationDialog()                          // Req 4.2
                }
            }
        })
    }

    /**
     * Shows an AlertDialog asking the user to confirm app exit (Req 4.2).
     * "Sim" finishes the activity (Req 4.3); "Não" dismisses the dialog (Req 4.4).
     */
    private fun showExitConfirmationDialog() {
        AlertDialog.Builder(this)
            .setTitle("Sair do aplicativo?")
            .setMessage("Deseja realmente sair?")
            .setPositiveButton("Sim") { _, _ ->
                finish()                                                  // Req 4.3
            }
            .setNegativeButton("Não") { dialog, _ ->
                dialog.dismiss()                                          // Req 4.4
            }
            .show()
    }

    // endregion

    // region Notification Channel & Permission (Task 8.2)

    /**
     * Creates the notification channel required for Android O (API 26) and above (Req 3.1).
     *
     * Uses the same channel ID ([CHANNEL_ID]) as [FCMService] so all push
     * notifications are grouped under a single user-visible channel.
     * This is safe to call multiple times — the system ignores the call if the
     * channel already exists. Creating it early in MainActivity ensures the
     * channel is available before any notification arrives.
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
            val notificationManager = getSystemService(NotificationManager::class.java)
            notificationManager.createNotificationChannel(channel)
            Log.d(TAG, "Notification channel created: $CHANNEL_ID")
        }
    }

    /**
     * Requests the POST_NOTIFICATIONS runtime permission on API 33+ (Req 3.1).
     *
     * On Android 13 (TIRAMISU) and above, apps must request this permission at
     * runtime before notifications can be displayed. On older API levels this
     * method is a no-op because the permission is granted at install time.
     *
     * Uses [notificationPermissionLauncher] (an [ActivityResultLauncher]) registered
     * in the class body to handle the permission result asynchronously.
     */
    private fun requestNotificationPermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(
                    this,
                    Manifest.permission.POST_NOTIFICATIONS
                ) != PackageManager.PERMISSION_GRANTED
            ) {
                notificationPermissionLauncher.launch(Manifest.permission.POST_NOTIFICATIONS)
            }
        }
    }

    // endregion

    // region Notification Navigation (Task 8.4)

    /**
     * Handles navigation when the activity is re-launched from a notification tap
     * while already running (singleTask launch mode).
     *
     * Delegates to [handleNotificationUrl] to load the notification URL in the WebView.
     *
     * Validates: Requirement 3.5
     */
    override fun onNewIntent(intent: Intent) {
        super.onNewIntent(intent)
        setIntent(intent)
        handleNotificationUrl(intent)
    }

    /**
     * Checks the given intent for a `notification_url` extra and loads it in the WebView.
     *
     * If the URL is a relative path (starts with "/"), it is prepended with
     * [BuildConfig.BASE_URL] to form a full URL. Absolute URLs are loaded as-is.
     *
     * This is called from [onCreate] (cold start from notification) and from
     * [onNewIntent] (activity already running, singleTask re-launch).
     *
     * Validates: Requirement 3.5
     *
     * @param intent The intent to check for the `notification_url` extra, or null.
     */
    private fun handleNotificationUrl(intent: Intent?) {
        val notificationUrl = intent?.getStringExtra("notification_url") ?: return

        if (notificationUrl.isBlank()) return

        val fullUrl = if (notificationUrl.startsWith("/")) {
            BuildConfig.BASE_URL.trimEnd('/') + notificationUrl
        } else {
            notificationUrl
        }

        Log.d(TAG, "Loading notification URL: $fullUrl")
        binding.webView.loadUrl(fullUrl)
    }

    // endregion

    // region Session Persistence (Task 3.4)

    /**
     * Flushes cookies to persistent storage when the activity is paused (Req 2.1, 2.5).
     * This ensures cookies are written to disk even if the process is killed while in the background.
     */
    override fun onPause() {
        super.onPause()
        CookieManager.getInstance().flush()
    }

    /**
     * Flushes cookies, unregisters the network monitor, and cleans up the WebView
     * when the activity is destroyed (Req 2.1, 2.5, 6.4).
     *
     * NetworkMonitor is unregistered before WebView is destroyed to avoid
     * callbacks firing on a destroyed view.
     */
    override fun onDestroy() {
        CookieManager.getInstance().flush()
        networkMonitor.unregister()
        binding.webView.destroy()
        super.onDestroy()
    }

    // endregion
}
