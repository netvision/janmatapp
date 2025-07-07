# Android Permissions Setup

This document explains the permission setup for the Janmat Agent Android app.

## Required Permissions

The app requires the following permissions for full functionality:

### 1. Location Permission
- **Permission**: `ACCESS_FINE_LOCATION`
- **Purpose**: To capture GPS coordinates when submitting survey responses
- **When requested**: Automatically when the survey page loads
- **User action**: User must grant permission in the system dialog

### 2. Microphone Permission
- **Permission**: `RECORD_AUDIO`
- **Purpose**: To record voice responses for surveys
- **When requested**: Automatically when the survey page loads
- **User action**: User must grant permission in the system dialog

## AndroidManifest.xml Configuration

The required permissions are already configured in `android/app/src/main/AndroidManifest.xml`:

```xml
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
<uses-permission android:name="android.permission.RECORD_AUDIO" />
```

## Permission Request Flow

1. **App Launch**: When the survey page loads, the app automatically requests both permissions
2. **System Dialogs**: Android shows permission request dialogs to the user
3. **User Response**: User can either grant or deny permissions
4. **UI Updates**: The app shows permission status and provides manual retry buttons if needed

## Permission Status Indicators

The app displays permission status in the UI:

- **Location Section**: Shows ✓ Location Access (green) or ✗ Location Access (red)
- **Voice Recording Section**: Shows ✓ Microphone Access (green) or ✗ Microphone Access (red)

## Manual Permission Requests

If permissions are denied initially, users can manually request them:

- **Location**: Click "Grant Permission" button in the location error message
- **Microphone**: Click "Grant Microphone Permission" button in the voice recording section

## Troubleshooting

### Permissions Not Requested
1. Check that the app is built with the latest code
2. Verify AndroidManifest.xml contains the required permissions
3. Check console logs for permission request messages

### Permissions Denied
1. User can manually request permissions using the UI buttons
2. If permanently denied, user must go to Android Settings > Apps > Janmat Agent > Permissions
3. Enable the required permissions manually

### Testing Permissions
1. Open the survey page in the app
2. Check console logs for permission request messages
3. Verify permission status indicators in the UI
4. Test location capture and voice recording functionality

## Development Notes

- Permissions are requested using Capacitor plugins for native Android functionality
- On web platforms, permissions are handled by the browser's standard APIs
- The app gracefully handles permission denials and provides clear error messages
- Permission status is tracked in reactive variables and updates the UI accordingly 