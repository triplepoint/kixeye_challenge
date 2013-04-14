# KIXEYE API Challenge


## Introduction
This project was written from scratch, in the 24 hours between 2013-04-13 09:00:00PDT and 2013-04-14 09:00:00PDT.  As such, there
are some corners cut, notably in the area of deployment infrastructure.  In addition, due to the
audition nature of this project, I've chosen to implement code from scratch in places where I would typically have
used off the shelf 3rd party libraries.

Note the docs/running_todo.md file, which is serving as a project status board.  It's git commit history
should be useful in tracing the process I went through to finish this project.


## Installing
### Install Composer
Composer handles the project dependencies and autoloader (see composer.json for dependency details):

``` bash
cd /wherever/you/cloned/this/repository/kixeye_challenge
php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"
```

### Setup Composer
In a production environment, we want the autoloader as optimized as possible, and the development
dependnecies to not be present.  This command will call composerwith the more efficient classmap
autoloader and without the testing dev dependencies:

``` bash
./composer.phar install --verbose --prefer-dist -o
```

### Database Initialization
Assuming the database is already present, and that there's a root user, this command will
initialize the database:

``` bash
mysql -u root -p < /wherever/you/cloned/this/repository/kixeye_challenge/install/schema.sql
```


## Configuration
Copy the example configuration file to a real configuration file:

``` bash
cd /wherever/you/cloned/this/repository/kixeye_challenge/configuration
cp project_config.example.php project_config.php
```

The configuration array is more or less self explanatory.  Typically, I would gitignore the actual config
file and only distribute the example file without any secure credentials.  Owing to the nature of this project,
however, I've decided to ship the actual config file as well.  I trust there's no actual security
risk here.

## Use
### API Call
The score API has a single route at `/v1/user/score` which only accepts POST requests.
It accepts two POST values: `signed_request` and `score`.  `signed_request` is the Facebook request
passed through from Facebook's API, and `score` is an integer representing the user's new score value.

Successful API calls will respond with a JSON payload describing the inserted user's record, and a status
message denoting a successful POST.

### Populating Test Data
Included with this project is a script for generating test score data, suitable for demonstrating the
reporting tools.

To populate the test data, first truncate the user score data in the database, and then run the generator:

``` bash
cd /wherever/you/cloned/this/repository/kixeye_challenge/bin
./populate_test_data.php
```

### Reporting Tool
TODO


## Testing
### Setup
In order to execute tests, we want to build the Composer dependencies with the dev dependencies included.
We can do this and configure the autoloader with the more convenient namespace map autoloader with:

``` bash
./composer.phar update --verbose --dev --prefer-dist
```

For a local dev, it's necessary to add a row to the host machine's etc/hosts file.
The IP address should be adjusted to match the test environment:

```
192.168.56.11       www.kixeye-challenge.local
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
