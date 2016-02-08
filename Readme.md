# Laravel Mailer

Wrapper with unified interface for sending emails with Mandrill or SendGrid with use of templates mechanism.

## Overview
This package provides wrapper for both Mandrill and SendGrid email sending engines and utilizes possibility to send emails with use of already prepared templates.

For more info, please check:
- https://mandrill.zendesk.com/hc/en-us/articles/205582507-Getting-Started-with-Templates (for Mandrill)
- https://sendgrid.com/docs/API_Reference/Web_API_v3/Transactional_Templates/index.html (for SendGrid)

Both mailers have unified interface, so changing your email sending engine is pretty painless.

Package also adds possibility to configure your Mandrill templates in the code and then seed them into your Mandrill account. Possibility to seed templates into SendGrid is work in progress.

## Compatibility
Package is compatible with Laravel 5.2.

## Installation

### Version >= 2.0
1. Add `hydreflab/laravel-mailer` to your `composer.json`:

   ```json
    {
        "require": {
            "hydreflab/laravel-mailer": "2.*"
        }
    }
   ```
  * If you will be using Mandrill, add `weblee/mandrill` to your `composer.json`:

    ```json
    {
        "require": {
            "weblee/mandrill": "dev-master"
        }
    }
    ```
  * If you will be using SendGrid, add `sendgrid/sendgrid` to your `composer.json`:

    ```json
    {
        "require": {
            "sendgrid/sendgrid": "4.0.*"
        }
    }
    ```

2. Add `DeSmart\Mailer\MailerServiceProvider::class` to your `config/app.php` file
3. Publish mailer configuration as well as Mandrill templates configuration: `php artisan vendor:publish`. This will create `config/mailer.php` and `config/mandrill-templates.php` files
4. Mailer configuration is based on .env entries:

   ```json
    MAILER_DRIVER=mandrill or sendgrid
    MAILER_API_KEY=YOUR_API_KEY
    DEFAULT_MAIL_FROM=mailer@example.com
    DEFAULT_MAIL_NAME=Mailer
   ```
5. _(Mandrill usage only)_ In `config/mandrill-templates.php` you can configure your Mandrill templates. This configuration is used by `mandrill_templates:seed` artisan command

### Version < 2.0
_Package implements wrapper only for Mandrill._

1. Add `hydreflab/laravel-mailer` to your `composer.json`:

   ```json
    {
        "require": {
            "hydreflab/laravel-mailer": "1.*"
        }
    }
   ```
2. Add `DeSmart\Mailer\ServiceProvider\MandrillServiceProvider::class` to your `config/app.php` file
3. Publish Mandrill templates configuration: `php artisan vendor:publish`. This will create `config/mandrill-templates.php` file where you can configure your templates. This configuration is used by `mandrill_templates:seed` artisan command
4. Mandrill mailer uses configuration for default sender email and name. You can find it inside `config/mail.php` file:

   ```php
    'from' => ['address' => null, 'name' => null],
   ```
5. Set up proper API key in your `.env` file:

   ```json
    MANDRILL_SECRET=YOUR_API_KEY
   ```

## Usage
### PHP
```php
class Notifier
{
    protected $mailer;

    public function __construct(\DeSmart\Mailer\MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notify()
    {
        // If you want to override default sender email and name
        $this->mailer->setFromEmail('johndoe@example.com');
        $this->mailer->setFromName('John Doe');

        // To add recipient
        $this->mailer->addRecipient(new Recipient('Jane Doe', 'janedoe@example.com'));
        // or
        $this->mailer->addRecipient(new Recipient('Jane Doe', 'janedoe@example.com'), RecipientType::to());

        // To add BCC recipient
        $this->mailer->addRecipient(new Recipient('Jane Doe', 'janedoe@example.com', RecipientType::bcc()));

        // To add CC recipient
        $this->mailer->addRecipient(new Recipient('Jane Doe', 'janedoe@example.com'), RecipientType::cc());

        // To add global variable (shared between all recipients)
        $this->mailer->addGlobalVariable(new Variable('variable_name', 'variable_value');

        // To add local variable (for specified recipient)
        $this->mailer->addLocalVariable(
            new Recipient('John Doe', 'johndoe@example.com'),
            new Variable('variable_name', 'variable_value')
        );

        // To add attachment
        $this->mailer->addAttachment(new Attachment('application/pdf', 'attachment.pdf', 'PDF content'));

        // To set reply to
        $this->mailer->setReplyTo('reply-to@example.com');
        // or (Mandrill only)
        $this->mailer->addHeader(new Header('Reply-To', 'reply-to@example.com')

        // To send email with subject 'test-subject' and use 'test-template' template id
        $this->mailer->send('Test subject', 'test-template');
    }
}
```

### Mandrill templates
In order to configure your templates, edit `config/mandrill-templates.php` file:
```php
'User registration email' => [
    'from_email' => null,
    'from_name' => null,
    'subject' => 'Confirm Your Account!',
    'code' => file_get_contents(__DIR__ . '/mandrill-templates/user-registration-email.phtml'),
    'text' => null,
    'publish' => true,
    'labels' => array(),
],
```
When your configuration is ready, you can run `php artisan mandrill_templates:seed` command to create and/or update Mandrill templates.