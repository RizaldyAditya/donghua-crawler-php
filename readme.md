# Donghua Streaming Web Crawler

## Overview

This project is a specialized web crawler designed to gather information about Donghua (Chinese animation/动画) from various streaming websites. Donghua has been gaining popularity globally, offering unique storytelling and animation styles different from Japanese anime or Western animation.

The crawler automatically scans specified streaming websites to collect the latest video URLs, episode information, and other metadata about Donghua series. This data is then organized and updated into a private Google spreadsheet, making it easier for fans to track and access their favorite Donghua content.

## Features

- Automated crawling of multiple Donghua streaming websites
- Extraction of video URLs, episode numbers, and series information
- Automatic updates to a private Google spreadsheet
- Support for multiple popular Donghua streaming platforms
- Error handling and logging for reliable operation

## Installation

### Prerequisites

- PHP 8.0 or higher
- Composer
- Valid Google Sheets API credentials (for spreadsheet updates)

### Setup

1. Clone the repository:

```bash
git clone https://github.com/yourusername/donghua-crawler-php.git
cd donghua-crawler-php
```

1. Install dependencies:

```bash
composer install
```

## Usage

The crawler can be run using PHP's command line interface. Here are some example commands:

```bash
# Crawl all supported websites
php index.php

# Crawl specific website
php index.php web=animexin
php index.php web=donghuastream
```

## Required Libraries

The project relies on a small set of Composer packages (installed into `vendor/`) to perform crawling, headless browser control and Google Sheets updates. The main packages installed in this repository are:

- `crwlr/crawler` (v3.5+) — Main crawling and scraping engine used to parse pages and extract metadata.
- `chrome-php/chrome` (v1.14+) — Control Headless Chrome/Chromium when JavaScript rendering is required.
- `guzzlehttp/guzzle` (v7.x) — HTTP client used by many libraries and the Google client.
- `google/apiclient` (v2.x) — Google API Client (used to update Google Sheets).
- `google/apiclient-services` — Google API service definitions (Sheets, Drive, etc.).
- `google/auth` — Google authentication helpers (service accounts, OAuth2).
- `firebase/php-jwt` — JWT encoding/decoding used by Google auth workflows.
- `monolog/monolog` — Logging support.
- `phpseclib/phpseclib` — Crypto helpers used by some auth flows.

Additionally, the `crwlr/*` meta-packages that `crwlr/crawler` depends on are included and useful:

- `crwlr/html-2-text`
- `crwlr/robots-txt`
- `crwlr/schema-org`
- `crwlr/url`
- `crwlr/utils`

System / PHP requirements:

- PHP 8.1 or higher (the installed packages require PHP >= 8.1).
- Recommended PHP extensions: `ext-dom`, `ext-json`, `ext-curl`, `ext-sockets` (for headless chrome websocket), and `ext-mbstring` for robust string handling.

Install everything via Composer with:

```bash
composer install
```

## Configuration

Before running the crawler, make sure to:

1. Set up your Google Sheets API credentials.
1. Configure the target spreadsheet ID in the configuration file.
1. Adjust crawling parameters if needed.

### .env file

You can keep sensitive configuration values in a `.env` file in the project root (this repo does not commit one). Example entries:

```ini
# Google service account JSON path
GOOGLE_SERVICE_ACCOUNT=auth/service_account.json
# Google Spreadsheet ID to update
GOOGLE_SPREADSHEET_ID=your_spreadsheet_id_here
# Optional: logging level (DEBUG, INFO, WARNING, ERROR)
APP_LOG_LEVEL=INFO
```

Load the `.env` values before running the crawler (the project uses environment variables where appropriate). If you use a library like `vlucas/phpdotenv`, run `composer require vlucas/phpdotenv` and then load with code similar to:

```php
(new \Dotenv\Dotenv(__DIR__))->load();
```

Tip: if you don't have a `.env` yet, copy the example file and edit it:

```bash
cp .env.example .env
```

## License

This project is licensed under the MIT License — see the `LICENSE` file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Disclaimer

This tool is for personal use only. Please respect the terms of service of the websites you're crawling and ensure you have permission to access and store the data.
