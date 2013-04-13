# KIXEYE API Challenge


## Introduction
Note the docs/running_todo.md file, which is serving as a project status board.


## Installing
### Install Composer
Composer handles the project dependencies and autoloader (see composer.json for dependency details):

``` bash
cd /wherever/you/cloned/this/repository/kixeye_challenge
php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"
```

### Setup Composer
Import the production (non-dev) dependencies, and configure the autoloader with the more efficient classmap autoloader:

``` bash
./composer.phar install --verbose --prefer-dist -o
```

### Database Initialization
TODO


## Configuration
Copy the example configuration file to a real configuration file:

``` bash
cd /wherever/you/cloned/this/repository/kixeye_challenge/configuration
cp configuration-example.php configuration.php
```

The configuration array is more or less self explanatory.

## Use
### API Call
TODO

### Reporting Tool
TODO


## Testing
### Setup
Import all dependencies (dev included), and configure the autoloader with the more convenient namespace map autoloader:

``` bash
./composer.phar install --verbose --dev --prefer-dist
```

### Automated Tests
Run the unit test suite:

``` bash
./vendor/bin/phpunit -c ./tests
```

### Coding Standards
Run the Php CodeSniffer coding standards rule checks:

``` bash
./vendor/bin/phpcs --encoding=utf-8 --extensions=php --standard=./tests/phpcs.xml -nsp ./
```
