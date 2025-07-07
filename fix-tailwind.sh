#!/bin/bash

echo "🔧 Fixing Tailwind CSS Setup"
echo "============================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Navigate to frontend directory
cd frontend

print_status "Checking current setup..."

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    print_status "Installing dependencies..."
    npm install
else
    print_status "Dependencies already installed"
fi

# Check if PostCSS config exists
if [ ! -f "postcss.config.js" ]; then
    print_status "Creating PostCSS configuration..."
    cat > postcss.config.js << 'EOF'
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
}
EOF
else
    print_status "PostCSS configuration exists"
fi

# Check if Tailwind config exists
if [ ! -f "tailwind.config.js" ]; then
    print_status "Creating Tailwind configuration..."
    cat > tailwind.config.js << 'EOF'
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
      },
    },
  },
  plugins: [],
}
EOF
else
    print_status "Tailwind configuration exists"
fi

# Check if style.css has Tailwind directives
if ! grep -q "@tailwind base" src/style.css; then
    print_status "Adding Tailwind directives to style.css..."
    cat > src/style.css << 'EOF'
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
  html {
    font-family: 'Inter', system-ui, sans-serif;
  }
}

@layer components {
  .btn-primary {
    @apply bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
  }
  
  .btn-secondary {
    @apply bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors duration-200;
  }
  
  .btn-danger {
    @apply bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
  }
  
  .input-field {
    @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent;
  }
  
  .card {
    @apply bg-white rounded-lg shadow-md p-6;
  }
}
EOF
else
    print_status "Tailwind directives already in style.css"
fi

# Reinstall Tailwind and PostCSS dependencies
print_status "Reinstalling Tailwind CSS dependencies..."
npm install -D tailwindcss postcss autoprefixer

# Initialize Tailwind (this will create a proper config if needed)
print_status "Initializing Tailwind CSS..."
npx tailwindcss init -p

# Clear any cached files
print_status "Clearing cache..."
rm -rf node_modules/.vite
rm -rf dist

# Test Tailwind compilation
print_status "Testing Tailwind compilation..."
npx tailwindcss -i ./src/style.css -o ./test-output.css --watch=false

if [ -f "test-output.css" ]; then
    print_status "Tailwind compilation successful!"
    rm test-output.css
else
    print_error "Tailwind compilation failed!"
    exit 1
fi

print_status "Tailwind CSS setup completed!"
echo ""
echo "To start the development server:"
echo "npm run dev"
echo ""
echo "If you're still having issues:"
echo "1. Clear browser cache"
echo "2. Restart the development server"
echo "3. Check browser console for errors" 