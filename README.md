<p align="center">
  <img src="https://www.seven.io/wp-content/uploads/Logo.svg" width="250" alt="seven logo" />
</p>

<h1 align="center">seven SMS for Mautic</h1>

<p align="center">
  Plug seven into <a href="https://www.mautic.org/">Mautic</a> as the SMS transport for marketing automation campaigns.
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-teal.svg" alt="MIT License" /></a>
  <img src="https://img.shields.io/badge/Mautic-4.x%20|%205.x-blue" alt="Mautic 4.x | 5.x" />
  <img src="https://img.shields.io/badge/PHP-7.4%2B-purple" alt="PHP 7.4+" />
  <a href="https://packagist.org/packages/seven.io/mautic"><img src="https://img.shields.io/packagist/v/seven.io/mautic" alt="Packagist" /></a>
</p>

---

## Features

- **SMS Transport** - Drop-in replacement for the default Mautic SMS transport
- **Campaign Integration** - Use SMS in any Mautic campaign action
- **Composer-First Install** - Standard Mautic plugin install via Composer

## Prerequisites

- A Composer-based [Mautic](https://www.mautic.org/) installation
- A [seven account](https://www.seven.io/) with API key ([How to get your API key](https://help.seven.io/en/developer/where-do-i-find-my-api-key))

## Installation

```bash
cd /path/to/mautic/root
composer require seven.io/mautic
php bin/console mautic:plugins:reload
```

In Mautic, go to **Plugins > Seven** and paste your seven API key.

## Support

Need help? Feel free to [contact us](https://www.seven.io/en/company/contact/) or [open an issue](https://github.com/seven-io/mautic/issues).

## License

[MIT](LICENSE)
