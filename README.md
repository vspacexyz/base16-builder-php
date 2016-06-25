# Base16 Builder PHP
A PHP implementation of a base16 builder that follows the conventions found at: http://chriskempson.com/projects/base16/
Requires PHP 5.3 or greater

## Installation
    git clone https://github.com/chriskempson/base16-builder-php
    composer install

## Usage
    php base16 update
Updates all scheme and template repositories as defined in `schemes.yaml` and `templates.yaml`.

    php base16
Build all templates using all schemes