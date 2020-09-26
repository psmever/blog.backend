@servers(['prod' => 'oracle_api', 'stage' => 'local_server'])

@setup
    $prod_root_directory = "/var/www/site/nicepage/blog-api";

    $stage_root_directory = "/var/www/site/blog/stage.backend";
@endsetup

@story('deploy')
    task_prod_deploy
@endstory

@story('deploy:prod', ['on' => 'prod'])
    prod_deploy
@endstory

@story('deploy:stage', ['on' => 'stage'])
    stage_deploy
@endstory

@task('prod_deploy')
    cd {{ $prod_root_directory }}
    pwd

    php artisan down --message="Now deploy" --retry=60

    # update source code
    echo "update source code";
    git pull

    # update PHP dependencies
    echo "update PHP dependencies";
    composer install --no-interaction --no-dev --prefer-dist
        # --no-interaction	Do not ask any interactive question
        # --no-dev		Disables installation of require-dev packages.
        # --prefer-dist		Forces installation from package dist even for dev versions.

    # clear cache
    echo "clear cache";
    php artisan cache:clear

    # clear config cache
    echo "clear config cache";
    php artisan config:clear

    # cache config
    echo "cache config";
    php artisan config:cache

    # restart queues
    echo "restart queues";
    php artisan -v queue:restart

    # update database
    echo "update database";
    php artisan migrate --force
        # --force		Required to run when in production.

    # optimize clear
    echo "optimize clear";
    php artisan optimize:clear

    php artisan up
@endtask

@task('stage_deploy')
    cd {{ $stage_root_directory }}
    pwd

    php artisan down --message="Now deploy" --retry=60

    # update source code
    echo "update source code";
    git pull

    # update PHP dependencies
    echo "update PHP dependencies";
    composer install --no-interaction --no-dev --prefer-dist
        # --no-interaction	Do not ask any interactive question
        # --no-dev		Disables installation of require-dev packages.
        # --prefer-dist		Forces installation from package dist even for dev versions.

    # clear cache
    echo "clear cache";
    php artisan cache:clear

    # clear config cache
    echo "clear config cache";
    php artisan config:clear

    # cache config
    echo "cache config";
    php artisan config:cache

    # restart queues
    echo "restart queues";
    php artisan -v queue:restart

    # update database
    echo "update database";
    php artisan migrate --force
        # --force		Required to run when in production.

    # optimize clear
    echo "optimize clear";
    php artisan optimize:clear

    php artisan up
@endtask
