# Blog.Backend

## psmever's Blog Back-End Source.

## Git Clone.

```bash
git clone https://github.com/psmever/blog.backend.git blog.backend
```

## Git Clone (Single Branch).

```bash
git clone -b develop --single-branch https://github.com/psmever/blog.backend.git
```

## Composer.
```bash
composer install

```

## First Config.
```bash
composer install
cp .env.example .env
```

## Local Develop Server.
```bash
php artisan serve
```

## Local Unit Test.
```bash
php artisan test

composer app-test:watch

./vendor/bin/phpunit-watcher watch --filter=test_waiting_
./vendor/bin/phpunit-watcher watch --filter=ScribbleEditTest

```

## db:seed.
```
php artisan db:seed --class=CodesSeeder --force
```

## Browser.
```bash
http://127.0.0.1:8000 || http://localhost:8000/
```

## Ex Site.
```bash
repository-pattern
https://medium.com/dev-genius/laravel-api-repository-pattern-make-your-code-more-structured-the-simple-guide-5b770da766d7

deploy
https://jeromejaglale.com/doc/php/laravel_github_webhook

deploy - envoy
https://github.com/appkr/envoy


Rest-api-Response-Format
https://github.com/cryptlex/rest-api-response-format

```

## CustomException.
```
throw new \App\Exceptions\CustomException('Something Went Wrong.');

```

## App Manager Script.
```
composer app-clear
composer app-test:watch
composer app-test:clear
```

## Server Deploy.

* Production Deploy Git Main Repositories Push
```
> composer global require laravel/envoy

envoy run deploy:stage

```

## Etc.
```
SlackMessage notifications
https://medium.com/@olayinka.omole/sending-slack-notifications-from-your-laravel-app-1bdb6e4e4127
https://www.lesstif.com/php-and-laravel/sending-slack-notifications-from-laravel-36209045.html
```




## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
