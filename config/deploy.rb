# Application #
#####################################################################################
set :application,     'LaravelKSS'
set :branch,          ENV["branch"] || "master"
set :user,            ENV["user"] || ENV["USER"] || "kss"


# SCM #
#####################################################################################
set :repo_url,        'git@github.com:puncoz/laravel-security-kss-yipl.git'
set :repo_base_url,   :'https://github.com/'
set :repo_diff_path,  :'puncoz/laravel-security-kss-yipl/compare/master...'
set :repo_branch_path,:'puncoz/laravel-security-kss-yipl/tree'
set :repo_commit_path,:'puncoz/laravel-security-kss-yipl/commit'


# Multistage Deployment #
#####################################################################################
set :stages,              %w(dev staging prod)
set :default_stage,       "staging"


# Other Options #
#####################################################################################
set :ssh_options,         { :forward_agent => true }
set :default_run_options, { :pty => true }


# Permissions #
#####################################################################################
set :use_sudo,            false
set :permission_method,   :acl
set :use_set_permissions, true
set :webserver_user,      "www-data"
set :group,               "www-data"
set :keep_releases,       2

# Slack Integration #
#####################################################################################
set :slack_incoming_hook_url,   "https://hooks.slack.com/services/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"

# Server Notification API #
#####################################################################################
set :server_notification_api,   "http://104.154.249.26:8086/api"

# Set current time #
#######################################################################################
require 'date'
set :current_time, DateTime.now
set :current_timestamp, DateTime.now.to_time.to_i

# Rollbar
#######################################################################################
set :rollbar_token, '1c2757871d6149698cb1c63751ecadd0'
set :rollbar_env, Proc.new { fetch :stage }
set :rollbar_role, Proc.new { :app }


# Setup Tasks #
#######################################################################################
namespace :setup do
    desc "Create shared folders"
    task :create_storage_folder do
        on roles(:all) do
            execute "mkdir -p #{shared_path}/storage"
            execute "mkdir -p #{shared_path}/storage/app"
            execute "mkdir -p #{shared_path}/storage/framework"
            execute "mkdir -p #{shared_path}/storage/framework/cache"
            execute "mkdir -p #{shared_path}/storage/framework/sessions"
            execute "mkdir -p #{shared_path}/storage/framework/views"
            execute "mkdir -p #{shared_path}/storage/logs"
        end
    end

    desc "Create overlay folders"
    task :create_overlay_folder do
        on roles(:all) do
            execute "mkdir -p #{fetch(:overlay_path)}"
        end
    end

    desc "Set up project"
    task :init do
        on roles(:all) do
            invoke "setup:create_storage_folder"
            invoke "setup:create_overlay_folder"
        end
    end
end


# DevOps Tasks #
#######################################################################################
namespace :devops do
    desc "Run Laravel Artisan migrate task."
    task :migrate do
        on roles(:app) do
            within release_path do
                execute :php, "artisan migrate --force"
            end
        end
    end

    desc "Run Laravel Artisan migrate:fresh task."
    task :migrate_fresh do
        on roles(:app) do
            within release_path do
                execute :php, "artisan migrate:fresh --force"
            end
        end
    end

    desc "Run Laravel Artisan seed task."
    task :seed do
        on roles(:app) do
            within release_path do
            execute :php, "artisan db:seed --force"
            end
        end
    end

    desc "Optimize Laravel Class Loader"
    task :optimize do
        on roles(:app) do
            within release_path do
                execute :php, "artisan clear-compiled"
                execute :php, "artisan optimize"
            end
        end
    end

    desc "Laravel Passport install"
        task :passport do
            on roles(:app) do
                within release_path do
                    execute :php, "artisan passport:install --force"
                end
            end
        end

    desc 'Reload nginx server'
    task :nginx_reload do
        on roles(:all) do
            execute :sudo, :service, "nginx reload"
        end
    end

    desc 'Reload php-fpm'
    task :php_reload do
        on roles(:all) do
            execute :sudo, :service, "#{fetch(:php_fpm)} restart"
        end
    end

    desc "Copy Parameter File(s)"
    task :copy_parameters do
        on roles(:all) do |host|
            %w[ parameters.sed ].each do |f|
                upload! "./config/deploy/parameters/#{fetch(:env)}/" + f , "#{fetch(:overlay_path)}/" + f
            end
        end
    end
end


# Installation Tasks #
#######################################################################################
namespace :installation do
    desc 'Copy vendor directory from last release'
    task :vendor_copy do
        on roles(:web) do
            puts ("--> Copy vendor folder from previous release")
            execute "vendorDir=#{current_path}/vendor; if [ -d $vendorDir ] || [ -h $vendorDir ]; then cp -a $vendorDir #{release_path}/vendor; fi;"
        end
    end

    desc "Running Composer Install"
    task :composer_install do
        on roles(:app) do
            within release_path do
                execute :composer, "install --quiet"
                execute :composer, "dump-autoload -o"
            end
        end
    end

    desc "Running npm Install"
    task :npm_install do
        on roles(:app) do
            within release_path do
                execute :npm, "install"
                execute :npm, "run prod"
            end
        end
    end

    desc "Set environment variables"
    task :set_env_variables do
        on roles(:app) do
              puts ("--> Copying environment configuration file")
              execute "cp #{release_path}/.env.server #{release_path}/.env"
              puts ("--> Setting environment variables")
              execute "sed --in-place -f #{fetch(:overlay_path)}/parameters.sed #{release_path}/.env"
        end
    end

    desc "Symbolic link for shared folders"
    task :create_symlink do
        on roles(:app) do
            within release_path do
                execute "rm -rf #{release_path}/storage"
                execute "rm -rf #{release_path}/public/uploads"
                execute "rm -rf #{release_path}/public/ocds"

                execute "ln -s #{shared_path}/storage/ #{release_path}"
                execute "ln -s #{shared_path}/uploads/ #{release_path}/public"
                execute "ln -s #{shared_path}/ocds/ #{release_path}/public"
            end
        end
    end

    desc "User permission to web group"
    task :user_permission do
        on roles(:all) do
            puts("--> Setting permission to laravel bootstrap/cache and storage directory")
            execute "chgrp -R www-data #{current_path}/storage #{current_path}/bootstrap/cache"
        end
    end

    desc "Create ver.txt"
    task :create_ver_txt do
        on roles(:all) do
            puts ("--> Copying ver.txt file")
            execute "cp #{release_path}/config/deploy/ver.txt.example #{release_path}/public/ver.txt"
            execute "sed --in-place 's|%date%|#{fetch(:current_time)}|g
                        s|%branch%|#{fetch(:branch)}|g
                        s|%revision%|#{fetch(:current_revision)}|g
                        s|%deployed_by%|#{fetch(:user)}|g' #{release_path}/public/ver.txt"
            execute "find #{release_path}/public -type f -name 'ver.txt' -exec chmod 664 {} \\;"
        end
    end

    desc "Generate Swagger API Docs"
    task :create_api_docs do
        on roles(:app) do
            within release_path do
                execute :php, "artisan l5-swagger:generate"
            end
        end
    end

    desc "Copy imports file"
    task :copy_imports_file do
        on roles(:app) do
            within release_path do
                execute :scp, "./storage/imports/ #{fetch(:shared_path)}/storage/imports"
            end
        end
    end
end

# Application Tasks #
#######################################################################################
namespace :application do

    desc "Cache Translation"
    task :cache_translations do
        on roles(:app) do
            within release_path do
                execute :php, "artisan ims:translations:cache"
            end
        end
    end

    desc "Flush Translation"
    task :flush_translations do
        on roles(:app) do
            within release_path do
                execute :php, "artisan ims:translations:flush"
            end
        end
    end

end

# Notification Tasks #
#######################################################################################
namespace :notification do
    desc 'Test Slack Incoming Webhook'
    task :test do
        on roles(:all) do
            execute "curl -s -X POST -H 'Content-type: application/json' --data '{\"text\":\"Hello! from the other side.\\nSee you later!\"}' #{fetch(:slack_incoming_hook_url)}"
        end
    end

    desc 'Notify Slack'
    task :notify do
        on roles(:all) do
            execute "curl -s -X POST -H 'Content-type: application/json' --data '{\"attachments\":[{\"fallback\":\"#{fetch(:notify_message)}\",\"color\":\"#{fetch(:notify_color)}\",\"pretext\":\"\",\"title\":\"#{fetch(:job_title)}\",\"title_link\":\"\",\"text\":\"#{fetch(:notify_message)}\",\"fields\":[{\"title\":\"Server URL\",\"value\":\"#{fetch(:site_url)}\",\"short\":false},{\"title\":\"Server\",\"value\":\"#{fetch(:env).upcase}\",\"short\":true},{\"title\":\"Branch\",\"value\":\"<#{fetch(:repo_base_url)}/#{fetch(:repo_branch_path)}/#{fetch(:branch)}|#{fetch(:branch)}> | <#{fetch(:repo_base_url)}/#{fetch(:repo_diff_path)}#{fetch(:branch)}|View Comparison>\",\"short\":true},{\"title\":\"Deployed By\",\"value\":\"#{fetch(:user)}\",\"short\":true},{\"title\":\"Commit SHA\",\"value\":\"#{fetch(:commit_detail)}\",\"short\":true}],\"image_url\":\"\",\"thumb_url\":\"\",\"footer\":\"Capistrano\",\"footer_icon\":\"https://pbs.twimg.com/profile_images/378800000067686459/5da4e1d78e930197cb7dc002ceafdfda.png\",\"ts\":#{fetch(:current_timestamp)}}]}' #{fetch(:slack_incoming_hook_url)}"
            Rake::Task["notification:notify"].reenable
        end
    end

    desc 'Notification on deployment start'
    task :start do
        on roles(:all) do
            set :job_title, "Deployment Started for #{fetch(:application)}"
            set :notify_message, "Hang on! #{fetch(:user)} is deploying #{fetch(:application)}/#{fetch(:branch)} to #{fetch(:env)}."
            set :notify_color, "#FA9040"
            set :commit_detail, "N/A"
            execute "curl -s -X POST -H 'Content-type: application/json' --data '{\"attachments\":[{\"fallback\":\"#{fetch(:notify_message)}\",\"color\":\"#{fetch(:notify_color)}\",\"pretext\":\"\",\"title\":\"#{fetch(:job_title)}\",\"title_link\":\"\",\"text\":\"#{fetch(:notify_message)}\",\"image_url\":\"\",\"thumb_url\":\"\",\"footer\":\"<http://capistranorb.com|Capistrano>\",\"footer_icon\":\"https://pbs.twimg.com/profile_images/378800000067686459/5da4e1d78e930197cb7dc002ceafdfda.png\",\"ts\":#{fetch(:current_timestamp)}}]}' #{fetch(:slack_incoming_hook_url)}"
        end
    end

    desc 'Notification on deployment completion'
    task :deployed do
        on roles(:all) do
            set :job_title, "Deployment Completed for #{fetch(:application)}"
            set :notify_message, "#{fetch(:user)} finished deploying #{fetch(:application)}/#{fetch(:branch)} to #{fetch(:env)}."
            set :notify_color, "#36A64F"
            set :commit_detail, "<#{fetch(:repo_base_url)}/#{fetch(:repo_commit_path)}/#{fetch(:current_revision)}|#{fetch(:current_revision)}>"
            invoke "notification:notify"
        end
    end
    desc 'Notification on deployment completion to server'
    task :to_server do
        on roles(:all) do
            execute "curl -i -H 'Content-type: application/json' -X POST -d '{\"site_url\":\"#{fetch(:site_url)}\",\"current_time\":\"#{fetch(:current_time)}\",\"branch\":\"#{fetch(:branch)}\",\"revision\":\"#{fetch(:current_revision)}\",\"deployed_by\":\"#{fetch(:user)}\"}' #{fetch(:server_notification_api)}"
        end
    end

    desc 'Notification on deployment failed'
    task :deploy_failed do
        on roles(:all) do
            set :job_title, "Deployment Failed for #{fetch(:env)}"
            set :notify_message, "Error occurred while deploying branch by #{fetch(:user)}. Check capistrano.log file."
            set :notify_color, "#FF0000"
            set :commit_detail, "<#{fetch(:repo_base_url)}/#{fetch(:repo_commit_path)}/#{fetch(:current_revision)}|#{fetch(:current_revision)}>"
            invoke "notification:notify"
        end
    end
end


# Tasks Execution #
#######################################################################################

desc "Setup Initialize"
task :setup_init do
    invoke "setup:init"
    invoke "devops:copy_parameters"
end


desc "Update Environment File"
task :update_env do
    invoke "devops:copy_parameters"
    invoke "installation:set_env_variables"
end

desc "Flush and Cache Translation"
task :flush_cache_translations do
    invoke "application:flush_translations"
    invoke "application:cache_translations"
end


desc "Deploy Application"
namespace :deploy do
    after :started, "notification:start"
    after :updated, "installation:vendor_copy"
    after :updated, "installation:composer_install"
    after :updated, "installation:set_env_variables"
    after :published, "installation:create_symlink"
    after :finished, "installation:create_ver_txt"
    after :finished, "devops:migrate"
    after :finished, "notification:deployed"
    after :failed, "notification:deploy_failed"
end

after "deploy", "devops:nginx_reload"
after "deploy", "devops:php_reload"
