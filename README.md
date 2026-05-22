![Ranking System](public/sys-logo.png)

# Setup

Step 1: Run the following commands in order.

```bash
    composer install;
    npm install
    npm run build
```

Step 2: Create a file and name it `.env`. Copy the contents of the `env.example` and paste it there.

Step 3: Run the following commands in order.

```bash
    php artisan key:generate;
    php artisan migrate
    php artisan shield:generate
```
