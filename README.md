# deploy
deploy.sh is a Bash script that automatically performs the necessary steps to run a Laravel project after pulling the latest changes from Git.


## 🚀 Usage Instructions

1. **Copy route file contents**

   Copy the contents of the provided `web.php` file into your Laravel project's `routes/web.php`.

2. **Copy custom Artisan commands**

   Copy all files inside the `commands-laravel` folder to your Laravel project's `app/Console/Commands` directory.

3. **Copy deployment and process manager config**

   Copy both `deploy-git` and `supervisor` folders into the **root directory** of your Laravel project.


4. **Run deployment manually**

   To manually deploy the project, run the following Artisan command:

```bash
php artisan deploy:prod


