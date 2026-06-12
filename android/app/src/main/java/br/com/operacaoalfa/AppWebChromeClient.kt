package br.com.operacaoalfa

import android.net.Uri
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebView

/**
 * Custom WebChromeClient for the Operação Alfa app.
 *
 * Handles:
 * - File upload: delegates file chooser requests to the MainActivity via [onFileChooser] callback,
 *   supporting image selection (camera and gallery) and generic file selection (Req 5.1, 5.2, 5.3, 5.4)
 *
 * The actual file picker intent creation and ActivityResultLauncher registration live in MainActivity
 * (Task 4.3). This client simply forwards the WebView's file chooser request through the callback.
 *
 * @param onFileChooser  Callback invoked when the web content requests a file upload.
 *                       Receives the [ValueCallback] to deliver the selected file URI(s) back to the WebView,
 *                       and the [FileChooserParams] describing the accepted MIME types and capture mode.
 *                       Should return `true` if the request was handled, `false` otherwise.
 */
class AppWebChromeClient(
    private val onFileChooser: (ValueCallback<Array<Uri>>, FileChooserParams) -> Boolean
) : WebChromeClient() {

    /**
     * Called when the web content requests a file chooser (e.g. `<input type="file">`).
     *
     * Delegates entirely to the [onFileChooser] callback so that MainActivity can:
     * - Build an intent chooser with camera capture and gallery/file picker options (Req 5.4)
     * - Launch the intent via ActivityResultLauncher
     * - Deliver the selected URI(s) back through [filePathCallback] (Req 5.1, 5.2)
     * - Handle cancellation by sending `null` to [filePathCallback] (Req 5.3)
     *
     * @param webView            The WebView that initiated the request
     * @param filePathCallback   Callback to deliver the selected file URI(s) back to the WebView
     * @param fileChooserParams  Parameters describing accepted MIME types and capture mode
     * @return `true` if the request was handled by the callback, `false` otherwise
     */
    override fun onShowFileChooser(
        webView: WebView,
        filePathCallback: ValueCallback<Array<Uri>>,
        fileChooserParams: FileChooserParams
    ): Boolean {
        return onFileChooser(filePathCallback, fileChooserParams)
    }
}
