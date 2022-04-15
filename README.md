# Experimental Hyde Realtime Server

## ⚠ Important! This feature is experimental at best and is in no way intended to be used in production.
Though, why you would want a dynamic web server to serve static HTML I don't know anyway.

So again, this is just a local testing tool and comes with no warranty in any way whatsoever.

## Installation

## Install with Composer

```
composer require hyde/realtime-compiler
```

## Manual / Git install

Clone the contents of the repo into `<hyde-project>/extensions/realtime-compiler`.

## Usage

### Running through Composer
> Currently running the bin file through the composer bin directory will not work as it is not compatible with the built in web server. See https://githubhot.com/repo/composer/composer/issues/10533 for more information.

Instead run the following command:

```bash
php -S localhost:80 ./vendor/hyde/realtime-compiler/server.php
```


### Running from the extension path
> Note, the paths in this section are relative to the package root and assuming the source is in the extensions/realtime-compiler directory.

To start a preconfigured server with the default settings, run:

Unix:

```bash
$ bash ./bin/serve.sh
```

Windows:
```powershell
PS: .\bin\serve.bat
```

Or if you want to start the server with a custom port:

```bash
php -S localhost:80 server.php
```