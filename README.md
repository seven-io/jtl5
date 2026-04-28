<p align="center">
  <img src="https://www.seven.io/wp-content/uploads/Logo.svg" width="250" alt="seven logo" />
</p>

<h1 align="center">seven SMS for JTL-Shop 5</h1>

<p align="center">
  Bulk and event-based SMS / text-to-speech for <a href="https://www.jtl-software.de/produkte/jtl-shop">JTL-Shop 5</a> via the seven gateway.
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-teal.svg" alt="MIT License" /></a>
  <img src="https://img.shields.io/badge/JTL--Shop-5.x-blue" alt="JTL-Shop 5.x" />
  <img src="https://img.shields.io/badge/PHP-7.4%2B-purple" alt="PHP 7.4+" />
</p>

---

## Features

- **Bulk SMS / Voice** - Reach customers in one go, filtered by *Active*, *Customer Group* or *Language*
- **Event-Based Dispatch** - Auto-fire on:
  - New order
  - Order shipped
  - Order partially shipped
  - Order cancelled
  - Order paid
- **Property Placeholders** - Reference customer fields in templates, e.g. `{{cVorname}}` resolves to the customer's first name

## Prerequisites

- [JTL-Shop](https://www.jtl-software.de/produkte/jtl-shop) 5.x
- A [seven account](https://www.seven.io/) with API key ([How to get your API key](https://help.seven.io/en/developer/where-do-i-find-my-api-key))

## Installation

1. Download the [latest archive](https://github.com/seven-io/jtl5/releases/latest/download/seven_jtl5.zip).
2. In the JTL-Shop admin go to **Plugins > Plugin Manager > Upload** and select the archive.
3. Open the **Deactivated** tab and click **Activate** on *seven*.
4. Open **Installed plugins > seven** and paste your seven API key.

## Usage

### Bulk Messaging

Open the plugin's *Bulk Messaging* tab, compose your SMS or voice message, apply filters and send.

### Event-Based Templates

Open the *Templates* tab, pick the event, write the template body using `{{cVorname}}` and other JTL customer-property placeholders, and save.

## Support

Need help? Feel free to [contact us](https://www.seven.io/en/company/contact/) or [open an issue](https://github.com/seven-io/jtl5/issues).

## License

[MIT](LICENSE)
