# Janmat Agent Application

A mobile-optimized Vue.js application for agents to collect survey responses from the public.

## Features

- **Mobile-First Design**: Optimized for mobile devices with touch-friendly interface
- **Agent Authentication**: Secure login for authorized agents
- **Survey Management**: View and access active surveys
- **Response Collection**: Easy-to-use forms for collecting survey responses
- **Offline Support**: Basic offline functionality (planned)
- **Real-time Sync**: Automatic synchronization with backend

## Tech Stack

- **Frontend**: Vue 3 with Composition API
- **Build Tool**: Vite
- **Styling**: Tailwind CSS
- **State Management**: Pinia
- **Routing**: Vue Router
- **HTTP Client**: Axios
- **Mobile Optimization**: Custom CSS and meta tags

## Setup Instructions

### Prerequisites

- Node.js 16+ 
- npm or yarn

### Installation

1. **Install dependencies**:
   ```bash
   npm install
   ```

2. **Start development server**:
   ```bash
   npm run dev
   ```

3. **Build for production**:
   ```bash
   npm run build
   ```

4. **Preview production build**:
   ```bash
   npm run preview
   ```

## Development

The application runs on `http://localhost:3001` by default.

### Project Structure

```
src/
├── components/     # Reusable Vue components
├── views/         # Page components
├── services/      # API services
├── stores/        # Pinia stores
├── router/        # Vue Router configuration
├── style.css      # Global styles
├── main.js        # Application entry point
└── App.vue        # Root component
```

## API Integration

The application integrates with the Janmat backend API:

- **Base URL**: `https://janmat.netserve.in/api/v1`
- **Authentication**: Bearer token in Authorization header
- **Endpoints**:
  - `/auth/login` - Agent authentication
  - `/surveys` - Get active surveys
  - `/surveys/:id` - Get survey details
  - `/responses/submit` - Submit survey responses
  - `/responses/analytics` - Get response statistics

## Deployment

### Web Deployment
1. Build the application: `npm run build`
2. Deploy the `dist` folder to your web server
3. Configure your server to serve the SPA correctly

### Mobile App (Future)
The application is designed to be easily converted to a mobile app using:
- Capacitor (for iOS/Android)
- PWA (Progressive Web App)
- React Native (if needed) 