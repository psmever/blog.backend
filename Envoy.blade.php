@servers(['prod' => 'local_server'])

@setup
    $prod_root_directory = "/var/www/site/blog/prod.backend";

    $stage_root_directory = "/var/www/site/blog/stage.backend";
@endsetup

@story('prod_deploy')
    prod_maintenance_mode
    run_task_prod_deploy
    stop_prod_maintenance_mode
@endstory

@story('stage_deploy')
    stage_maintenance_mode
    run_task_prod_deploy
    stop_stage_maintenance_mode
@endstory


@task('prod_maintenance_mode')
    cd {{ $prod_root_directory }}
    php artisan down --message="Now deploy" --retry=60
@endtask

@task('run_task_prod_deploy')
    cd {{ $prod_root_directory }}

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

@endtask

@task('stop_prod_maintenance_mode')
    cd {{ $prod_root_directory }}
    php artisan up
@endtask


@task('stage_maintenance_mode')
    cd {{ $stage_root_directory }}
    php artisan down --message="Now deploy" --retry=60
@endtask

@task('run_task_prod_deploy')
    cd {{ $stage_root_directory }}

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

@endtask

@task('stop_stage_maintenance_mode')
    cd {{ $stage_root_directory }}
    php artisan up
@endtask
