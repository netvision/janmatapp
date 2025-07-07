# Frontend Troubleshooting Guide

## Common Issues and Solutions

### 1. Page Looks Strange or Blank

**Symptoms:**
- Page appears blank or with broken styling
- Console shows errors
- Components not loading properly

**Solutions:**

1. **Check Console Errors:**
   ```bash
   # Open browser developer tools (F12)
   # Look for errors in Console tab
   ```

2. **Clear Cache and Restart:**
   ```bash
   # Stop the dev server (Ctrl+C)
   # Clear node_modules and reinstall
   rm -rf node_modules package-lock.json
   npm install
   npm run dev
   ```

3. **Test Debug Page:**
   - Navigate to `http://localhost:3000/debug`
   - This will show if Vue is working properly

### 2. API Connection Issues

**Symptoms:**
- Login fails
- Data not loading
- Network errors in console

**Solutions:**

1. **Check API Endpoint:**
   - Verify API is running at `https://janmat.netserve.in/api/v1`
   - Test with: `curl https://janmat.netserve.in/api/v1/health`

2. **Check CORS:**
   - Ensure backend has CORS properly configured
   - Check browser console for CORS errors

3. **Test API Connection:**
   - Go to `/debug` page
   - Click "Test API Connection" button

### 3. Authentication Issues

**Symptoms:**
- Can't login
- Redirected to login repeatedly
- Token not working

**Solutions:**

1. **Check Default Credentials:**
   - Username: `admin`
   - Password: `admin123`

2. **Clear Local Storage:**
   ```javascript
   // In browser console
   localStorage.clear()
   // Then refresh page
   ```

3. **Check Token:**
   ```javascript
   // In browser console
   console.log(localStorage.getItem('token'))
   ```

### 4. Styling Issues

**Symptoms:**
- Components look unstyled
- Tailwind classes not working
- Layout broken

**Solutions:**

1. **Check Tailwind CSS:**
   - Ensure `style.css` is imported in `main.js`
   - Verify `tailwind.config.js` is correct

2. **Check Custom Classes:**
   - Verify `.btn-primary`, `.input-field`, `.card` classes are defined
   - Check `style.css` for component classes

### 5. Development Server Issues

**Symptoms:**
- Can't start dev server
- Port conflicts
- Hot reload not working

**Solutions:**

1. **Check Port:**
   ```bash
   # Default port is 3000
   # If busy, change in vite.config.js
   server: {
     port: 3001  # or any available port
   }
   ```

2. **Kill Existing Process:**
   ```bash
   # On Windows
   netstat -ano | findstr :3000
   taskkill /PID <PID> /F
   
   # On Linux/Mac
   lsof -ti:3000 | xargs kill -9
   ```

3. **Restart Dev Server:**
   ```bash
   npm run dev
   ```

## Debug Steps

1. **Start with Debug Page:**
   - Go to `http://localhost:3000/debug`
   - Check if Vue is working

2. **Check Browser Console:**
   - Open Developer Tools (F12)
   - Look for errors in Console tab
   - Check Network tab for failed requests

3. **Test API Endpoints:**
   ```bash
   curl https://janmat.netserve.in/api/v1/health
   curl -X POST https://janmat.netserve.in/api/v1/auth/login \
     -H "Content-Type: application/json" \
     -d '{"username":"admin","password":"admin123"}'
   ```

4. **Check File Structure:**
   ```
   frontend/
   ├── src/
   │   ├── views/          # All view components
   │   ├── stores/         # Pinia stores
   │   ├── services/       # API services
   │   ├── router/         # Vue Router
   │   ├── App.vue         # Main app component
   │   └── main.js         # Entry point
   ├── package.json        # Dependencies
   ├── vite.config.js      # Vite configuration
   └── tailwind.config.js  # Tailwind configuration
   ```

## Getting Help

If you're still having issues:

1. **Check the console errors** and share them
2. **Test the debug page** at `/debug`
3. **Verify API is working** with curl commands
4. **Check if all files are present** in the correct locations

## Quick Fix Commands

```bash
# Complete reset
cd frontend
rm -rf node_modules package-lock.json
npm install
npm run dev

# Test API
curl https://janmat.netserve.in/api/v1/health

# Check if port is free
netstat -ano | findstr :3000  # Windows
lsof -i :3000                 # Linux/Mac
``` 