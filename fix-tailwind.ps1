# Fix Tailwind CSS Setup for Windows
# Run this script in PowerShell

Write-Host "🔧 Fixing Tailwind CSS Setup" -ForegroundColor Green
Write-Host "============================" -ForegroundColor Green

# Function to print colored output
function Write-Status {
    param([string]$Message)
    Write-Host "✅ $Message" -ForegroundColor Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "⚠️  $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "❌ $Message" -ForegroundColor Red
}

# Navigate to frontend directory
Set-Location frontend

Write-Status "Checking current setup..."

# Check if node_modules exists
if (-not (Test-Path "node_modules")) {
    Write-Status "Installing dependencies..."
    npm install
} else {
    Write-Status "Dependencies already installed"
}

# Check if PostCSS config exists
if (-not (Test-Path "postcss.config.js")) {
    Write-Status "Creating PostCSS configuration..."
    @"
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
}
"@ | Out-File -FilePath "postcss.config.js" -Encoding UTF8
} else {
    Write-Status "PostCSS configuration exists"
}

# Check if Tailwind config exists
if (-not (Test-Path "tailwind.config.js")) {
    Write-Status "Creating Tailwind configuration..."
    @"
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
"@ | Out-File -FilePath "tailwind.config.js" -Encoding UTF8
} else {
    Write-Status "Tailwind configuration exists"
}

# Check if style.css has Tailwind directives
$styleContent = Get-Content "src/style.css" -Raw
if ($styleContent -notmatch "@tailwind base") {
    Write-Status "Adding Tailwind directives to style.css..."
    @"
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
"@ | Out-File -FilePath "src/style.css" -Encoding UTF8
} else {
    Write-Status "Tailwind directives already in style.css"
}

# Reinstall Tailwind and PostCSS dependencies
Write-Status "Reinstalling Tailwind CSS dependencies..."
npm install -D tailwindcss postcss autoprefixer

# Initialize Tailwind (this will create a proper config if needed)
Write-Status "Initializing Tailwind CSS..."
npx tailwindcss init -p

# Clear any cached files
Write-Status "Clearing cache..."
if (Test-Path "node_modules/.vite") {
    Remove-Item -Recurse -Force "node_modules/.vite"
}
if (Test-Path "dist") {
    Remove-Item -Recurse -Force "dist"
}

# Test Tailwind compilation
Write-Status "Testing Tailwind compilation..."
npx tailwindcss -i ./src/style.css -o ./test-output.css --watch=false

if (Test-Path "test-output.css") {
    Write-Status "Tailwind compilation successful!"
    Remove-Item "test-output.css"
} else {
    Write-Error "Tailwind compilation failed!"
    exit 1
}

Write-Status "Tailwind CSS setup completed!"
Write-Host ""
Write-Host "To start the development server:"
Write-Host "npm run dev"
Write-Host ""
Write-Host "If you're still having issues:"
Write-Host "1. Clear browser cache"
Write-Host "2. Restart the development server"
Write-Host "3. Check browser console for errors" 