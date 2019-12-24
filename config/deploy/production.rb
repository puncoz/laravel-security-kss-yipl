server '10.56.188.206',
user: 'laravel_kss',
roles: %w{web app},
port:22

# Directory to deploy
# ===================
set :env, 'production'
set :app_debug, 'false'
set :deploy_to, '/home/laravel_kss/web'
set :shared_path, '/home/laravel_kss/web/shared'
set :overlay_path, '/home/laravel_kss/web/overlay'
set :tmp_dir, '/home/laravel_kss/web/tmp'
set :site_url, 'http://10.56.188.206'
set :php_fpm, 'php7.2-fpm'
