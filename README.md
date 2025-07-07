# Janmat Survey Admin Frontend

A Vue 3 admin panel for managing surveys, responses, and field agents.

## Features

- 📊 **Dashboard** - Overview with charts and statistics
- 📝 **Survey Management** - Create, edit, and manage surveys
- 📋 **Response Tracking** - View and analyze survey responses
- 👥 **Agent Management** - Manage field survey agents
- 🔐 **JWT Authentication** - Secure login system
- 📱 **Responsive Design** - Works on desktop and mobile

## Tech Stack

- **Vue 3** - Progressive JavaScript framework
- **Vue Router** - Official router for Vue.js
- **Pinia** - State management for Vue
- **Axios** - HTTP client for API calls
- **Tailwind CSS** - Utility-first CSS framework
- **Chart.js** - Charting library for analytics
- **Heroicons** - Beautiful SVG icons

## Setup

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Start development server:**
   ```bash
   npm run dev
   ```

3. **Build for production:**
   ```bash
   npm run build
   ```

## API Configuration

The frontend is configured to connect to the live API at:
```
https://janmat.netserve.in/api/v1
```

## Default Login

- **Username:** admin
- **Password:** admin123

## Project Structure

```
src/
├── components/     # Reusable Vue components
├── views/         # Page components
├── stores/        # Pinia state management
├── services/      # API services
├── router/        # Vue Router configuration
└── style.css      # Global styles
```

## Available Routes

- `/login` - Authentication page
- `/dashboard` - Main dashboard with analytics
- `/surveys` - Survey management
- `/surveys/create` - Create new survey
- `/surveys/:id` - Edit survey
- `/responses` - View survey responses
- `/responses/:id` - Response details
- `/agents` - Agent management
- `/agents/create` - Add new agent

## Development

The frontend uses Vite for fast development and building. Key features:

- **Hot Module Replacement** - Instant updates during development
- **ESLint** - Code linting and formatting
- **Tailwind CSS** - Utility-first styling
- **Vue DevTools** - Browser extension for debugging

## Deployment

Build the project and deploy the `dist` folder to your web server:

```bash
npm run build
```

The built files will be in the `dist` directory, ready for deployment. 