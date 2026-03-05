# Synapse Professional Theme

A modern, professional theme for Laravel Synapse applications featuring comprehensive UI components, dark mode support, and designer-friendly customization.

## 🎨 Theme Features

✅ **Responsive sidebar** with collapse functionality
✅ **Dark mode support** via `@media (prefers-color-scheme: dark)`
✅ **Modern gradient design** with oklch color system
✅ **Comprehensive component library** (cards, buttons, forms, tables, badges)
✅ **Alpine.js interactive components** (modals, notifications, dropdowns)
✅ **Smooth animations** and transitions
✅ **Accessibility-first** design with ARIA attributes
✅ **Mobile-optimized** layouts

## 📂 File Structure

```
default/
├── theme.json          # Theme metadata and CDN resources
├── README.md           # This file
├── assets/
│   ├── images/
│   │   ├── logo.svg        # Light mode logo
│   │   └── logo-dark.svg   # Dark mode logo variant
│   └── fonts/             # Custom fonts (if needed)
├── css/
│   └── theme.css       # Custom Tailwind v4 styles
├── js/
│   └── theme.js        # Alpine.js components and utilities
└── layouts/
    ├── app.blade.php       # Backend/admin layout
    ├── guest.blade.php     # Authentication pages layout
    └── frontend.blade.php  # Public website layout
```

## 🚀 Quick Start for Designers

**No PHP knowledge required!** This theme is designed to be customized by designers using familiar web technologies.

### 1. Customize Colors

Edit `css/theme.css` and modify the `@theme` block:

```css
@theme {
  /* Brand Colors - Modern Blue Palette */
  --color-brand-500: oklch(0.55 0.20 240);  /* Primary blue */
  --color-brand-600: oklch(0.45 0.18 240);  /* Darker blue */

  /* Change the hue (240) to customize:
     0-60: Red/Orange
     60-180: Yellow/Green
     180-270: Blue/Purple
     270-360: Purple/Red
  */
}
```

**OKLCH Color System:**
- **L** (Lightness): 0-1 (0 = black, 1 = white)
- **C** (Chroma): 0-0.4 (saturation/intensity)
- **H** (Hue): 0-360 (color angle)

[OKLCH Color Picker Tool](https://oklch.com/)

### 2. Update Logo

Replace logo files in `assets/images/`:
- `logo.svg` - Used in light mode
- `logo-dark.svg` - Used in dark mode (optional)

### 3. Customize Spacing

Edit layout spacing variables in `css/theme.css`:

```css
@theme {
  --spacing-sidebar-width: 16rem;      /* Sidebar width when open */
  --spacing-sidebar-collapsed: 4rem;   /* Sidebar width when collapsed */
  --spacing-header-height: 4rem;       /* Header height */
  --spacing-content-padding: 2rem;     /* Main content padding */
}
```

### 4. Add External Resources (CDN)

Edit `theme.json` to add external CSS/JS:

```json
{
  "css": {
    "all": ["https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"],
    "app": ["https://cdn.example.com/admin-styles.css"],
    "frontend": ["https://cdn.example.com/frontend-styles.css"]
  },
  "js": {
    "all": ["https://cdn.example.com/analytics.js"],
    "app": []
  }
}
```

**Layout Types:**
- `all` - Loaded on every page
- `app` - Backend/admin pages only (URL starts with backend prefix)
- `guest` - Authentication pages (login, register)
- `frontend` - Public website pages

## 🎯 Using Theme Assets

### In Blade Templates

```blade
<!-- Logo -->
<img src="{{ theme_asset('images/logo.svg') }}" alt="Logo">

<!-- Dark mode logo variant -->
<img src="{{ theme_asset('images/logo-dark.svg') }}"
     alt="Logo"
     class="hidden dark:block">

<!-- Custom image -->
<img src="{{ theme_asset('images/hero-banner.jpg') }}" alt="Hero">
```

### Helper Functions

```php
// Get asset URL
theme_asset('images/logo.svg')
// → http://localhost/assets/themes/default/images/logo.svg

// Get theme file path
theme_path('css/theme.css')
// → /var/www/synapps/resources/themes/default/css/theme.css

// Get active theme name
active_theme()
// → 'default'
```

## 🧩 Available Components

### Sidebar

```html
<div x-data="themeSidebar" class="theme-sidebar" :class="{ 'collapsed': collapsed }">
    <div class="theme-sidebar-logo">
        <img src="{{ theme_asset('images/logo.svg') }}" alt="Logo">
        <span>App Name</span>
    </div>

    <nav class="theme-sidebar-nav">
        <a href="/dashboard" class="theme-sidebar-nav-item active">
            <i class="fa-solid fa-home"></i>
            <span>Dashboard</span>
        </a>
    </nav>
</div>

<!-- Toggle Button -->
<button @click="toggle()" class="theme-btn-primary">
    Toggle Sidebar
</button>
```

### Cards

```html
<div class="theme-card">
    <div class="theme-card-header">
        <h3 class="theme-card-title">Card Title</h3>
        <button class="theme-btn-secondary">Action</button>
    </div>
    <div class="theme-card-body">
        Card content goes here...
    </div>
    <div class="theme-card-footer">
        Footer content
    </div>
</div>
```

### Buttons

```html
<button class="theme-btn theme-btn-primary">Primary Button</button>
<button class="theme-btn theme-btn-secondary">Secondary Button</button>
<button class="theme-btn theme-btn-success">Success Button</button>
<button class="theme-btn theme-btn-danger">Danger Button</button>
```

### Forms

```html
<div class="theme-form-group">
    <label class="theme-form-label">Email Address</label>
    <input type="email" class="theme-form-input" placeholder="you@example.com">
    <div class="theme-form-error">This field is required</div>
</div>
```

### Badges

```html
<span class="theme-badge theme-badge-primary">Primary</span>
<span class="theme-badge theme-badge-success">Success</span>
<span class="theme-badge theme-badge-warning">Warning</span>
<span class="theme-badge theme-badge-danger">Danger</span>
```

### Tables

```html
<table class="theme-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>John Doe</td>
            <td>john@example.com</td>
            <td><span class="theme-badge theme-badge-success">Active</span></td>
        </tr>
    </tbody>
</table>
```

## ⚡ Alpine.js Components

This theme includes ready-to-use Alpine.js components:

### Modal

```html
<div x-data="modal()">
    <button @click="open()" class="theme-btn-primary">Open Modal</button>

    <div x-show="isOpen"
         @click.away="close()"
         @keydown.escape="closeOnEscape($event)"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white p-6 rounded-xl max-w-md">
            <h3 class="text-xl font-bold mb-4">Modal Title</h3>
            <p>Modal content...</p>
            <button @click="close()" class="theme-btn-secondary mt-4">Close</button>
        </div>
    </div>
</div>
```

### Toast Notifications

```html
<div x-data="toasts()" class="fixed top-4 right-4 z-50 space-y-2">
    <template x-for="toast in items" :key="toast.id">
        <div x-show="toast.visible"
             x-transition
             class="bg-white px-6 py-4 rounded-lg shadow-lg"
             :class="{
                 'border-l-4 border-green-500': toast.type === 'success',
                 'border-l-4 border-red-500': toast.type === 'error'
             }">
            <p x-text="toast.message"></p>
        </div>
    </template>
</div>

<!-- Trigger -->
<button @click="success('Operation completed!')">Show Success</button>
```

### Dropdown

```html
<div x-data="dropdown()">
    <button @click="toggle()" class="theme-header-button">
        <i class="fa-solid fa-user"></i>
    </button>

    <div x-show="isOpen"
         @click.away="close()"
         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg">
        <a href="/profile" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
        <a href="/settings" class="block px-4 py-2 hover:bg-gray-100">Settings</a>
        <a href="/logout" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
    </div>
</div>
```

### Theme Toggle (Dark Mode)

```html
<div x-data="themeToggle()">
    <button @click="toggle()" class="theme-header-button">
        <i class="fa-solid" :class="dark ? 'fa-moon' : 'fa-sun'"></i>
    </button>
</div>
```

### Global Search

```html
<div x-data="globalSearch()">
    <input type="text"
           x-model="query"
           @input.debounce.300ms="search()"
           placeholder="Search... (Ctrl+K)"
           class="theme-form-input">

    <div x-show="isOpen && results.length > 0">
        <template x-for="result in results">
            <a @click="selectResult(result)" x-text="result.title"></a>
        </template>
    </div>
</div>
```

## 🎭 Keyboard Shortcuts

- **Ctrl/Cmd + K** - Focus global search
- **Ctrl/Cmd + B** - Toggle sidebar
- **Escape** - Close modals

## 🌙 Dark Mode Support

Dark mode is automatically activated based on system preference:

```css
@media (prefers-color-scheme: dark) {
  body {
    @apply bg-gray-900 text-gray-100;
  }

  .theme-card {
    @apply bg-gray-800 border-gray-700;
  }
}
```

Users can also toggle manually using the `themeToggle()` component.

## 📱 Responsive Design

All components are mobile-optimized:
- Sidebar collapses automatically on mobile
- Tables scroll horizontally
- Forms stack vertically
- Modals adjust to screen size

## 🔧 Build & Deploy

### Development Mode (with live reload)

```bash
npm run dev
```

### Production Build

```bash
npm run build
```

This will compile your theme assets to `public/assets/themes/default/`

### Activate Theme

Edit `synapps/config/apps.json`:

```json
{
  "themes": {
    "active": "default",
    "fallback": "default"
  }
}
```

Then regenerate config:

```bash
php artisan synapps:config
```

## 📚 Learning Resources

### Tailwind CSS v4
- [Official Docs](https://tailwindcss.com/docs)
- [Tailwind v4 Beta](https://tailwindcss.com/docs/v4-beta)
- [OKLCH Colors](https://oklch.com/)

### Alpine.js
- [Alpine.js Docs](https://alpinejs.dev)
- [Alpine.js Examples](https://alpinejs.dev/start-here)

### FontAwesome Icons
- [Icon Search](https://fontawesome.com/icons)
- Usage: `<i class="fa-solid fa-icon-name"></i>`

## 🎨 Advanced Customization

### Creating a Custom Theme

```bash
# Create new theme based on default
php artisan synapps:make-theme my-theme --copy-from=default

# Edit the new theme
cd synapps/resources/themes/my-theme
# Customize css/theme.css, js/theme.js, layouts/

# Activate your theme
# Edit synapps/config/apps.json: "active": "my-theme"

# Regenerate config
php artisan synapps:config

# Build assets
npm run build
```

### Custom Animations

Add to `css/theme.css`:

```css
@keyframes customSlide {
  from { transform: translateX(-100%); }
  to { transform: translateX(0); }
}

.my-custom-animation {
  animation: customSlide 0.3s ease-in-out;
}
```

### Custom Alpine Components

Add to `js/theme.js`:

```javascript
Alpine.data('myComponent', () => ({
  count: 0,
  increment() {
    this.count++;
  }
}));
```

Use in Blade:

```html
<div x-data="myComponent">
    <button @click="increment()">Count: <span x-text="count"></span></button>
</div>
```

## 📝 CSS Class Naming Convention

This theme uses a prefix system for clarity:

- `.theme-*` - Theme-specific components
- `.theme-sidebar-*` - Sidebar related
- `.theme-header-*` - Header related
- `.theme-card-*` - Card components
- `.theme-btn-*` - Button variants
- `.theme-form-*` - Form components
- `.theme-badge-*` - Badge variants
- `.theme-table` - Table component

This prevents conflicts with Tailwind utilities and other plugins.

## 🐛 Troubleshooting

**Theme assets not copying?**
```bash
# Re-run patch command
php artisan synapps:patch-resources

# Rebuild assets
npm run build
```

**Dark mode not working?**
- Check browser/OS dark mode setting
- Or use `themeToggle()` component for manual control

**Sidebar not collapsing?**
- Ensure Alpine.js is loaded
- Check browser console for errors
- Verify `x-data="themeSidebar"` is present

## 📄 License

This theme is part of Laravel Synapse and follows the same license.

## 🤝 Support

For more documentation:
- **Theming Guide**: `packages/synapse/docs/THEMING.md`
- **CSS Standards**: `packages/synapse/docs/CSS_STANDARDS.md`
- **Synapse Docs**: `packages/synapse/README.md`

---

**Made with ❤️ by VM Engine**
