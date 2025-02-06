# Email Service voor WordPress

Een flexibele email service voor het versturen van HTML emails met dynamische content en SMTP ondersteuning.

## Features

- Centraal email design met HTML/CSS templates
- Ondersteuning voor dynamische content met placeholders
- Flexibele SMTP configuratie
- Mock mode voor testing
- Logging van alle email activiteit
- Responsive email templates
- Eenvoudig te gebruiken API

## Installatie

1. Kopieer de `email-service` directory naar je `includes` map
2. Voeg de SMTP configuratie toe aan je `.env` bestand:

```env
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=your_username
SMTP_PASSWORD=your_password
SMTP_ENCRYPTION=tls
SMTP_FROM_EMAIL=noreply@example.com
SMTP_FROM_NAME="Your Company Name"
```

## Gebruik

### Basis gebruik

```php
// Initialiseer de email service met SMTP configuratie
$smtp_config = [
    'host' => $_ENV['SMTP_HOST'],
    'port' => $_ENV['SMTP_PORT'],
    'username' => $_ENV['SMTP_USERNAME'],
    'password' => $_ENV['SMTP_PASSWORD'],
    'encryption' => $_ENV['SMTP_ENCRYPTION'],
    'from_email' => $_ENV['SMTP_FROM_EMAIL'],
    'from_name' => $_ENV['SMTP_FROM_NAME']
];

$email_service = new Email_Service($smtp_config);

// Verstuur een email met het standaard template
$email_service->send_email(
    'recipient@example.com',
    'Welkom bij onze service',
    [
        'name' => 'John Doe',
        'company_name' => 'Mijn Bedrijf',
        'logo_url' => 'https://example.com/logo.png',
        'message' => 'Bedankt voor je registratie!',
        'cta_url' => 'https://example.com/login',
        'cta_text' => 'Login op je account'
    ]
);
```

### Custom Template Gebruiken

```php
// Stel een ander template in als standaard
$email_service->set_default_template('welcome');

// Of specificeer een template bij het versturen
$email_service->send_email(
    'recipient@example.com',
    'Welkom bij onze service',
    [
        'name' => 'John Doe',
        'custom_content' => '<p>Je custom HTML hier</p>'
    ],
    'custom-template'
);
```

### Mock Mode voor Testing

```php
// Activeer mock mode (geen echte emails worden verstuurd)
$email_service->set_mock_mode(true);

// Test email versturen
$email_service->send_email(
    'test@example.com',
    'Test Email',
    ['name' => 'Test User']
);
```

## Template Variabelen

Het standaard template ondersteunt de volgende variabelen:

- `{{subject}}` - Email onderwerp
- `{{name}}` - Naam van de ontvanger
- `{{company_name}}` - Bedrijfsnaam
- `{{logo_url}}` - URL naar het bedrijfslogo
- `{{message}}` - Hoofdbericht
- `{{custom_content}}` - Custom HTML content
- `{{cta_url}}` - Call-to-action link URL
- `{{cta_text}}` - Call-to-action button tekst
- `{{footer_text}}` - Custom footer tekst

## Custom Templates

Je kunt eigen templates maken door PHP bestanden toe te voegen in de `templates` directory. Templates hebben toegang tot alle variabelen die je meegeeft in de `$data` array bij `send_email()`.

## Logging

Alle email activiteit wordt gelogd met de volgende informatie:
- Succes/fout status
- Ontvanger
- Onderwerp
- Gebruikt template
- Timestamp

De logs zijn te vinden in `logs/email_service_YYYY-MM-DD.log`. 