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

### SendGrid
New SendGrid API has some downsides when it comes to sending emails to multiple recipients. In order not to break anything, SendGrid mailer treats all recipients as of `to` type.

For more information, please check https://github.com/sendgrid/sendgrid-php#please-read-this.

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

2. Add `DeSmart\Mailer\MailerServiceProvider::class` to your `config/app.php` file.
3. Publish mailer configuration: `php artisan vendor:publish`. This will create `config/mailer.php` file.
4. Mailer configuration is based on `.env` entries:

   ```json
    MAILER_DRIVER=mandrill or sendgrid
    MAILER_API_KEY=YOUR_API_KEY
    DEFAULT_MAIL_FROM=mailer@example.com
    DEFAULT_MAIL_NAME=Mailer
   ```
5. _(Mandrill usage only)_ If you want to use templates create/update functionality:
   1. Add `DeSmart\Mailer\Mandrill\MandrillTemplatesSeedCommandServiceProvider::class` to your `config/app.php` file.
   2. Publish Mandrill templates configuration: `php artisan vendor:publish`. This will create `config/mandrill-templates.php` file where you can configure your Mandrill templates. This configuration is used by `mandrill_templates:seed` artisan command.
   3. _<b>Important</b>_ Step 5.i & 5.ii should be done after installing Mailer package - this is because template seeder uses mailer configuration which has to be published first.

### Version < 2.0
_Package implements wrapper only for Mandrill._

_If possible, please use version >= 2.0_

1. Add `hydreflab/laravel-mailer` to your `composer.json`:

   ```json
    {
        "require": {
            "hydreflab/laravel-mailer": "1.*"
        }
    }
   ```
2. Add `DeSmart\Mailer\ServiceProvider\MandrillServiceProvider::class` to your `config/app.php` file.
3. Publish Mandrill templates configuration: `php artisan vendor:publish`. This will create `config/mandrill-templates.php` file where you can configure your templates. This configuration is used by `mandrill_templates:seed` artisan command.
4. Mandrill mailer uses configuration for default sender email and name. You can find it inside `config/mail.php` file:

   ```php
    'from' => ['address' => null, 'name' => null],
   ```
5. Set up proper API key in your `.env` file:

   ```json
    MANDRILL_SECRET=YOUR_API_KEY
   ```

## Interface overview
Package provides unified interface for both mailers:
```php
interface MailerInterface
{
    /**
     * @param string $email
     * @return void
     */
    public function setFromEmail($email);

    /**
     * @param string $name
     * @return void
     */
    public function setFromName($name);

    /**
     * @param string $subject
     * @return void
     */
    public function setSubject($subject);

    /**
     * @param string $template
     * @return void
     */
    public function setTemplate($template);

    /**
     * @param string|null $subject
     * @param string|null $template
     * @return bool
     */
    public function send($subject = null, $template = null);

    /**
     * @param string $queue
     * @param string|null $subject
     * @param string|null $template
     * @return bool
     */
    public function queue($queue, $subject = null, $template = null);

    /**
     * @param Recipient $recipient
     * @return void
     */
    public function addRecipient(Recipient $recipient);

    /**
     * @param Header $header
     * @return void
     */
    public function addHeader(Header $header);

    /**
     * @param string $email
     * @return void
     */
    public function setReplyTo($email);

    /**
     * @param Variable $variable
     * @return void
     */
    public function addGlobalVariable(Variable $variable);

    /**
     * @param Recipient $recipient
     * @param Variable $variable
     * @return void
     */
    public function addLocalVariable(Recipient $recipient, Variable $variable);

    /**
     * @param Attachment $attachment
     * @return void
     */
    public function addAttachment(Attachment $attachment);

    /**
     * @return array
     */
    public function getData();

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data);
}
```

- `setFromEmail()`: overrides default email sender; takes `string` argument
- `setFromName()`: overrides default email sender name; takes `string` argument
- `setSubject()`: sets email subject; takes `string` argument
- `setTemplate()`: sets template identifier; takes `string` argument
- `addRecipient()`: adds recipient to the message; requires `Recipient` object as argument
- `addHeader()`: adds proper SMTP header to the message; requires `Header` object as argument
- `setReplyTo()`: sets reply-to email; takes `string` argument
- `addGlobalVariable()`: adds variable shared by all recipients (in Mandrill it is equivalent to global merge var, in SendGrid it is equivalent to section); requires `Variable` object as argument
- `addLocalVariable()`: adds variable for specified recipient (in Manrdill it is equivalent to merge var, in SendGrid it is equivalent to substitution); requires `Recipient` and `Variable` objects as arguments
- `addAttachment()`: adds attachment to the message; requires `Attachment` object as argument
- `send()`: sends message to previously defined recipients; email subject (`string`) and template identifier (`string`) can be passed (if subject and/or template id was not set before)
- `queue()`: adds email to queue (**it uses Laravel queue mechanism**); queue name (`string`) must be passed as first param; email subject (`string`) and template identifier (`string`) can also be passed (if not set before)
- `getData()`: gets mailer whole configuration, i.e. recipients, variables, header, etc.; returns `array`
- `setData()`: sets mailer configuration; requires `array`

### Recipient object
Recipient object describes details of recipient.

Recipient object takes three arguments:
- recipient name: `string`
- recipient email: `string`
- recipient type (optional): `RecipientType` object (if recipient type is not passed, type is set as `to`)

### RecipientType object
RecipientType object describes type of recipient - is he either:
- primary recipient (`to`) or
- should be in copy (`cc`) or
- should be in hidden copy (`bcc`).

RecipientType object is simple value object with named constructors:
- `RecipientType::to()`
- `RecipientType::bcc()`
- `RecipientType::cc()`

### Variable object
Variable object describes details of variable that is used for personalizing email content. Variable (defined by its name) is placed in email templates.

Variable object takes two arguments:
- variable name: `string`
- variable value: `string`

### Header object
Header object described SMTP header details. for more details about acceptable headers, check Mandrill/SendGrid API docs.

Header object takes two arguments:
- variable name: `string`
- variable value: `string`

### Attachment object
Attachment object contains data about file attached to the message. Content set in object should be in plain text.

For Mandrill, content is base64 encoded when passed to API. 

For SendGrid, temporary file with content is created, then path to this file is passed to API. After message is sent, temporary file is deleted.

Attachment object takes three arguments:
- attachment MIME type: `string`
- attachment filename: `string`
- attachment content (in plain text): `string`

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
        
        // You can set subject and template identifier
        $this->mailer->setSubject('Test subject');
        $this->mailer->setTemplate('test-template');

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
        $this->mailer->addAttachment(new Attachment('application/pdf', 'attachment.pdf', 'PDF content in plain text'));
        $this->mailer->addAttachment(new Attachment('text/html', 'attachment.txt', 'Txt file content in plain text'));

        // To set reply to
        $this->mailer->setReplyTo('reply-to@example.com');
        // or (Mandrill only)
        $this->mailer->addHeader(new Header('Reply-To', 'reply-to@example.com')

        // To send email
        $this->mailer->send();
        // or to send email if subject and/or template was not set before
        $this->mailer->send('Test subject', 'test-template');
        
        // To add email to queue
        $this->mailer->queue();
        // or to add email to defined queue 
        $this->mailer->queue('queue_name');
        // or to add email to queue if subject and/or template was not set before
        $this->mailer->queue('queue_name', 'Test subject', 'test-template');
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