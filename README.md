# Go Theme - WordPress Multisite Development

Een WordPress multisite thema met TransIP API en OpenKVK integraties. Gebouwd met Tailwind CSS, modern PHP, en geavanceerde WordPress features.

## 🛠 Tech Stack

- PHP 8.0+
- WordPress 6.0+
- Composer voor PHP dependencies
- Node.js & NPM voor frontend tooling
- Tailwind CSS voor styling
- jQuery voor DOM manipulatie
- REST API integraties

## 🚀 Quick Start

```bash
# Clone repository
git clone [repository-url]

# Installeer PHP dependencies
composer install

# Installeer Node modules
npm install

# Start development
npm run watch

# Build voor productie
npm run build
```

## 📁 Project Structuur

```
go-theme/
├── assets/                 # Frontend assets
│   ├── css/               # Tailwind & custom CSS
│   ├── js/                # JavaScript modules
│   └── images/            # Theme images
├── includes/              # PHP classes & functions
│   ├── api/               # API integraties
│   ├── cli/               # WP-CLI commands
│   └── gravity-forms/     # GF custom fields
├── templates/             # Theme templates
├── vendor/                # Composer packages
└── node_modules/          # NPM packages
```

## 🔌 API Integraties

### TransIP API

```php
// Initialiseer de API
$transipApi = new TransIP_API(
    $_ENV['transip_api_login_name'],
    $_ENV['transip_api_private_key'],
    true,  // generateWhitelistOnlyTokens
    true   // testMode
);

// Check domein beschikbaarheid
$status = $transipApi->checkDomain('example.com');

// Registreer domein
$result = $transipApi->registerDomain('example', 'nl');
```

### OpenKVK API

```php
// Endpoint handler
add_action('wp_ajax_gf_openkvk_autocomplete', 'handle_openkvk_autocomplete');
add_action('wp_ajax_nopriv_gf_openkvk_autocomplete', 'handle_openkvk_autocomplete');

function handle_openkvk_autocomplete() {
    check_ajax_referer('gf-openkvk-autocomplete-nonce', 'nonce');
    $query = sanitize_text_field($_GET['q']);
    // API logic here
}
```

## 🔧 Development Tools

### WP-CLI Commands

```bash
# TransIP API commands
wp transip test-connection --test
wp transip check-domain --domain=example.com
wp transip register-domain --domain=example --test

# Cache management
wp cache flush
```

### Environment Variables

```env
# Required API credentials
transip_api_login_name=
transip_api_private_key=
transip_api_demo_token=
overheid_io_api_development_key=

# Optional settings
environment=development
intake_form_id=1
```

## 🎨 Frontend Development

### Tailwind CSS

```bash
# Watch mode
npm run watch

# Production build
npm run production
```

Custom Tailwind configuratie in `tailwind.config.js`:
```javascript
module.exports = {
    content: [
        './templates/**/*.php',
        './includes/**/*.php',
        './assets/js/**/*.js'
    ],
    // ... custom config
};
```

### JavaScript Modules

Client-side caching implementatie:
```javascript
const cache = {
    set: function(query, data) {
        localStorage.setItem(CACHE_PREFIX + query, JSON.stringify({
            timestamp: Date.now(),
            data: data
        }));
    },
    get: function(query) {
        const item = localStorage.getItem(CACHE_PREFIX + query);
        if (!item) return null;
        // Cache validation logic
    }
};
```

## 🔒 Security

- Gebruik `wp_nonce` voor AJAX calls
- API keys in `.env` file (niet in versie control)
- XSS preventie met `esc_*` functies
- Input sanitization met WordPress functies
- CORS headers voor API requests

## 🧪 Testing

1. **API Testing**
   - Gebruik test mode voor TransIP API
   - Mock responses voor development
   - Error handling tests

2. **Frontend Testing**
   - Browser compatibility (laatste 2 versies)
   - Responsive design testing
   - Performance monitoring

## 📦 Deployment

1. Build productie assets:
```bash
npm run production
```

2. Verifieer environment variables:
```bash
wp dotenv list
```

3. Clear caches:
```bash
wp cache flush
```

## 🐛 Debugging

- WP_DEBUG ingeschakeld in development
- Error logging naar `/logs` directory
- Browser console logging voor JS
- API response logging

## 📚 Dependencies

### PHP Packages
- `transip/transip-api-php`: ^6.0
- `vlucas/phpdotenv`: ^5.0

### NPM Packages
- `tailwindcss`: ^3.0
- `@babel/core`: ^7.0
- `webpack`: ^5.0

## 👥 Contributing

1. Fork de repository
2. Maak een feature branch
3. Commit je wijzigingen
4. Push naar de branch
5. Open een Pull Request

## 📝 License

Proprietary - © Geregeld Online
