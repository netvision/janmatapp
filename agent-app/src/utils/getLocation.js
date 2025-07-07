// src/utils/getLocation.js
import { Capacitor } from '@capacitor/core'
import { Geolocation } from '@capacitor/geolocation'

export async function getCurrentLocation() {
  // Check if running in a native environment (Capacitor)
  if (Capacitor.isNativePlatform()) {
    try {
      const pos = await Geolocation.getCurrentPosition()
      return {
        latitude: pos.coords.latitude,
        longitude: pos.coords.longitude,
        accuracy: pos.coords.accuracy,
        source: 'capacitor'
      }
    } catch (e) {
      // Fallback to browser if Capacitor fails
      console.warn('Capacitor Geolocation failed, falling back to browser:', e)
    }
  }

  // Fallback: use browser geolocation
  if ('geolocation' in navigator) {
    return new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          resolve({
            latitude: pos.coords.latitude,
            longitude: pos.coords.longitude,
            accuracy: pos.coords.accuracy,
            source: 'browser'
          })
        },
        (err) => {
          reject(err)
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 60000
        }
      )
    })
  } else {
    throw new Error('Geolocation not supported')
  }
} 