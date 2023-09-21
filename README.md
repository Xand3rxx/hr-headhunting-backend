<p align="center"><a href="https://hrheadhunting.com/" target="_blank"><img alt="HR Headhunting Logo" src="https://hrheadhunting.com/Content/images/Logo_hit_50x50.png" /></a></p>

## About HR Headhunting

HR Headhunting is an online career portal deployed to connect real people to verified jobs in Nigeria.

Knowing the enormous challenge faced by recruiters in connecting top talents to jobs, we thought to make the hiring process as simple as possible for recruiters. Also, candidates have a platform to do a self-evaluation of their resumes at no charge and get possible suggestions to help them make improvements.

Artisans are not left out of our offerings. Demand is high for artisans in Nigeria today. We just want to make manpower availability less demanding.

Our mission is presently a work-in-progress. We are making a conscious effort to get there. We will!

## HR Headhunting Setup

1. CD into the application root directory with your command prompt/terminal/git bash.
2. Run `cp .env.example .env`.
3. Inside `.env` file, setup database, mail and other configurations.
4. Run `composer install`.
5. Run `php artisan key:generate` command.
6. Run `php artisan serve` command.
7. Define your routes in the `routes/web.php` or `routes/api.php` files.
8. To run a single migration `php artisan migrate --path=/database/migrations/my_migration.php`.
9.  To run single seeder `php artisan db:seed --class=UserSeeder`.
10. To generate the passport keys, run `php artisan passport:keys`.
11. Creating a Personal Access Client by running `php artisan passport:client --personal` command.
12. For the multiple mail options, run the this command `composer require mailgun/mailgun-php sendgrid/sendgrid symfony/mailgun-mailer symfony/http-client` to download the dependent packages.
13. Run `composer dump-autoload` to generate new optimized autoload files.
<!-- 14. Set providers constants in `.env` and `config/constants.php` files. -->
