package br.com.operacaoalfa

import android.content.Context
import android.content.Intent
import android.net.Uri
import android.net.http.SslError
import android.webkit.SslErrorHandler
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebResourceResponse
import android.webkit.WebView
import android.webkit.WebViewClient

/**
 * Custom WebViewClient for the Operação Alfa app.
 *
 * Handles:
 * - URL routing: internal domain URLs load in the WebView, external URLs open in the default browser (Req 1.4, 1.5)
 * - Splash screen: hides splash when the page finishes loading via [onPageFinished] callback (Req 1.6)
 * - Offline screen: shows offline screen on network/HTTP errors via [onError] callback (Req 6.1)
 * - SSL errors: accepts certificates in debug builds only for local development
 *
 * @param context         Application or Activity context used to launch external browser intents
 * @param baseDomain      The base domain extracted from BuildConfig.BASE_URL (e.g. "operacaoalfa.com.br" or "10.0.2.2")
 * @param onPageLoaded    Callback invoked when a page finishes loading — used to hide the splash screen
 * @param onError         Callback invoked when a network or HTTP error occurs — used to show the offline screen
 */
class AppWebViewClient(
    private val context: Context,
    private val baseDomain: String,
    private val onPageLoaded: () -> Unit,
    private val onError: () -> Unit
) : WebViewClient() {

    /**
     * Intercepts URL loading requests.
     *
     * - URLs whose host contains the [baseDomain] are loaded internally in the WebView (Req 1.5).
     * - All other URLs are opened in the device's default browser via ACTION_VIEW intent (Req 1.4).
     */
    override fun shouldOverrideUrlLoading(view: WebView, request: WebResourceRequest): Boolean {
        val url = request.url
        val host = url.host ?: return false

        return if (host.contains(baseDomain, ignoreCase = true)) {
            // Internal URL — let the WebView handle it
            false
        } else {
            // External URL — open in the default browser
            val intent = Intent(Intent.ACTION_VIEW, url)
            context.startActivity(intent)
            true
        }
    }

    /**
     * Called when the WebView encounters a network-level error (e.g. no internet).
     * Only triggers the offline screen for main frame errors to avoid false positives
     * from failing sub-resources like images or scripts (Req 6.1).
     */
    override fun onReceivedError(
        view: WebView,
        request: WebResourceRequest,
        error: WebResourceError
    ) {
        super.onReceivedError(view, request, error)
        if (request.isForMainFrame) {
            onError()
        }
    }

    /**
     * Called when the WebView receives an HTTP error response (4xx, 5xx).
     * Only triggers the offline screen for main frame errors (Req 6.1).
     */
    override fun onReceivedHttpError(
        view: WebView,
        request: WebResourceRequest,
        errorResponse: WebResourceResponse
    ) {
        super.onReceivedHttpError(view, request, errorResponse)
        if (request.isForMainFrame) {
            onError()
        }
    }

    /**
     * Called when a page finishes loading.
     * Invokes [onPageLoaded] to hide the splash screen (Req 1.6).
     */
    override fun onPageFinished(view: WebView, url: String?) {
        super.onPageFinished(view, url)
        onPageLoaded()
    }

    /**
     * Handles SSL certificate errors.
     *
     * - In debug builds: proceeds with the request to allow local HTTPS development
     *   with self-signed certificates.
     * - In release builds: cancels the request to enforce certificate validation.
     */
    @android.annotation.SuppressLint("WebViewClientOnReceivedSslError")
    override fun onReceivedSslError(view: WebView, handler: SslErrorHandler, error: SslError) {
        if (BuildConfig.DEBUG) {
            handler.proceed()
        } else {
            handler.cancel()
        }
    }
}
