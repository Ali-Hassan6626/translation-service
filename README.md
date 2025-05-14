Laravel Translation Management

A simple Laravel-based translation management tool with support for:

Adding new translations
Viewing/searching translations (with pagination)
Exporting translations by locale

---

Setup Instructions

Clone the Repository

```bash
git clone https://github.com/your-org/your-repo.git
cd your-repo

composer install or composer update

cp .env.example .env
php artisan key:generate

DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

php artisan migrate

php artisan tinker
>>> \App\Models\Translation::factory()->count(1000)->create();

php artisan serve
http://localhost:8000

```
