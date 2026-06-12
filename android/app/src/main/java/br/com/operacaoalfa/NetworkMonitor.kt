package br.com.operacaoalfa

import android.content.Context
import android.net.ConnectivityManager
import android.net.Network
import android.net.NetworkCapabilities

/**
 * Monitors network connectivity changes using ConnectivityManager (Req 6.1, 6.4).
 *
 * Exposes:
 * - [isOnline]: current connectivity state
 * - [onConnectivityChanged]: callback invoked on the main thread when connectivity changes
 *
 * Usage:
 * ```
 * val monitor = NetworkMonitor(context)
 * monitor.onConnectivityChanged = { online -> /* update UI */ }
 * monitor.register()
 * // ...
 * monitor.unregister()
 * ```
 */
class NetworkMonitor(context: Context) {

    private val connectivityManager: ConnectivityManager =
        context.getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager

    /**
     * Whether the device currently has internet connectivity.
     * Initialised by querying the active network so the value is correct
     * even before the first callback fires.
     */
    var isOnline: Boolean = checkCurrentConnectivity()
        private set

    /**
     * Optional callback invoked whenever connectivity changes.
     * The Boolean parameter is `true` when the device gains connectivity
     * and `false` when it loses connectivity.
     */
    var onConnectivityChanged: ((Boolean) -> Unit)? = null

    /**
     * Internal network callback registered with ConnectivityManager.
     *
     * - [onAvailable]: device gained a default network → online
     * - [onLost]: device lost its default network → offline
     */
    private val networkCallback = object : ConnectivityManager.NetworkCallback() {
        override fun onAvailable(network: Network) {
            updateConnectivity(true)
        }

        override fun onLost(network: Network) {
            updateConnectivity(false)
        }
    }

    /**
     * Starts monitoring connectivity changes (Req 6.4).
     * Must be balanced with a call to [unregister] to avoid leaks.
     */
    fun register() {
        connectivityManager.registerDefaultNetworkCallback(networkCallback)
    }

    /**
     * Stops monitoring connectivity changes.
     * Safe to call even if [register] was not called — the exception is caught silently.
     */
    fun unregister() {
        try {
            connectivityManager.unregisterNetworkCallback(networkCallback)
        } catch (_: IllegalArgumentException) {
            // Callback was not registered — ignore
        }
    }

    /**
     * Queries the active network to determine current connectivity.
     * Used to initialise [isOnline] so the value is accurate from the start.
     */
    private fun checkCurrentConnectivity(): Boolean {
        val network = connectivityManager.activeNetwork ?: return false
        val capabilities = connectivityManager.getNetworkCapabilities(network) ?: return false
        return capabilities.hasCapability(NetworkCapabilities.NET_CAPABILITY_INTERNET)
    }

    /**
     * Updates [isOnline] and notifies the listener when the state actually changes.
     */
    private fun updateConnectivity(online: Boolean) {
        if (isOnline != online) {
            isOnline = online
            onConnectivityChanged?.invoke(online)
        }
    }
}
